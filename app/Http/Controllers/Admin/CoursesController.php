<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\SpecialCategory;
use App\Models\Profile;
use App\Models\Session;
use App\Models\StudentCertificate;
use App\Helpers\DbHelper;
use App\Services\ClcCertificateNumberGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

use Auth;

class CoursesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $courses = Courses::with('subjects')
            ->orderBy('sr')
            ->orderBy('name')
            ->get();
        return view('admin.courses', compact('courses'));
    }
    
 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $nextSerial = ((int) Courses::max('sr')) + 1;

        Courses::create([
            'sr' => $nextSerial,
            'name' => trim($request->name),
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->to(url('admin/course'))->with('success', 'Course created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $course = Courses::where('id', $id)->firstOrFail();

        $course->update([
            'name' => trim($request->name),
        ]);

        return redirect()->to(url('admin/course'))->with('success', 'Course updated successfully.');
    }

    public function destroy($id)
    {
        $course = Courses::where('id', $id)->firstOrFail();
        $course->delete();

        return redirect()->to(url('admin/course'))->with('success', 'Course deleted successfully.');
    }

    public function storeSubject(Request $request, $courseId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $course = Courses::where('id', $courseId)->firstOrFail();

        $subject = Subject::create([
            'name' => trim($request->name),
            'course_id' => $course->id,
        ]);

        if ($request->input('response_format') === 'json' || $request->ajax() || $request->expectsJson()) {
            $subjects = Subject::where('course_id', $courseId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Subject added successfully.',
                'subjects' => $subjects,
            ]);
        }

        return redirect()->to(url('admin/course'))->with('success', 'Subject added successfully.');
    }

    public function updateSubject(Request $request, $courseId, $subjectId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $course = Courses::where('id', $courseId)->firstOrFail();
        $subject = Subject::where('id', $subjectId)->where('course_id', $courseId)->firstOrFail();

        $subject->update([
            'name' => trim($request->name),
        ]);

        if ($request->input('response_format') === 'json' || $request->ajax() || $request->expectsJson()) {
            $subjects = Subject::where('course_id', $courseId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Subject updated successfully.',
                'subjects' => $subjects,
            ], 200);
        }

        return redirect()->to(url('admin/course'))->with('success', 'Subject updated successfully.');
    }

    public function destroySubject($courseId, $subjectId)
    {
        $course = Courses::where('id', $courseId)->firstOrFail();
        $subject = Subject::where('id', $subjectId)->where('course_id', $courseId)->firstOrFail();
        $subject->delete();

        if (request()->input('response_format') === 'json' || request()->ajax() || request()->expectsJson()) {
            $subjects = Subject::where('course_id', $courseId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Subject deleted successfully.',
                'subjects' => $subjects,
            ]);
        }

        return redirect()->to(url('admin/course'))->with('success', 'Subject deleted successfully.');
    }

    /**
     * Display course strength.
     */
    public function studentStrength()
    {
        $user = Auth::user();
    
        $strength = \DB::table('student_courses')
        ->join('students', 'student_courses.student_id', '=', 'students.id')
        ->join('courses', \DB::raw(DbHelper::castInt('students.course')), '=', 'courses.id')
        ->where('student_courses.session_id', $user->active_session_id)
        ->groupBy('students.course', 'courses.name')
        ->selectRaw("
            students.course,
            courses.name as course_name,
            SUM(CASE WHEN students.gender = 'Male'   THEN 1 ELSE 0 END) as boys,
            SUM(CASE WHEN students.gender = 'Female' THEN 1 ELSE 0 END) as girls,
            SUM(CASE WHEN students.gender = 'Other'  THEN 1 ELSE 0 END) as others,
            COUNT(*) as total
        ")
        ->get();
    
    
        return view('admin.strength', compact('strength'));
    }
    

    /**
     * Display students, optionally filtered by course.
     */
    public function students(Request $request)
{
    $certificate_fields=[
        'clc'=>[
            'examination_year'=>'Examination Year',
            'registrtaion_number'=>'Registration No/Enlistment No',
            'division'=>'Division',
            'remarks'=>'General Remarks',
            
        ],
        'character'=>[],
        'bonafied'=>[],
    ];
    $user = Auth::user();
    $activeSessionId = (int) $user->active_session_id;
    // Cast course to string so PostgreSQL doesn't get varchar = bigint mismatch
    $course = (string) $request->query('course');
    $course_name = $course !== '' ? (Courses::whereKey($course)->value('name') ?? 'Unknown') : 'All Courses';

    $studentsQuery = Student::query();

    if ($course !== '') {
        $studentsQuery->where('course', $course);
    }

    if ($activeSessionId) {
        $studentsQuery->whereHas('StudentCourse', function ($q) use ($activeSessionId) {
            $q->where('session_id', $activeSessionId);
        });
    }

    $studentsQuery->with([
        'StudentCourse' => function ($q) use ($activeSessionId) {
            if ($activeSessionId) {
                $q->where('session_id', $activeSessionId);
            }
            $q->with('session:id,session_name');
        },
        'course_details:id,name',
        'certificates',
    ])
    // PostgreSQL: sort roll_no numerically when it contains digits, else alphabetically
    ->orderByRaw(DbHelper::rollNoOrder());

    if (!empty($request->query('search'))) {
        $search = $request->query('search');
        $il = DbHelper::ilike();
        $studentsQuery->where(function ($q) use ($search, $il) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) {$il} ?", ["%{$search}%"])
              ->orWhereRaw("roll_no {$il} ?", ["%{$search}%"]);
        });
    }

    if ($request->filled('batch')) {
        $studentsQuery->where('batch', $request->query('batch'));
    }

    if ($request->filled('semester')) {
        $semester = (int) $request->query('semester');
        $studentsQuery->whereHas('StudentCourse', function ($q) use ($activeSessionId, $semester) {
            $q->where('session_id', $activeSessionId)
              ->where('semaster', $semester);
        });
    }

    if ($request->filled('subject_id')) {
        $subjectId = $request->query('subject_id');
        $subjectsColumn = DbHelper::asText('subjects');
        $studentsQuery->where(function ($q) use ($subjectId, $subjectsColumn) {
            $q->whereRaw("{$subjectsColumn} LIKE ?", ['%"' . $subjectId . '"%'])
                ->orWhereRaw("{$subjectsColumn} LIKE ?", ['%,' . $subjectId . ',%'])
                ->orWhereRaw("{$subjectsColumn} LIKE ?", ['%[' . $subjectId . ',%'])
                ->orWhereRaw("{$subjectsColumn} LIKE ?", ['%,' . $subjectId . ']%'])
                ->orWhereRaw("{$subjectsColumn} LIKE ?", ['%[' . $subjectId . ']%']);
        });
    }

    $students = $studentsQuery->paginate(30)->withQueryString();
    $allCourses = Courses::orderBy('name')->get(['id', 'name']);
    $allBatches = Student::query()->whereNotNull('batch')->where('batch', '!=', '')->distinct()->orderBy('batch')->pluck('batch');
    $allSubjects = Subject::when($course, fn ($query) => $query->where('course_id', $course))->orderBy('name')->get(['id', 'name']);
    $sessions = Session::orderBy('session_name')->get(['id', 'session_name']);
    $profile = Profile::first();

    return view('admin.students', compact(
        'students', 'course', 'course_name', 'certificate_fields',
        'allCourses', 'allBatches', 'allSubjects', 'sessions', 'activeSessionId', 'profile'
    ));
}
public function download_csv(Request $request){
        $course = $request->query('course');
    $user = Auth::user();

    $students = Student::whereHas('StudentCourse', function ($q) use ($user) {
            $q->where('session_id', $user->active_session_id);
        })
        ->where('course', (string) $course)
        ->with(['StudentCourse' => function ($q) use ($user) {
            $q->where('session_id', $user->active_session_id);
        }])
        ->orderByRaw(DbHelper::rollNoOrder())
        ->get();

 $fileName = 'Students.csv';

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
    ];

    $columns = ['ID', 'Student Id','Roll No','First Name', 'Last Name','Gender','DOB','Phone Number','Email','Father Name','Mother Name','Batch','Subjects'];

    $callback = function () use ($columns,$students) {
        $file = fopen('php://output', 'w');

        // CSV header
        fputcsv($file, $columns);

        // Data
        // $users = \App\Models\User::select('id','name','email')->get();
        // foreach ($users as $user) {
        //     fputcsv($file, [
        //         $user->id,
        //         $user->name,
        //         $user->email
        //     ]);
        // }
        // $subjects = "";
        foreach($students as $student){
            $subjects = "";
            if($student->subjects){
                                        
                                            $subjectNames = Subject::whereIn('id', $student->subjects)
                                                ->whereHas('course', function ($query) use ($student) {
                                                    $query->where('id', $student->course);
                                                })
                                                ->pluck('name')
                                                ->toArray();
                                      
                                        $subjects=implode(', ', $subjectNames) ?: 'None';
            }else{ 
                $subjects= "none";
            }
           fputcsv($file, [
                $student->id,
                $student->student_id,
                $student->roll_no,
                $student->first_name,
                $student->last_name,
                $student->gender,
                $student->date_of_birth,
                $student->phone_number,
                $student->email,
                $student->father_name,
                $student->mother_name,
                $student->batch,
                $subjects,
                
            ]); 
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);

}
public function certificate_certificate($student_id,$type,Request $request, ClcCertificateNumberGenerator $numberGenerator){
    $student = Student::where('id',$student_id)->firstOrFail();

    if ($type === 'clc') {
        $existingClc = StudentCertificate::where('student_id', $student_id)
            ->where('type', 'clc')
            ->first();
        if ($existingClc) {
            if ($request->has('data') && !empty($request->data)) {
                $existingClc->update(['data' => json_encode($request->data)]);
            }
            return redirect('admin/students/'.$student->id.'/clc?certId='.$existingClc->id);
        }
    }

    $sr = ((int) StudentCertificate::where('type', $type)->max('sr')) + 1;

    if ($type === 'clc' && $sr > 9999) {
        throw ValidationException::withMessages([
            'certificate' => 'The automatic CLC serial number limit of 9999 has been reached.',
        ]);
    }

    $profile = $request->user()->profile ?: Profile::first();
    $autoCertNumber = $profile ? ($profile->auto_cert_number ?? true) : true;
    $manualNumber = $request->filled('manual_certificate_number') ? trim((string)$request->manual_certificate_number) : null;

    $attributes = [
        'student_id'=>$student_id,
        'type'=>$type,
        'sr'=>$sr,
        'data'=>json_encode($request->data),
        'generated_by'=>$request->user()->id,
    ];

    if ($manualNumber !== null && $manualNumber !== '') {
        // Manual patrak / certificate number entered by college staff
        $attributes['certificate_number'] = $manualNumber;
        $cert = StudentCertificate::create($attributes);
    } elseif (! $autoCertNumber) {
        // Auto generation is turned off in profile, but no manual number given
        $tag = $type === 'character' ? 'CHR' : null;
        $attributes['certificate_number'] = $numberGenerator->generate($profile?->lc_prefix, null, $sr, $tag);
        $cert = StudentCertificate::create($attributes);
    } elseif (in_array($type, ['clc', 'character'], true)) {
        $tag = $type === 'character' ? 'CHR' : null;

        // The unique database index protects concurrent certificate requests;
        // retry with another four-digit suffix if two requests ever collide.
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $attributes['certificate_number'] = $numberGenerator->generate($profile?->lc_prefix, null, $sr, $tag);

            try {
                $cert = StudentCertificate::create($attributes);
                break;
            } catch (QueryException $exception) {
                if (! in_array((string) $exception->getCode(), ['23000', '23505'], true)) {
                    throw $exception;
                }
            }
        }

        if (! isset($cert)) {
            throw new \RuntimeException("Unable to reserve a unique {$type} certificate number. Please try again.");
        }
    } else {
        $cert = StudentCertificate::create($attributes);
    }

    return redirect('admin/students/'.$student->id.'/'.$type.'?certId='.$cert->id);
}

    /**
     * Show the form for creating a new student.
     */
    public function createStudent()
    {
        $user = Auth::user();
        $courses = Courses::query()
            ->with(['subjects' => fn ($query) => $query->orderBy('name')->select('id', 'course_id', 'name')])
            ->orderBy('name')
            ->get(['id', 'name']);
        $special_categories = SpecialCategory::get();
        $lastStudent = Student::where('user_id', $user->id)
            ->latest('id')
            ->first(['roll_no', 'student_id']);
        $next_roll_no = $lastStudent ? ((int) $lastStudent->roll_no) + 1 : '';
        $next_student_id = $lastStudent ? ((int) $lastStudent->student_id) + 1 : '';
        $defaultBatch = optional(Session::find($user->active_session_id))->session_name;

        return view('admin.create-student', compact('courses', 'special_categories', 'next_roll_no', 'next_student_id', 'defaultBatch'));
    }

    /**
     * Store a newly created student.
     */
    public function storeStudent(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            
            'reg_no' => 'nullable|string|max:255',
            'roll_no' => 'required|string|max:255',
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'last_name' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
            'phone_number' => ['required', 'regex:/^\\d{10,15}$/'], // 'unique:students,phone_number' commented out to allow shared parent contact numbers
            'email' => 'required|email|max:255|unique:students',
            'father_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'mother_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'course' => 'required|integer|exists:courses,id',
            'batch' => 'required|string|max:255',
            'admission_date' => 'required|date',
            'current_year' => 'required|integer|min:1|max:4',
            'current_semester' => 'required|integer|min:1|max:8',
            'status' => 'required|in:Active,Inactive',
            'subjects' => 'nullable|array',
            'subjects.*' => 'integer|exists:subjects,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_card' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'category' => 'nullable|string|max:255',
            'caste' => 'nullable|string|max:255',
            'category_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'special_category' => 'nullable|integer',
            'student_id' => 'required|string|max:255',
            'permanent_address' => 'required|string',
            'correspondence_address' => 'required|string',
        ]);

        $data = [
            'reg_no' => $request->reg_no,
            'roll_no' => $request->roll_no,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth ? Carbon::parse($request->date_of_birth)->format('Y-m-d') : null,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'course' => $request->course,
            'batch' => $request->batch,
            'admission_date' => $request->admission_date ? Carbon::parse($request->admission_date)->format('Y-m-d') : null,
            'current_year' => $request->current_year,
            'current_semester' => $request->current_semester,
            'status' => $request->status,
            'subjects' => $request->subjects ?? [],
            'session_id' => $user->active_session_id,
            'user_id' => $user->id,
            'category' => $request->category,
            'caste' => $request->caste,
            'special_category'=>$request->special_category,
            'correspondence_address'=>$request->correspondence_address,
            'permanent_address'=>$request->permanent_address,
            'student_id'=>$request->student_id
        ];
 
        // Handle file uploads
        foreach (['photo', 'aadhar_card', 'id_proof', 'category_certificate'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fname = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $fname);
                $data[$field] = 'uploads/' . $fname;
            }
        }

        $student = Student::create($data);
        $studentId = $student->id;

        StudentCourse::create(
            [
                'session_id'=>Auth::user()->active_session_id,
                'user_id'=>Auth::user()->id,
                'student_id'=>$studentId,
                'semaster'=>$request->current_semester


            ]
        );
        return redirect()->to(url('admin/students/strength'))->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student’s details.
     */
    public function studentDetails($id)
    {
        $student = Student::with([
            'course_details:id,name',
            'session:id,session_name',
        ])->findOrFail($id);

        $subjects = Subject::whereIn('id', $student->subjects ?? [])
            ->where('course_id', $student->course)
            ->orderBy('name')
            ->get();

        return view('admin.student-details', compact('student', 'subjects'));
    }

    /**
     * Show the form for editing a student.
     */
    public function editStudent($id)
    {
        $user = Auth::user();
        $student = Student::where('id', $id)
            
            // ->where('session_id', $user->active_session_id)
            ->firstOrFail();
        $courses = Courses::query()
            ->with(['subjects' => fn ($query) => $query->orderBy('name')->select('id', 'course_id', 'name')])
            ->orderBy('name')
            ->get(['id', 'name']);
        $subjects = Subject::whereHas('course', function ($query) use ($user, $student) {
            $query
                ->where('id', $student->course);
        })->get();
        $special_categories=SpecialCategory::get();
        $defaultBatch = optional(Session::find($user->active_session_id))->session_name;

        return view('admin.edit-student', compact('student', 'courses', 'subjects', 'special_categories', 'defaultBatch'));
    }

    /**
     * Update the specified student.
     */
    public function updateStudent(Request $request, $id)
    {
        $user = Auth::user();
        $student = Student::where('id', $id)
            
            // ->where('session_id', $user->active_session_id)
            ->firstOrFail();

        $request->validate([
            'reg_no' => 'nullable|string|max:255|',
            'roll_no' => 'required|string|max:255|',
            // unique:students,roll_no,' . $student->id,
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'last_name' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
            'phone_number' => ['required', 'regex:/^\\d{10,15}$/'],
            'email' => 'required|email|max:255|',//unique:students,email,' . $student->id,
            'father_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'mother_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z]+(?:[ \'-][A-Za-z]+)*$/'],
            'course' => 'required|integer|exists:courses,id',
            'batch' => 'required|string|max:255',
            'admission_date' => 'required|date',
            'current_year' => 'required|integer|min:1|max:4',
            'current_semester' => 'required|integer|min:1|max:8',
            'status' => 'required|in:Active,Inactive',
            'subjects' => 'nullable|array',
            'subjects.*' => 'integer|exists:subjects,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'aadhar_card' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'category' => 'nullable|string|max:255',
            'caste' => 'nullable|string|max:255',
            'category_certificate' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'special_category' => 'nullable|integer',
            'student_id' => 'required|string|max:255',
            'permanent_address' => 'required|string',
            'correspondence_address' => 'required|string',
        ]);

        $data = [
                        'reg_no' => $request->reg_no,

            'roll_no' => $request->roll_no,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth ? Carbon::parse($request->date_of_birth)->format('Y-m-d') : null,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'course' => $request->course,
            'batch' => $request->batch,
            'admission_date' => $request->admission_date ? Carbon::parse($request->admission_date)->format('Y-m-d') : null,
            'current_year' => $request->current_year,
            'current_semester' => $request->current_semester,
            'status' => $request->status,
            'subjects' => $request->subjects ?? [],
            'category' => $request->category,
            'caste' => $request->caste,
            'special_category' => $request->special_category,
            'correspondence_address'=>$request->correspondence_address,
            'permanent_address'=>$request->permanent_address,
                        'student_id'=>$request->student_id

        ];

        // Handle file uploads
        foreach (['photo', 'aadhar_card', 'id_proof', 'category_certificate'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($student->$field && file_exists(public_path($student->$field))) {
                    unlink(public_path($student->$field));
                }
                $file = $request->file($field);
                $fname = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $fname);
                $data[$field] = 'Uploads/' . $fname;
            }
        }

        $student->update($data);

        return redirect()->to(url('admin/students/strength'))->with('success', 'Student updated successfully.');
    }

    /**
     * Delete the specified student.
     */
    public function destroyStudent($id)
    {
        $user = Auth::user();
        $student = Student::where('id', $id)
            
            // ->where('session_id', $user->active_session_id)
            ->firstOrFail();

        // Delete associated files
        foreach (['photo', 'aadhar_card', 'id_proof', 'category_certificate'] as $field) {
            if ($student->$field && file_exists(public_path($student->$field))) {
                unlink(public_path($student->$field));
            }
        }

        $student->delete();

        return redirect()->to(url('admin/students'))->with('success', 'Student deleted successfully.');
    }

    /**
     * Return the subjects assigned to a course for the student admission form.
     */
    public function getSubjects(Request $request)
    {
        $courseId = $request->validate([
            'course' => ['required', 'integer', 'exists:courses,id'],
        ])['course'];

        $subjects = Subject::query()
            ->where('course_id', $courseId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'subjects' => $subjects,
        ]);
    }
    public function clc($id,Request $request){
        $certId=$request->certId;
        $StudentCertificate = StudentCertificate::where('id',$certId)->firstOrFail();
        $student=Student::where('id',$id)->first();
        $profile=Profile::first();
        return view('admin.certificates.clc',compact('student','profile','StudentCertificate'));
    }
    public function character($id,Request $request){
        $student=Student::where('id',$id)->first();
                $certId=$request->certId;

                $StudentCertificate = StudentCertificate::where('id',$certId)->firstOrFail();

        $profile=Profile::first();
        return view('admin.certificates.character',compact('student','profile','StudentCertificate'));
    }
    public function bonafied($id,Request $request){
        $user=Auth::user();
        $student=Student::where('id',$id)->first();
        $profile=Profile::first();
                $certId=$request->certId;

                $StudentCertificate = StudentCertificate::where('id',$certId)->firstOrFail();

        $subjects = Subject::whereIn('id', $student->subjects ?? [])
        ->whereHas('course', function ($query) use ($user, $student) {
            $query
                ->where('id', $student->course);
        })
        ->get();
        return view('admin.certificates.bonafied',compact('student','profile','subjects','StudentCertificate'));
    }
    public function add_session($id, Request $request)
    {
        $data = $request->validate([
            'session'  => 'required|exists:sessions,id',
            'semaster' => 'required|integer|between:1,8',
        ]);

        $student = Student::findOrFail($id);
        $sessionId = (int) $data['session'];
        $semester = (int) $data['semaster'];

        \DB::transaction(function () use ($student, $sessionId, $semester) {
            StudentCourse::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'session_id' => $sessionId,
                ],
                [
                    'user_id'  => Auth::id(),
                    'semaster' => $semester,
                ]
            );

            $student->update([
                'session_id'       => $sessionId,
                'current_semester' => $semester,
            ]);
        });

        return back()->with('success', "Student promoted to semester {$semester} successfully.");
    }

    public function upload()
    {
        $courses = Courses::with('subjects')->get();
            $sessions = Session::all();

        return view('admin.student_upload', compact('courses','sessions'));
    }
       public function uploadAKS()
    {
        $courses = Courses::with('subjects')->get();
            $sessions = Session::all();

        return view('admin.student_uploadAKS', compact('courses','sessions'));
    }
    public function upload_saveAKS(Request $r){
            ini_set ( 'max_execution_time', 0); 
 $r->validate([
         'csv_file' => 'required|mimes:csv,txt',
     ]);
      $path = $r->file('csv_file')->getRealPath();
    $file = fopen($path, "r");

    $header = fgetcsv($file); // skip 1st row
    // dd($header);
    $arr=[];
    foreach($header as $key=>$val){
$arr[$val]=$key;
    }
    // dd($arr);
    $uploaded=0;

       while (($row = fgetcsv($file)) !== false) {
$session_namee=$row[$arr['session']];
$Session = Session::where('session_name',$session_namee)->first();
if(!$Session){
    $Session = Session::create([
        'session_name'=>$session_namee
    ]);
}
// $course_name=str_contains($row[$arr['course']],"B.A.")?"HUMANITIES AND SOCIAL SCIENCES":
// (str_contains($row[$arr['course']],"B.Sc.")?"NATURAL AND PHYSICAL SCIENCES":
// ($row[$arr['course']]=="COMMERCE"?"COMMERCE AND MANAGEMENT":(
//     $row[$arr['course']]=="MATHEMATICS"?'MATHEMATICS, STATISTICS, AND COMPUTER APPLICATIONS':(
        
//         str_contains($row[$arr['course']],"B.Com.")?'COMMERCE AND MANAGEMENT'
//     )
// ))
// );

// if($course_name==""){
//     if($row[$arr['course']]=="ENGLISH" 
//     || $row[$arr['course']]=="HINDI" 
//     || $row[$arr['course']]=="GEOGRAPHY" 
//     || $row[$arr['course']]=="POLITICAL SCIENCE" 
//     || $row[$arr['course']]=="HOME SCIENCE" 
//     || $row[$arr['course']]=="HINDI" 
//     ){
//         $course_name="HUMANITIES AND SOCIAL SCIENCES";
//     }else{
//         $course_name="NATURAL AND PHYSICAL SCIENCES";
//     }
// }
// $subject_name=str_replace("B.A. - ",'',$row[$arr['course']]);
// $subject_name=str_replace("B.Sc. - ",'',$subject_name);
// $subject_name=str_replace("B.Com. - ",'',$subject_name);
// Normalize input: remove degree prefixes and trim whitespace
$raw_value = $row[$arr['course']];
$clean_input = strtoupper(trim(str_replace(["B.A. - ", "B.Sc. - ", "B.Com. - "], "", $raw_value)));

// Define the Subject-to-Category Map
$subject_map = [
    // HUMANITIES AND SOCIAL SCIENCES
    'HISTORY'           => 'HUMANITIES AND SOCIAL SCIENCES',
    'POLITICAL SCIENCE' => 'HUMANITIES AND SOCIAL SCIENCES',
    'GEOGRAPHY'         => 'HUMANITIES AND SOCIAL SCIENCES',
    'HINDI'             => 'HUMANITIES AND SOCIAL SCIENCES',
    'ENGLISH'           => 'HUMANITIES AND SOCIAL SCIENCES',
    'ECONOMICS'         => 'HUMANITIES AND SOCIAL SCIENCES',
    'HOME SCIENCE'      => 'HUMANITIES AND SOCIAL SCIENCES',
    'PSYCHOLOGY'        => 'HUMANITIES AND SOCIAL SCIENCES',
    'PHILOSOPHY'        => 'HUMANITIES AND SOCIAL SCIENCES',
    'URDU'              => 'HUMANITIES AND SOCIAL SCIENCES',
    'SOCIOLOGY'         => 'HUMANITIES AND SOCIAL SCIENCES',
    'SANSKRIT'          => 'HUMANITIES AND SOCIAL SCIENCES',

    // NATURAL AND PHYSICAL SCIENCES
    'ZOOLOGY'           => 'NATURAL AND PHYSICAL SCIENCES',
    'CHEMISTRY'         => 'NATURAL AND PHYSICAL SCIENCES',
    'PHYSICS'           => 'NATURAL AND PHYSICAL SCIENCES',
    'BOTANY'            => 'NATURAL AND PHYSICAL SCIENCES',

    // MATHEMATICS & STATS
    'MATHEMATICS'       => 'MATHEMATICS, STATISTICS, AND COMPUTER APPLICATIONS',

    // COMMERCE
    'COMMERCE'          => 'COMMERCE AND MANAGEMENT'
];

// 1. Initial assignment based on keywords (B.A., B.Sc., etc.)
if (str_contains($raw_value, "B.A.")) {
    $course_name = "HUMANITIES AND SOCIAL SCIENCES";
} elseif (str_contains($raw_value, "B.Sc.")) {
    $course_name = "NATURAL AND PHYSICAL SCIENCES";
} elseif (str_contains($raw_value, "B.Com.") || $clean_input == "COMMERCE") {
    $course_name = "COMMERCE AND MANAGEMENT";
} else {
    // 2. Fallback: Direct subject name match from the map
    $course_name = $subject_map[$clean_input] ?? "";
}

// 3. Final Catch-all
if ($course_name == "") {
    // Default to Science if unmapped, or keep as "OTHER" for data integrity
    $course_name = "NATURAL AND PHYSICAL SCIENCES";
}

// Subject name cleanup for display 
$subject_name = $clean_input;
$course = Courses::where('name',$course_name)->first();
if(!$course){
    $course=Courses::create([
        'user_id'=>Auth::id(),
        'name'=>$course_name,
        'sr'=>1,

    ]);
}
$Subject = Subject::where('name',$subject_name)->where('course_id',$course->id)->first();
if(!$Subject){
$Subject = Subject::create(
[
    'name'=>$subject_name,
    'course_id'=>$course->id
]
);
}
$d = Student::where('roll_no',$row[$arr['roll_number']])->where('student_id',$row[$arr['student_id']])->where('first_name',explode(" ",$row[$arr['full_name']])[0])->count();
if($d==0){
 $uploaded+=1;
 
  $student = Student::create([
            'roll_no' => $row[$arr['roll_number']],
            'student_id' => $row[$arr['student_id']],
            'first_name' =>  explode(" ",$row[$arr['full_name']])[0],
            'last_name' => explode(" ",$row[$arr['full_name']])[1]??'',
            'gender' => $row[$arr['gender']]=="Transgender"?"Other":$row[$arr['gender']],
            'date_of_birth' => $row[$arr['dob']],
            'phone_number' =>$row[$arr['mobile']],
            'email' => $row[$arr['email']],
            'father_name' => $row[$arr['father_name']],
            'mother_name' => $row[$arr['mother_name']],
            'batch' => $row[$arr['session']],
            'admission_date' => '2026-01-01',
            'current_year' => 1,
            'current_semester' =>1,
            'category' =>$row[$arr['caste_category']],
            'caste' => $row[$arr['caste_category']],
            'permanent_address' => '---',
            'correspondence_address' => '----',
            'course' => $course->id,  
            'user_id' => Auth::id(),
            'session_id'=>$Session->id,
            'subjects'=>[$Subject->id],
            'status'=>'Active',
            'photo'=>$row[$arr['student_photo']]
        ]);
        StudentCourse::create([
                'user_id'    => Auth::id(),
                'student_id' => $student->id,
                'session_id' => $Session->id,
                'semaster'   => $row[$arr['semester']],
            ]);
}


       }
           fclose($file);
echo "Uploaded: ".$uploaded;
    }
    public function upload_save(Request $r)
{
    ini_set ( 'max_execution_time', 0); 

    $r->validate([
        'course_id' => 'required',
        'csv_file' => 'required|mimes:csv,txt',
        'session_id' => 'required|array',
        'semester' => 'required|array',
    ]);

    $path = $r->file('csv_file')->getRealPath();
    $file = fopen($path, "r");

    $header = fgetcsv($file); // skip 1st row
$uploaded=0;
    while (($row = fgetcsv($file)) !== false) {

        // Create student
        $d = Student::where('roll_no',$row[0])->where('student_id',$row[1])->where('first_name',explode(" ",$row[2])[0])->count();
        if($d==0    ){
            $uploaded+=1;
        $student = Student::create([
            'roll_no' => $row[0],
            'student_id' => $row[1],
            'first_name' =>  explode(" ",$row[2])[0],
            'last_name' => explode(" ",$row[2])[1]??'',
            'gender' => $row[3],
            'date_of_birth' => self::convertDate($row[4]),
            'phone_number' => $row[5],
            'email' => $row[6],
            'father_name' => $row[7],
            'mother_name' => $row[8],
            'batch' => $row[9],
            'admission_date' => self::convertDate($row[10]),
            'current_year' => $row[11],
            'current_semester' => $row[12],
            'category' => $row[13],
            'caste' => $row[14],
            'permanent_address' => $row[15],
            'correspondence_address' => $row[16],
            'course' => $r->course_id,  // <-- entire CSV applies 1 course
            'user_id' => Auth::id(),
            'session_id'=>Auth::user()->active_session_id,
            'subjects'=>[],
            'status'=>'Active'
        ]);

        // Insert session + semester rows
        foreach ($r->session_id as $i => $session_id) {
            StudentCourse::create([
                'user_id'    => Auth::id(),
                'student_id' => $student->id,
                'session_id' => $session_id,
                'semaster'   => $r->semester[$i],
            ]);
        }
        }
    }

    fclose($file);
echo "Uploaded: ".$uploaded;
    // return back()->with('success', 'CSV uploaded successfully for selected course!');
}
function convertDate($date)
{
    if (!$date) return null;

    // Expecting DD/MM/YYYY
    $parts = explode('/', $date);

    if (count($parts) !== 3) {
        return null; // avoid crash
    }

    return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
}
}
?>
