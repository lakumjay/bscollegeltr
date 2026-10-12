<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Courses;
use App\Models\Exam;
use App\Models\GradingScale;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamManagementController extends Controller
{
    // Marks/Grades Entry
    public function marksEntry(Request $request)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $courses = Courses::get();
        $exams = collect();
        $students = collect();
        $subjects = collect();
        $marks = collect();
        $selectedCourseId = $request->query('course_id');
        $selectedExamId = $request->query('exam_id');

        if ($selectedCourseId) {
            $exams = Exam::where('course_id', $selectedCourseId)
                ->where('session_id', $currentSession)
                
                ->get();
            $subjects = Subject::where('course_id', $selectedCourseId)
                // 
                ->get();
            $students = Student::where('course', $selectedCourseId)
                
                ->whereIn('id', function ($query) use ($currentSession, $user) {
                    $query->select('student_id')
                        ->from('student_courses')
                        ->where('session_id', $currentSession)
                        ;
                })->get();

            if ($selectedExamId) {
                $marks = Mark::where('exam_id', $selectedExamId)
                    ->where('session_id', $currentSession)
                    
                    ->get()->keyBy(function ($mark) {
                        return $mark->student_id . '_' . $mark->subject_id;
                    });
            }
        }

        return view('admin.marks_entry', compact('courses', 'exams', 'students', 'subjects', 'marks', 'selectedCourseId', 'selectedExamId'));
    }

    public function saveMarks(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|array',
            'marks.*.*' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $exam = Exam::findOrFail($request->exam_id);
        $gradingScale = $exam->gradingScale->scale;

        foreach ($request->marks as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $marks) {
                if ($marks !== null) {
                    $grade = null;
                    foreach ($gradingScale as $scale) {
                        if ($marks >= $scale['min'] && $marks <= $scale['max']) {
                            $grade = $scale['grade'];
                            break;
                        }
                    }

                    Mark::updateOrCreate(
                        [
                            
                            'session_id' => $currentSession,
                            'exam_id' => $request->exam_id,
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                        ],
                        [
                            'user_id' => $user->id,
                            'marks' => $marks,
                            'grade' => $grade,
                        ]
                    );
                }
            }
        }

        return redirect()->route('exam_management.marks_entry', ['course_id' => $request->course_id, 'exam_id' => $request->exam_id])
            ->with('success', 'Marks saved successfully!');
    }

    // Grading Scale Management
    public function gradingScale()
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $gradingScales = GradingScale::where('session_id', $currentSession)
            ->get();

        return view('admin.grading_scale', compact('gradingScales'));
    }

    public function saveGradingScale(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scale' => 'required|array',
            'scale.*.min' => 'required|numeric|min:0',
            'scale.*.max' => 'required|numeric|min:0',
            'scale.*.grade' => 'required|string|max:10',
        ]);

        $user = Auth::user();
        $currentSession = $user->active_session_id;

        GradingScale::create([
            'user_id' => $user->id,
            'session_id' => $currentSession,
            'name' => $request->name,
            'scale' => $request->scale,
        ]);

        return redirect()->route('exam_management.grading_scale')
            ->with('success', 'Grading scale created successfully!');
    }

    // Exam Management
    public function exams(Request $request)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $courses = Courses::get();
        $gradingScales = GradingScale::where('session_id', $currentSession)
            ->get();
        $exams = Exam::where('session_id', $currentSession)
            ->with('course', 'gradingScale')
            ->get();

        return view('admin.exams', compact('courses', 'gradingScales', 'exams'));
    }

    public function saveExam(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'grading_scale_id' => 'required|exists:grading_scales,id',
            'max_marks' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $currentSession = $user->active_session_id;

        Exam::create([
            'user_id' => $user->id,
            'session_id' => $currentSession,
            'course_id' => $request->course_id,
            'name' => $request->name,
            'grading_scale_id' => $request->grading_scale_id,
            'max_marks' => $request->max_marks,
        ]);

        return redirect()->route('exam_management.exams')
            ->with('success', 'Exam created successfully!');
    }

    // Report Cards Page
    public function reportCards(Request $request)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $courses = Courses::get();
        $exams = collect();
        $students = collect();
        $selectedCourseId = $request->query('course_id');
        $selectedExamId = $request->query('exam_id');

        if ($selectedCourseId) {
            $exams = Exam::where('course_id', $selectedCourseId)
                ->where('session_id', $currentSession)
                
                ->get();
            $students = Student::where('course', $selectedCourseId)
                
                ->whereIn('id', function ($query) use ($currentSession, $user) {
                    $query->select('student_id')
                        ->from('student_courses')
                        ->where('session_id', $currentSession)
                        ;
                })->get();
        }

        return view('admin.report_cards', compact('courses', 'exams', 'students', 'selectedCourseId', 'selectedExamId'));
    }

    // Report Card Preview
    public function reportCard($studentId, $examId)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $student = Student::findOrFail($studentId);
        $exam = Exam::findOrFail($examId);
        $marks = Mark::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->where('session_id', $currentSession)
            
            ->where('marks','!=',0)
            ->with('subject')
            ->get();

        $totalMarks = $marks->sum('marks');
        $maxTotalMarks = $exam->max_marks * $marks->count();
        $percentage = $maxTotalMarks ? ($totalMarks / $maxTotalMarks) * 100 : 0;
        $grade = null;
        foreach ($exam->gradingScale->scale as $scale) {
            if ($percentage >= $scale['min'] && $percentage <= $scale['max']) {
                $grade = $scale['grade'];
                break;
            }
        }

        return view('admin.report_card', compact('student', 'exam', 'marks', 'totalMarks', 'maxTotalMarks', 'percentage', 'grade'));
    }

    // Report Card Print
    public function reportCardPrint($studentId, $examId)
    {
        $user = Auth::user();
        $currentSession = $user->active_session_id;
        $student = Student::findOrFail($studentId);
        $exam = Exam::findOrFail($examId);
        $marks = Mark::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->where('session_id', $currentSession)
            
                        ->where('marks','!=',0)

            ->with('subject')
            ->get();

        $totalMarks = $marks->sum('marks');
        $maxTotalMarks = $exam->max_marks * $marks->count();
        $percentage = $maxTotalMarks ? ($totalMarks / $maxTotalMarks) * 100 : 0;
        $grade = null;
        foreach ($exam->gradingScale->scale as $scale) {
            if ($percentage >= $scale['min'] && $percentage <= $scale['max']) {
                $grade = $scale['grade'];
                break;
            }
        }

        return view('admin.report_card_print', compact('student', 'exam', 'marks', 'totalMarks', 'maxTotalMarks', 'percentage', 'grade'));
    }

    // Result Publishing
    public function resultPublic(Request $request)
    {
        $student = null;
        $exam = null;
        $marks = collect();
        $totalMarks = 0;
        $maxTotalMarks = 0;
        $percentage = 0;
        $grade = null;

        if ($request->has('student_id') && $request->has('exam_id')) {
            $student = Student::where('id', $request->student_id)
                ->orWhere('roll_no', $request->student_id)
                ->first();

            if ($student) {
                $exam = Exam::find($request->exam_id);
                if ($exam) {
                    $marks = Mark::where('exam_id', $exam->id)
                        ->where('student_id', $student->id)
                        ->with('subject')
                        ->get();

                    $totalMarks = $marks->sum('marks');
                    $maxTotalMarks = $exam->max_marks * $marks->count();
                    $percentage = $maxTotalMarks ? ($totalMarks / $maxTotalMarks) * 100 : 0;
                    foreach ($exam->gradingScale->scale as $scale) {
                        if ($percentage >= $scale['min'] && $percentage <= $scale['max']) {
                            $grade = $scale['grade'];
                            break;
                        }
                    }
                }
            }
        }

        $exams = Exam::all();
        return view('public.result', compact('student', 'exam', 'marks', 'totalMarks', 'maxTotalMarks', 'percentage', 'grade', 'exams'));
    }
}