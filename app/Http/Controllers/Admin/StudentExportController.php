<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Courses;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\DbHelper;
use App\Models\Session;
class StudentExportController extends Controller
{
    /**
     * Build filtered student query (shared by both export types)
     */
    private function buildQuery(Request $request)
    {
        $user  = Auth::user();
        $query = Student::query()
            ->whereHas('StudentCourse', function ($q) use ($user) {
                $q->where('session_id', $user->active_session_id);
            })
            ->with(['StudentCourse' => function ($q) use ($user) {
                $q->where('session_id', $user->active_session_id);
            }, 'course_details', 'session'])
            ->orderByRaw(DbHelper::rollNoOrder());

        $courseId = $request->input('course_id', $request->input('course'));
        if ($courseId) {
            $query->where('course', (string) $courseId);
        }

        if ($request->filled('batch')) {
            $query->where('batch', $request->batch);
        }

        if ($request->filled('semester')) {
            $semester = (int) $request->semester;
            $query->whereHas('StudentCourse', function ($q) use ($user, $semester) {
                $q->where('session_id', $user->active_session_id)
                  ->where('semaster', $semester);
            });
        }

        if ($request->filled('subject_id')) {
            // subjects stored as JSON array of IDs — match any common serialization
            $sid = $request->subject_id;
            $subCol = DbHelper::asText('subjects');
            $query->where(function ($q) use ($sid, $subCol) {
                $q->whereRaw("{$subCol} LIKE ?", ['%"'.$sid.'"%'])
                  ->orWhereRaw("{$subCol} LIKE ?", ['%,'.$sid.',%'])
                  ->orWhereRaw("{$subCol} LIKE ?", ['%['.$sid.',%'])
                  ->orWhereRaw("{$subCol} LIKE ?", ['%,'.$sid.']%'])
                  ->orWhereRaw("{$subCol} LIKE ?", ['%['.$sid.']%']);
            });
        }

        return $query;
    }

    /**
     * GET /admin/students/export/excel
     * Download CSV (Excel-compatible) with applied filters
     */
  public function excel(Request $request)
{
    $students = $this->buildQuery($request)->get();
    $user = auth()->user();

    $filename = 'students_export_' . now()->format('Ymd_His') . '.csv';

    $headers = [
        'Content-Type'        => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function () use ($students, $user) {
        $handle = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fputs($handle, "\xEF\xBB\xBF");

        // --- OPTIMIZATION: Eager load subjects into memory ---
        // 1. Extract all unique subject IDs from the student collection
        $allSubjectIds = $students->pluck('subjects')
            ->flatten()
            ->filter()
            ->unique()
            ->toArray();

        // 2. Query all required subjects in a single database execution
        $subjectsCached = Subject::whereIn('id', $allSubjectIds)
            ->with('course') // Optional: Eager load course if needed
            ->get()
            ->keyBy('id'); // Key the collection by 'id' for $O(1)$ fast lookups
        // -----------------------------------------------------

        // Header Row matching the Admission Form
        fputcsv($handle, [
            'Registration Number', 'Roll Number', 'Student ID', 
            'First Name', 'Last Name', 'Gender', 'Date of Birth', 
            'Phone Number', 'Email Address', 'Father\'s Name', 
            'Mother\'s Name', 'Category', 'Caste', 'Permanent Address', 
            'Correspondence Address', 'Course Name', 'Batch', 'Session', 
            'Admission Date', 'Status', 'Current Year', 'Current Semester', 
            'Subjects Enrolled'
        ]);

        foreach ($students as $s) {
            // Filter the cached subjects in memory based on the student's IDs and course
            $studentSubjectIds = (array) ($s->subjects ?? []);
            
            $subjectsFormatted = [];
            $index = 1;

            foreach ($studentSubjectIds as $id) {
                if (isset($subjectsCached[$id])) {
                    $subject = $subjectsCached[$id];
                    
                    // Match your criteria: Ensure the subject's course ID matches the student's course ID
                    if ($subject->course_id == $s->course) { 
                        $subjectsFormatted[] = $index . '. ' . $subject->name;
                        $index++;
                    }
                }
            }

            $subjectsString = empty($subjectsFormatted) ? 'No subjects enrolled' : implode(', ', $subjectsFormatted);

            fputcsv($handle, [
                $s->reg_no,
                $s->roll_no,
                $s->student_id,
                $s->first_name,
                $s->last_name ?? 'N/A',
                $s->gender,
                $s->date_of_birth instanceof \Carbon\Carbon ? $s->date_of_birth->format('d-m-Y') : $s->date_of_birth,
                $s->phone_number,
                $s->email,
                $s->father_name,
                $s->mother_name,
                $s->category ?? 'N/A',
                $s->caste ?? 'N/A',
                $s->permanent_address ?? 'N/A',
                $s->correspondence_address ?? 'N/A',
                $s->course_details->name ?? '', 
                $s->batch,
                $s->session->session_name ?? '', 
                $s->admission_date instanceof \Carbon\Carbon ? $s->admission_date->format('d-m-Y') : \Carbon\Carbon::parse($s->admission_date)->format('d-m-Y'),
                $s->status,
                $s->current_year,
                $s->current_semester,
                $subjectsString
            ]);
        }
        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}

    /**
     * GET /admin/students/export/pdf
     * Download PDF with applied filters
     */
     public function pdf(Request $request)
{
    $students = $this->buildQuery($request)->get();

    $filters = [
        'course'  => $request->filled('course_id') || $request->filled('course')
            ? (Courses::find($request->input('course_id', $request->input('course')))->name ?? 'All')
            : 'All',
        'batch'   => $request->batch    ?? 'All',
        'semester' => $request->semester ?? 'All',
        'subject' => $request->filled('subject_id')
            ? (Subject::find($request->subject_id)->name ?? 'All')
            : 'All',
    ];

    $sessionName = optional(Session::find(Auth::user()->active_session_id))->session_name ?? '';

    // --- OPTIMIZATION: Cache subjects into memory for fast lookup ---
    $allSubjectIds = $students->pluck('subjects')->flatten()->filter()->unique()->toArray();
    $subjectsCached = Subject::whereIn('id', $allSubjectIds)->get()->keyBy('id');

    // Attach processed subjects string to each student object dynamically
    foreach ($students as $s) {
        $studentSubjectIds = (array) ($s->subjects ?? []);
        $subjectsFormatted = [];
        $index = 1;

        foreach ($studentSubjectIds as $id) {
            if (isset($subjectsCached[$id])) {
                $subject = $subjectsCached[$id];
                if ($subject->course_id == $s->course) { 
                    $subjectsFormatted[] = $index . '. ' . $subject->name;
                    $index++;
                }
            }
        }
        $s->formatted_subjects = empty($subjectsFormatted) ? 'No subjects enrolled' : implode(', ', $subjectsFormatted);
    }

    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $pdf = Pdf::loadView('admin.students_pdf', compact('students', 'filters', 'sessionName'))
              ->setPaper('a4', 'landscape');

    return $pdf->download('students_all_fields_' . now()->format('Ymd_His') . '.pdf');
}
}
