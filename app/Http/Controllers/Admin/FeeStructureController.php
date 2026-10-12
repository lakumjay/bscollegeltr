<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Courses;
use App\Models\FeeStructure;
use App\Models\FeeStructure2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $courses = Courses::get();
        $feeHeads = collect();
        $selectedCourseId = $request->query('course_id');

        if ($selectedCourseId) {
            $user = Auth::user();
            $feeHeads = FeeStructure::where('course_id', $selectedCourseId)
                
                ->where('session_id', $user->active_session_id)
                ->get();
        }

        return view('admin.fee.fee_structure', compact('courses', 'feeHeads', 'selectedCourseId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'fee_heads' => 'required|array',
            'fee_heads.*.id' => 'nullable|exists:fee_structures,fee_head_id',
            'fee_heads.*.name' => 'required|string|max:255',
            'fee_heads.*.amount' => 'required|numeric|min:0',
       
        ]);

        $user = Auth::user();

        foreach ($request->fee_heads as $head) {
            $data = [
                'user_id' => $user->id,
                'session_id' => $user->active_session_id,
                'course_id' => $request->course_id,
                'fee_head_name' => $head['name'],
                'amount' => $head['amount'],
                 'is_optional' => isset($head['is_optional']),
            ];

            if (isset($head['id'])) {
                // Update existing fee head
                FeeStructure::where('fee_head_id', $head['id'])
                    
                    ->where('session_id', $user->active_session_id)
                    ->update($data);
            } else {
                // Create new fee head
                $data['fee_head_id'] = Str::uuid();
                FeeStructure::create($data);
            }
        }

        return redirect()->route('fee_structure.index', ['course_id' => $request->course_id])
            ->with('success', 'Fee structure saved successfully!');
    }

    private function defaultFeeHeads(): array
    {
        return [
    "Admission form",
    "Admission fee",
    "Tution fee",
    "C.D.F Fee",
    "S.D.F Fee",
    "Library",
    "Mid sem exam",
    "Student aid fund",
    "Common room",
    "Games & cultural",
    "Icard",
    "Building fee",
    "Study tour / seminar",
    "College development fund",
    "Annual charge",
    "Laboratory charge",
    "NSS",
    "Festival charge",
    "CLC & Character certificate",
    "Other charge",
    "Registration fee",
    "University exam fee with practical",
    "University exam fee",
    "Rectification / exam form",
    "Marksheet fee",
    "No dues form",
    "Bonafide",
    "Urgent charge for CLC & Character",
    "Rectification charge",
    "Forwarding charge",
    "Prospectus",
    "College examination fees"
        ];
    }

    public function index2(Request $request)
    {
        $courses = Courses::orderBy('name')->get();
        // Request::integer() is not available in the Laravel version used by this project.
        $selectedCourseId = $request->filled('course_id') ? (int) $request->input('course_id') : null;
        $selectedCourse = $selectedCourseId ? $courses->firstWhere('id', $selectedCourseId) : null;

        abort_if($selectedCourseId && !$selectedCourse, 404);

        $fees = collect();
        if ($selectedCourse) {
            $existing = FeeStructure2::where('session_id', Auth::user()->active_session_id)
                ->where('course_id', $selectedCourse->id)
                ->get()
                ->keyBy('head_name');

            $fees = collect($this->defaultFeeHeads())->map(function (string $headName) use ($existing) {
                $defaults = ['head_name' => $headName];
                foreach (range(1, 8) as $semester) {
                    $defaults['sem' . $semester] = 0;
                }

                return array_merge($defaults, optional($existing->get($headName))->toArray() ?? []);
            })->values();
        }

        return view('admin.fee.fee_structure2', compact('courses', 'selectedCourseId', 'selectedCourse', 'fees'));
    }

    public function store2(Request $request)
    {
        $rules = [
            'course_id' => 'required|exists:courses,id',
            'fees' => 'required|array',
            'fees.*.head_name' => 'required|string|max:255',
        ];
        foreach (range(1, 8) as $semester) {
            $rules['fees.*.sem' . $semester] = 'required|numeric|min:0';
        }
        $data = $request->validate($rules);
        $sessionId = Auth::user()->active_session_id;

        abort_if(!$sessionId, 422, 'Select an active academic session before saving a fee structure.');

        DB::transaction(function () use ($data, $sessionId) {
            foreach ($data['fees'] as $feeData) {
                $values = [];
                foreach (range(1, 8) as $semester) {
                    $values['sem' . $semester] = $feeData['sem' . $semester];
                }

                FeeStructure2::updateOrCreate(
                    [
                        'session_id' => $sessionId,
                        'course_id' => $data['course_id'],
                        'head_name' => $feeData['head_name'],
                    ],
                    $values
                );
            }
        });

        return redirect()->route('fee_structure.index', ['course_id' => $data['course_id']])
            ->with('success', 'Fee structure saved for the selected course.');
    }

}
