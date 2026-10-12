<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use App\Models\FeePayment;
use App\Models\FeeRecord;
use App\Models\FeeStructure;
use App\Models\FeeStructure2;
use App\Models\Profile;
use App\Models\SpecialCategory;
use App\Models\SpecialCategoryFeeHead;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Helpers\DbHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeManagementController extends Controller
{
    // ─── Shared Constants ─────────────────────────────────────────────────────
    // To add a new payment method: just add it here — it appears everywhere.
    const PAYMENT_METHODS = ['Cash', 'Bank Transfer', 'UPI', 'Cheque'];

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Get the current semester of a student in the active session.
     * Returns 1 if no record found.
     */
    private function getStudentSemester(int $studentId, int $sessionId): int
    {
        return (int) (DB::table('student_courses')
            ->where('student_id', $studentId)
            ->where('session_id', $sessionId)
            ->value('semaster') ?: 1);
    }

    /**
     * Build the base FeePayment query for a student+course in current session.
     */
    private function studentPaymentsQuery(int $studentId, int $courseId, int $sessionId)
    {
        return FeePayment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('session_id', $sessionId);
    }

    /**
     * Get total fees for a course in a given semester from fee_structure2.
     */
    private function getTotalFees(int $courseId, int $sem, int $sessionId): float
    {
        $col = 'sem' . $sem;
        return (float) FeeStructure2::where('session_id', $sessionId)
            ->where('course_id', $courseId)
            ->sum($col);
    }

    // ─── Fee Management: Student List ─────────────────────────────────────────

    public function index(Request $request)
    {
        $user     = Auth::user();
        $courses  = Courses::get();
        $students = collect();
        $selectedCourseId = $request->query('course_id');

        if ($selectedCourseId) {
            $sessionId = $user->active_session_id;

            $query = Student::with(['StudentCourse' => function ($relation) use ($sessionId) {
                    $relation->where('session_id', $sessionId);
                }])
                ->where('course', $selectedCourseId)
                ->whereIn('id', function ($q) use ($sessionId) {
                    $q->select('student_id')
                        ->from('student_courses')
                        ->where('session_id', $sessionId);
                });

            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                      ->orWhere('roll_no', 'LIKE', "%{$search}%");
                });
            }

            $students = $query->paginate(30)->withQueryString();
        }

        return view('admin.fee.fee_management', compact('courses', 'students', 'selectedCourseId'));
    }

    // ─── Generate Fee Records for a Student ───────────────────────────────────

    public function generate(Request $request)
    {
        $request->validate([
            'student_id'          => 'required|exists:students,id',
            'course_id'           => 'required|exists:courses,id',
            'special_category_id' => 'nullable|exists:special_categories,id',
        ]);

        $user      = Auth::user();
        $sessionId = $user->active_session_id;
        $studentId = $request->student_id;
        $courseId  = $request->course_id;

        if (FeeRecord::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('session_id', $sessionId)
                ->exists()) {
            return redirect()->route('fee_management.index', ['course_id' => $courseId])
                ->with('error', 'Fees already generated. Use Regenerate option.');
        }

        $this->createFeeRecords($user, $sessionId, $studentId, $courseId, $request->special_category_id);

        return redirect()->route('fee_management.index', ['course_id' => $courseId])
            ->with('success', 'Fees generated successfully!');
    }

    // ─── Regenerate Fee Records ────────────────────────────────────────────────

    public function regenerate(Request $request)
    {
        $request->validate([
            'student_id'          => 'required|exists:students,id',
            'course_id'           => 'required|exists:courses,id',
            'special_category_id' => 'nullable|exists:special_categories,id',
        ]);

        $user      = Auth::user();
        $sessionId = $user->active_session_id;
        $studentId = $request->student_id;
        $courseId  = $request->course_id;

        $feeStructures = FeeStructure::where('course_id', $courseId)
            ->where('session_id', $sessionId)
            ->when($request->filled('feeStructures_ids'), fn($q) => $q->whereIn('id', $request->feeStructures_ids))
            ->get();

        $this->createFeeRecords(
            $user, $sessionId, $studentId, $courseId,
            $request->special_category_id,
            $feeStructures
        );

        return redirect()->route('fee_management.view', ['student_id' => $studentId, 'course_id' => $courseId])
            ->with('success', 'Fees regenerated successfully!');
    }

    /**
     * Shared logic: create fee records for a student from fee structures.
     * Pass $feeStructures to use a custom set, or null to load all for the course.
     */
    private function createFeeRecords($user, int $sessionId, int $studentId, int $courseId, ?int $specialCategoryId, $feeStructures = null): void
    {
        if ($feeStructures === null) {
            $feeStructures = FeeStructure::where('course_id', $courseId)
                ->where('session_id', $sessionId)
                ->get();
        }

        $discounts = [];
        if ($specialCategoryId) {
            $discounts = SpecialCategoryFeeHead::whereHas('specialCategory', fn($q) => $q->where('id', $specialCategoryId))
                ->get()
                ->keyBy('fee_head_id');
        }

        foreach ($feeStructures as $fs) {
            $discount = 0;
            if (isset($discounts[$fs->fee_head_id])) {
                $disc     = $discounts[$fs->fee_head_id];
                $discount = $disc->discount_type === 'Percentage'
                    ? ($fs->amount * $disc->discount / 100)
                    : $disc->discount;
            }

            FeeRecord::create([
                'user_id'             => $user->id,
                'session_id'          => $sessionId,
                'student_id'          => $studentId,
                'course_id'           => $courseId,
                'fee_head_id'         => $fs->fee_head_id,
                'fee_head_name'       => $fs->fee_head_name,
                'special_category_id' => $specialCategoryId,
                'amount'              => $fs->amount,
                'discount'            => $discount,
                'net_amount'          => $fs->amount - $discount,
                'due_date'            => Carbon::now()->addDays(30),
            ]);
        }
    }

    // ─── Add One-Off Fee to a Student ─────────────────────────────────────────

    public function addFee(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'course_id'     => 'required|exists:courses,id',
            'fee_head_name' => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0',
            'due_date'      => 'required|date',
            'notes'         => 'nullable|string',
        ]);

        FeeRecord::create([
            'user_id'       => Auth::id(),
            'session_id'    => Auth::user()->active_session_id,
            'student_id'    => $request->student_id,
            'course_id'     => $request->course_id,
            'fee_head_id'   => null,
            'fee_head_name' => $request->fee_head_name,
            'amount'        => $request->amount,
            'discount'      => 0,
            'net_amount'    => $request->amount,
            'due_date'      => $request->due_date,
            'notes'         => $request->notes,
        ]);

        return redirect()->route('fee_management.view', [
            'student_id' => $request->student_id,
            'course_id'  => $request->course_id,
        ])->with('success', 'Additional fee added successfully!');
    }

    // ─── Student Fee Details Page ──────────────────────────────────────────────

    public function view(Request $request, $studentId, $courseId)
    {
        $user      = Auth::user();
        $sessionId = $user->active_session_id;

        $student          = Student::findOrFail($studentId);
        $course           = Courses::findOrFail($courseId);
        $specialCategories = SpecialCategory::where('course_id', $courseId)->get();
        $feeStructures    = FeeStructure::where('course_id', $courseId)->where('session_id', $sessionId)->get();
        $feeRecords       = FeeRecord::where('student_id', $studentId)
                                ->where('course_id', $courseId)
                                ->where('session_id', $sessionId)
                                ->with('feeStructure', 'specialCategory')
                                ->get();
        $payments         = $this->studentPaymentsQuery($studentId, $courseId, $sessionId)->get();
        $sem              = $this->getStudentSemester($studentId, $sessionId);
        $total_fees       = $this->getTotalFees($courseId, $sem, $sessionId);
        $paid             = (float) $payments->sum('amount');
        $discountTotal    = (float) $payments->sum('discount_amount');
        $balance          = max(0, (float) $total_fees - $paid - $discountTotal);
        $paymentMethods   = self::PAYMENT_METHODS;

        return view('admin.fee.fee_view', compact(
            'student', 'course', 'feeRecords', 'payments',
            'specialCategories', 'feeStructures',
            'total_fees', 'paid', 'discountTotal', 'balance', 'sem', 'paymentMethods'
        ));
    }

    // ─── AJAX: Load Fee Heads for a Semester ──────────────────────────────────

    public function getFeeHeads(Request $request)
    {
        $courseId  = (int) $request->course_id;
        $sem       = (int) $request->semester;
        $sessionId = Auth::user()->active_session_id;

        if (!$courseId || $sem < 1 || $sem > 8) {
            return response()->json([]);
        }

        $semCol   = 'sem' . $sem;
        $feeHeads = FeeStructure2::where('course_id', $courseId)
            ->where('session_id', $sessionId)
            ->orderBy('head_name')
            ->get(['id', 'head_name', $semCol]);

        return response()->json(
            $feeHeads->map(fn($fh) => [
                'id'     => $fh->id,
                'name'   => $fh->head_name,
                'amount' => (float) ($fh->$semCol ?? 0),
            ])
        );
    }

    // ─── Record Multiple Fee Payments at Once ─────────────────────────────────

    public function payMulti(Request $request)
    {
        $data = $request->validate([
            'student_id'         => 'required|exists:students,id',
            'course_id'          => 'required|exists:courses,id',
            'semester'           => 'required|integer|between:1,8',
            'payment_date'       => 'required|date',
            'payment_method'     => 'required|in:' . implode(',', self::PAYMENT_METHODS),
            'transaction_id'     => 'nullable|string|max:255',
            'notes'              => 'nullable|string|max:1000',
            'student_type'       => 'nullable|string|in:Regular College,Ward,Other College',
            'other_college_name' => 'nullable|string|max:255',
            'discount_eligible'  => 'nullable|boolean',
            'discount_amount'    => 'nullable|numeric|min:0',
            'fee_heads'          => 'required|array|min:1',
            'fee_heads.*.name'   => 'required|string|max:255',
            'fee_heads.*.amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $sessionId = $user->active_session_id;

        if (!$sessionId) {
            return back()->withInput()->with('error', 'Select an active academic session before recording a payment.');
        }

        $isEnrolled = DB::table('student_courses')
            ->join('students', 'students.id', '=', 'student_courses.student_id')
            ->where('student_courses.student_id', $data['student_id'])
            ->where('students.course', $data['course_id'])
            ->where('student_courses.session_id', $sessionId)
            ->exists();

        if (!$isEnrolled) {
            return back()->withInput()->withErrors([
                'student_id' => 'This student is not enrolled in the active academic session for the selected course.',
            ]);
        }

        $selectedHeads = collect($data['fee_heads'])
            ->filter(fn (array $feeHead) => (float) $feeHead['amount'] > 0)
            ->values();

        if ($selectedHeads->isEmpty()) {
            return back()->withInput()->withErrors([
                'fee_heads' => 'Select at least one fee head with an amount greater than zero.',
            ]);
        }

        $selectedNames = $selectedHeads->pluck('name')->unique()->values();
        $availableNames = FeeStructure2::where('session_id', $sessionId)
            ->where('course_id', $data['course_id'])
            ->whereIn('head_name', $selectedNames)
            ->pluck('head_name');

        if ($availableNames->count() !== $selectedNames->count()) {
            return back()->withInput()->withErrors([
                'fee_heads' => 'One or more selected fee heads are not available for this course in the active session.',
            ]);
        }

        $grossAmount = round((float) $selectedHeads->sum(fn (array $feeHead) => (float) $feeHead['amount']), 2);
        $discountEligible = $request->boolean('discount_eligible');
        $discountAmount = $discountEligible
            ? round((float) ($data['discount_amount'] ?? ($grossAmount * 0.10)), 2)
            : 0;

        if ($discountAmount > $grossAmount) {
            return back()->withInput()->withErrors([
                'discount_amount' => 'Discount cannot be greater than the selected fee amount.',
            ]);
        }

        $studentType = $data['student_type'] ?? 'Regular College';
        $otherCollegeName = $studentType === 'Other College' ? ($data['other_college_name'] ?? null) : null;

        $createdIds = DB::transaction(function () use ($data, $user, $sessionId, $selectedHeads, $grossAmount, $discountEligible, $discountAmount, $studentType, $otherCollegeName) {
            $paymentIds = [];
            $remainingDiscount = $discountAmount;
            $lastIndex = $selectedHeads->count() - 1;

            foreach ($selectedHeads as $index => $feeHead) {
                $headAmount = round((float) $feeHead['amount'], 2);
                $headDiscount = $discountEligible
                    ? ($index === $lastIndex
                        ? $remainingDiscount
                        : round($discountAmount * ($headAmount / $grossAmount), 2))
                    : 0;
                $remainingDiscount = round($remainingDiscount - $headDiscount, 2);

                // Fee payments must always belong to a real fee record.  Older
                // installations may not have generated records from the new
                // course fee matrix yet, so create the missing record once and
                // reuse it for later payments.
                $feeRecord = FeeRecord::firstOrCreate(
                    [
                        'session_id'    => $sessionId,
                        'student_id'    => $data['student_id'],
                        'course_id'     => $data['course_id'],
                        'fee_head_name' => $feeHead['name'],
                    ],
                    [
                        'user_id'             => $user->id,
                        'fee_head_id'         => null,
                        'special_category_id' => null,
                        'amount'              => $headAmount,
                        'discount'            => 0,
                        'net_amount'          => $headAmount,
                        'due_date'            => Carbon::parse($data['payment_date'])->addDays(30),
                        'notes'               => 'Created from the active course fee structure.',
                    ]
                );

                $payment = FeePayment::create([
                    'user_id'            => $user->id,
                    'session_id'         => $sessionId,
                    'student_id'         => $data['student_id'],
                    'course_id'          => $data['course_id'],
                    'fee_record_id'      => $feeRecord->id,
                    'amount'             => round(max(0, $headAmount - $headDiscount), 2),
                    'discount_eligible'  => $discountEligible,
                    'discount_amount'    => $headDiscount,
                    'student_type'       => $studentType,
                    'other_college_name' => $otherCollegeName,
                    'payment_date'       => Carbon::parse($data['payment_date'])->format('Y-m-d'),
                    'payment_method'     => $data['payment_method'],
                    'transaction_id'     => $data['transaction_id'] ?? null,
                    'notes'              => trim($feeHead['name'] . (!empty($data['notes']) ? ' — ' . $data['notes'] : '')),
                ]);

                $paymentIds[] = $payment->id;
            }

            return $paymentIds;
        });

        return redirect()->route('fee_management.receipt_batch', [
            'student_id' => $data['student_id'],
            'course_id'  => $data['course_id'],
        ])->with('batch_payment_ids', implode(',', $createdIds))
          ->with('success', 'Payment recorded successfully.');
    }

    // ─── Single Fee Payment (Legacy) ──────────────────────────────────────────

    public function pay(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,id',
            'course_id'          => 'required|exists:courses,id',
            'fee_record_id'      => 'required',
            'amount'             => 'required|numeric|min:0',
            'payment_date'       => 'required|date',
            'payment_method'     => 'required|in:' . implode(',', self::PAYMENT_METHODS),
            'transaction_id'     => 'nullable|string|max:255',
            'student_type'       => 'nullable|string|in:Regular College,Ward,Other College',
            'other_college_name' => 'nullable|string|max:255',
            'notes'              => 'nullable|string',
        ]);

        $studentType = $request->input('student_type', 'Regular College');
        $otherCollegeName = $studentType === 'Other College' ? $request->input('other_college_name') : null;

        FeePayment::create([
            'user_id'            => Auth::id(),
            'session_id'         => Auth::user()->active_session_id,
            'student_id'         => $request->student_id,
            'course_id'          => $request->course_id,
            'fee_record_id'      => $request->fee_record_id,
            'amount'             => $request->amount,
            'student_type'       => $studentType,
            'other_college_name' => $otherCollegeName,
            'payment_date'       => Carbon::parse($request->payment_date)->format('Y-m-d'),
            'payment_method'     => $request->payment_method,
            'transaction_id'     => $request->transaction_id,
            'notes'              => $request->notes,
        ]);

        return redirect()->route('fee_management.view', [
            'student_id' => $request->student_id,
            'course_id'  => $request->course_id,
        ])->with('success', 'Payment recorded successfully!');
    }

    // ─── Delete a Payment Receipt ──────────────────────────────────────────────

    public function delete_receipt($studentId, $courseId, $receiptId)
    {
        $sessionId = Auth::user()->active_session_id;

        FeePayment::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('session_id', $sessionId)
            ->where('id', $receiptId)
            ->firstOrFail()
            ->delete();

        return back()->with('success', 'Receipt deleted!');
    }

    // ─── Receipts ─────────────────────────────────────────────────────────────

    /**
     * Batch receipt: shown right after payMulti — shows only the just-paid fees.
     */
    public function receiptBatch($studentId, $courseId)
    {
        $user       = Auth::user();
        $sessionId  = $user->active_session_id;
        $student    = Student::findOrFail($studentId);
        $course     = Courses::findOrFail($courseId);
        $profile    = $user->profile ?: Profile::first();
        $idsStr     = session('batch_payment_ids', '');
        $paymentIds = array_filter(explode(',', $idsStr));

        if (empty($paymentIds)) {
            return redirect()->route('fee_management.view', compact('studentId', 'courseId') + [
                'student_id' => $studentId, 'course_id' => $courseId,
            ])->with('error', 'Receipt data not found. Please try again.');
        }

        $payments = FeePayment::whereIn('id', $paymentIds)->get();
        $sem      = $this->getStudentSemester($studentId, $sessionId);

        return view('admin.fee.fee_reciept_single', compact('student', 'course', 'payments', 'sem', 'profile'));
    }

    /**
     * Single receipt: shown when clicking Print on one row in the payments table.
     */
    public function receipt_single($studentId, $courseId, $receiptId)
    {
        $user       = Auth::user();
        $sessionId  = $user->active_session_id;
        $student    = Student::findOrFail($studentId);
        $course     = Courses::findOrFail($courseId);
        $profile    = $user->profile ?: Profile::first();
        $payments   = FeePayment::where('student_id', $studentId)
                        ->where('course_id', $courseId)
                        ->where('session_id', $sessionId)
                        ->where('id', $receiptId)
                        ->get();
        $sem        = $this->getStudentSemester($studentId, $sessionId);

        return view('admin.fee.fee_reciept_single', compact('student', 'course', 'payments', 'sem', 'profile'));
    }

    /**
     * Full receipt: all payments for the student in this session.
     */
    public function receipt($studentId, $courseId)
    {
        $user      = Auth::user();
        $sessionId = $user->active_session_id;
        $student   = Student::findOrFail($studentId);
        $course    = Courses::findOrFail($courseId);
        $profile   = $user->profile ?: Profile::first();
        $feeRecords = FeeRecord::where('student_id', $studentId)
                        ->where('course_id', $courseId)
                        ->where('session_id', $sessionId)
                        ->with('feeStructure', 'specialCategory')
                        ->get();
        $payments  = $this->studentPaymentsQuery($studentId, $courseId, $sessionId)->get();
        $sem       = $this->getStudentSemester($studentId, $sessionId);
        $total_fees = $this->getTotalFees($courseId, $sem, $sessionId);

        return view('admin.fee.fee_receipt', compact('student', 'course', 'feeRecords', 'payments', 'total_fees', 'profile'));
    }

    // ─── Fee Report ───────────────────────────────────────────────────────────

    /**
     * Payment report with rich filters:
     *   course_id       — required first step
     *   fee_head        — filter by fee head name (partial match)
     *   payment_method  — Cash / UPI / etc.
     *   date_from       — payment date from
     *   date_to         — payment date to
     *   semester        — filter students in a specific semester
     *   student_name    — partial name / roll no search
     */
    public function report(Request $request)
    {
        $user      = Auth::user();
        $sessionId = $user->active_session_id;
        $courses   = Courses::get();

        $selectedCourseId  = $request->query('course_id');
        $feeHeadNames      = collect();   // distinct fee head names for this course
        $payments          = collect();

        // Filters (empty = no filter applied)
        $filterFeeHead     = $request->query('fee_head', '');
        $filterMethod      = $request->query('payment_method', '');
        $filterDateFrom    = $request->query('date_from', '');
        $filterDateTo      = $request->query('date_to', '');
        $filterSemester    = $request->query('semester', '');
        $filterStudent     = $request->query('student_name', '');

        if ($selectedCourseId) {
            // Distinct fee-head names for the filter dropdown
            $feeHeadNames = FeePayment::where('course_id', $selectedCourseId)
                ->where('session_id', $sessionId)
                ->whereNotNull('notes')
                ->distinct()
                ->orderBy('notes')
                ->pluck('notes');

            // Build the payments query
            $query = FeePayment::with('student')
                ->where('course_id', $selectedCourseId)
                ->where('session_id', $sessionId);

            if ($filterFeeHead) {
                $query->where('notes', 'like', "%{$filterFeeHead}%");
            }
            if ($filterMethod) {
                $query->where('payment_method', $filterMethod);
            }
            if ($filterDateFrom) {
                try {
                    $dateFrom = Carbon::parse($filterDateFrom)->format('Y-m-d');
                    $query->where('payment_date', '>=', $dateFrom);
                } catch (\Exception $e) {}
            }
            if ($filterDateTo) {
                try {
                    $dateTo = Carbon::parse($filterDateTo)->format('Y-m-d');
                    $query->where('payment_date', '<=', $dateTo);
                } catch (\Exception $e) {}
            }
            if ($filterSemester) {
                // Filter students whose semester matches
                $studentIds = DB::table('student_courses')
                    ->where('session_id', $sessionId)
                    ->where('semaster', $filterSemester)
                    ->pluck('student_id');
                $query->whereIn('student_id', $studentIds);
            }
            if ($filterStudent) {
                $il = DbHelper::ilike();
                $query->whereHas('student', function ($q) use ($filterStudent, $il) {
                    $q->where(function ($inner) use ($filterStudent, $il) {
                        $inner->whereRaw("CONCAT(first_name, ' ', last_name) {$il} ?", ["%{$filterStudent}%"])
                              ->orWhereRaw("roll_no {$il} ?", ["%{$filterStudent}%"]);
                    });
                });
            }

            $payments = $query->orderBy('payment_date', 'desc')->get();
        }

        $paymentMethods = self::PAYMENT_METHODS;
        $semesterOptions = range(1, 8);

        return view('admin.fee.fee_report', compact(
            'courses', 'selectedCourseId',
            'feeHeadNames', 'payments',
            'paymentMethods', 'semesterOptions',
            'filterFeeHead', 'filterMethod',
            'filterDateFrom', 'filterDateTo',
            'filterSemester', 'filterStudent'
        ));
    }

    // ─── Shared: Build Filtered Report Query ──────────────────────────────────
    //
    // All export methods (CSV, Excel, PDF) use this so filters are always consistent.
    // To add a new filter: add it here once and it applies everywhere.

    private function buildReportQuery(Request $request, int $sessionId)
    {
        $query = FeePayment::with('student')
            ->where('course_id', $request->course_id)
            ->where('session_id', $sessionId);

        if ($v = $request->fee_head)       $query->where('notes', 'like', "%{$v}%");
        if ($v = $request->payment_method) $query->where('payment_method', $v);
        if ($v = $request->date_from) {
            try {
                $query->where('payment_date', '>=', Carbon::parse($v)->format('Y-m-d'));
            } catch (\Exception $e) {}
        }
        if ($v = $request->date_to) {
            try {
                $query->where('payment_date', '<=', Carbon::parse($v)->format('Y-m-d'));
            } catch (\Exception $e) {}
        }
        if ($v = $request->semester) {
            $studentIds = DB::table('student_courses')
                ->where('session_id', $sessionId)
                ->where('semaster', $v)
                ->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        }
        if ($v = $request->student_name) {
            $il = DbHelper::ilike();
            $query->whereHas('student', function ($q) use ($v, $il) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) {$il} ?", ["%{$v}%"])
                  ->orWhereRaw("roll_no {$il} ?", ["%{$v}%"]);
            });
        }

        return $query->orderBy('payment_date', 'desc');
    }

    // ─── Export: CSV ──────────────────────────────────────────────────────────

    public function reportExport(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);
        $payments = $this->buildReportQuery($request, Auth::user()->active_session_id)->get();

        $filename = 'fee_report_' . now()->format('Y-m-d_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payments) {
            $fh = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fputs($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['#', 'Roll No', 'Student Name', 'Fee Head', 'Amount (Rs)', 'Date', 'Method', 'Txn ID']);
            foreach ($payments as $i => $p) {
                fputcsv($fh, [
                    $i + 1,
                    $p->student->roll_no ?? '',
                    trim(($p->student->first_name ?? '') . ' ' . ($p->student->last_name ?? '')),
                    $p->notes ?? '',
                    number_format($p->amount, 2),
                    $p->payment_date ? $p->payment_date->format('d-m-Y') : '',
                    $p->payment_method,
                    $p->transaction_id ?? '',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Export: Excel (HTML-based, opens natively in Excel) ─────────────────

    public function reportExcel(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);
        $sessionId = Auth::user()->active_session_id;
        $payments  = $this->buildReportQuery($request, $sessionId)->get();
        $course    = Courses::findOrFail($request->course_id);
        $filters   = $request->only(['fee_head','payment_method','date_from','date_to','semester','student_name']);

        $filename = 'fee_report_' . now()->format('Y-m-d_His') . '.xls';
        $content  = view('admin.fee.fee_report_excel', compact('payments', 'course', 'filters'))->render();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    // ─── Export: PDF ──────────────────────────────────────────────────────────

    public function reportPDF(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate(['course_id' => 'required|exists:courses,id']);
        $sessionId = Auth::user()->active_session_id;
        $payments  = $this->buildReportQuery($request, $sessionId)->get();
        $course    = Courses::findOrFail($request->course_id);
        $filters   = $request->only(['fee_head','payment_method','date_from','date_to','semester','student_name']);
        $sessionName = \App\Models\Session::find($sessionId)->session_name ?? 'Current';

        $pdf = Pdf::loadView('admin.fee.fee_report_pdf', compact('payments', 'course', 'filters', 'sessionName'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'fee_report_' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
