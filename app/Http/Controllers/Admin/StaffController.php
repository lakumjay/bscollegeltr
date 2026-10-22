<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\SalaryStructure;
use App\Models\SalaryPayment;
use App\Services\PayrollCalculator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    protected $payrollCalculator;

    public function __construct(PayrollCalculator $payrollCalculator)
    {
        $this->payrollCalculator = $payrollCalculator;
    }

    protected function salaryStructureRules(bool $allComponentsRequired = true): array
    {
        $componentRule = ($allComponentsRequired ? 'required' : 'nullable') . '|numeric|min:0';

        return [
            'basic_pay' => 'required|numeric|min:0',
            'da' => $componentRule,
            'hra' => $componentRule,
            'additional_earnings' => $componentRule,
            'epf' => $componentRule,
            'medical_deduction' => $componentRule,
            'additional_deductions' => $componentRule,
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Za-z]{3}$/'],
        ];
    }

    protected function salaryStructureData(array $validated): array
    {
        $data = [];
        foreach (['basic_pay', 'da', 'hra', 'additional_earnings', 'epf', 'medical_deduction', 'additional_deductions'] as $field) {
            $data[$field] = (float) ($validated[$field] ?? 0);
        }
        $data['currency'] = strtoupper($validated['currency']);
        // Keep the legacy columns aligned for older reports and integrations.
        $data['allowances'] = $data['additional_earnings'];
        $data['deductions'] = $data['additional_deductions'];

        return $data;
    }

    public function calculateSalaryAjax(Request $request)
    {
        // Salary payments are INR-only and no longer accept one-off manual
        // adjustments. Keep the snapshot fields at zero for compatibility
        // with historical records created before the form was simplified.
        $request->merge([
            'currency' => 'INR',
            'manual_allowances' => 0,
            'manual_deductions' => 0,
        ]);

        $validated = $request->validate(array_merge([
            'staff_id' => 'required|exists:staff,id',
            'month_year' => 'required|date_format:Y-m',
            'manual_allowances' => 'nullable|numeric|min:0',
            'manual_deductions' => 'nullable|numeric|min:0',
        ], $this->salaryStructureRules()));

        $structure = new SalaryStructure($this->salaryStructureData($validated));
        $structure->staff_id = $validated['staff_id'];
        $calculated_salary = $this->payrollCalculator->calculateForStructure(
            $structure,
            $validated['month_year'],
            (float) ($validated['manual_allowances'] ?? 0),
            (float) ($validated['manual_deductions'] ?? 0)
        );

        return response()->json($calculated_salary);
    }

    public function index()
    {
        $staff = Staff::with('salaryStructure')->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|unique:staff',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:staff',
            'phone' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'address' => 'nullable',
            'role' => 'required',
            'joining_date' => 'nullable|date',
            'qualification' => 'nullable',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fname = 'photo_' . $validated['staff_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fname);
            $validated['photo'] = 'uploads/' . $fname;
        }
        if ($request->hasFile('id_proof')) {
            $file = $request->file('id_proof');
            $fname = 'id_proof_' . $validated['staff_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('Uploads'), $fname);
            $validated['id_proof'] = 'uploads/' . $fname;
        }
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $fname = 'certificate_' . $validated['staff_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('Uploads'), $fname);
            $validated['certificate'] = 'uploads/' . $fname;
        }

        Staff::create($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff added successfully.');
    }

    public function show(Staff $staff)
    {
        $staff->load('salaryStructure');
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'staff_id' => 'required|unique:staff,staff_id,' . $staff->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'address' => 'nullable',
            'role' => 'required',
            'joining_date' => 'nullable|date',
            'qualification' => 'nullable',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'id_proof' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($staff->photo && file_exists(public_path($staff->photo))) {
                unlink(public_path($staff->photo));
            }
            $file = $request->file('photo');
            $fname = 'photo_' . $validated['staff_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fname);
            $validated['photo'] = 'uploads/' . $fname;
        }
        if ($request->hasFile('id_proof')) {
            if ($staff->id_proof && file_exists(public_path($staff->id_proof))) {
                unlink(public_path($staff->id_proof));
            }
            $file = $request->file('id_proof');
            $fname = 'id_proof_' . $validated['staff_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('Uploads'), $fname);
            $validated['id_proof'] = 'uploads/' . $fname;
        }
        if ($request->hasFile('certificate')) {
            if ($staff->certificate && file_exists(public_path($staff->certificate))) {
                unlink(public_path($staff->certificate));
            }
            $file = $request->file('certificate');
            $fname = 'certificate_' . $validated['staff_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('Uploads'), $fname);
            $validated['certificate'] = 'Uploads/' . $fname;
        }

        $staff->update($validated);

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        // Payroll and attendance records must remain auditable after a staff member leaves.
        // Soft deletion keeps the staff relation available to historical records and retains
        // supporting documents for authorised archive access.
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff archived successfully. Historical records have been retained.');
    }

    public function generateTransferCertificate(Staff $staff)
    {
        return view('admin.staff.transfer_certificate', compact('staff'));
    }

    public function generateIdCard(Staff $staff)
    {
        return view('admin.staff.id_card', compact('staff'));
    }
public function markAttendancePre(Request $request){
    $date = $request->filled('date') ? Carbon::parse($request->input('date'))->format('Y-m-d') : Carbon::today()->toDateString();

    return view('admin.staff.attendance.pre_mark', compact('date'));

}
    public function markAttendance(Request $request)
    {
        $staff = Staff::with('salaryStructure')->get();
        $date = $request->filled('date') ? Carbon::parse($request->date)->format('Y-m-d') : Carbon::today()->toDateString();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'date' => 'required|date',
                'attendance' => 'required|array',
                'attendance.*.status' => 'required|in:Present,Absent,Leave,Holiday',
                'attendance.*.remarks' => 'nullable|string',
            ]);

            $recordDate = Carbon::parse($validated['date'])->format('Y-m-d');
            foreach ($validated['attendance'] as $staff_id => $data) {
                Attendance::updateOrCreate(
                    ['staff_id' => $staff_id, 'date' => $recordDate],
                    ['status' => $data['status'], 'remarks' => $data['remarks']]
                );
            }

            return redirect()->route('admin.staff.attendance.markAttendancePre')->with('success', 'Attendance marked successfully.');
        }

        $attendance = Attendance::where('date', $date)->get()->keyBy('staff_id');
        return view('admin.staff.attendance.mark', compact('staff', 'date', 'attendance'));
    }

    public function viewAttendance(Request $request, ?Staff $staff = null)
    {
        $start_date = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->format('Y-m-d') : Carbon::today()->toDateString();
        $end_date = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->format('Y-m-d') : Carbon::today()->toDateString();

         $query = Attendance::whereBetween('date', [$start_date, $end_date]);


        if ($staff) {
            $query->where('staff_id', $staff->id);
        } elseif ($request->filled('staff_id')) {
            $query->where('staff_id', $request->input('staff_id'));
        }

        $attendance = $query->with('staff')->orderByDesc('date')->get();
        $staffList = Staff::orderBy('first_name')->orderBy('last_name')->get();
         return view('admin.staff.attendance.view', compact('attendance', 'staff', 'staffList', 'start_date', 'end_date'));
    }

    public function generateAttendanceReport(Request $request)
    {
        $start_date = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->format('Y-m-d') : Carbon::today()->subMonth()->toDateString();
        $end_date = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->format('Y-m-d') : Carbon::today()->toDateString();
        $staff_id = $request->input('staff_id');

        $query = Attendance::whereBetween('date', [$start_date, $end_date]);

        if ($staff_id) {
            $query->where('staff_id', $staff_id);
        }

        $attendance = $query->with('staff')->get();
        $staff = $staff_id ? Staff::withTrashed()->find($staff_id) : null;

        return view('admin.staff.attendance.report', compact('attendance', 'staff', 'start_date', 'end_date'));
    }

    public function exportAttendanceCsv(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::today()->subMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::today()->toDateString());
        $staff_id = $request->input('staff_id');

        $query = Attendance::whereBetween('date', [$start_date, $end_date]);
        if ($staff_id) {
            $query->where('staff_id', $staff_id);
        }

        $attendance = $query->with('staff')->get();

        $filename = 'attendance_' . ($staff_id ? $staff_id : 'all') . '_' . Carbon::parse($start_date)->format('Ymd') . '_to_' . Carbon::parse($end_date)->format('Ymd') . '.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Date', 'Staff ID', 'Name', 'Status', 'Remarks']);

        foreach ($attendance as $record) {
            fputcsv($handle, [
                $record->date->format('d-m-Y'),
                $record->staff?->staff_id ?? 'Archived #' . $record->staff_id,
                $record->staff ? trim($record->staff->first_name . ' ' . $record->staff->last_name) : 'Staff record unavailable',
                $record->status,
                $record->remarks ?? 'N/A',
            ]);
        }

        fclose($handle);

        return response()->streamDownload(function() use ($handle) {}, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function manageSalaryStructure(Request $request, Staff $staff)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate(array_merge($this->salaryStructureRules(false), [
                'notes' => 'nullable|string',
            ]));
            $structureData = $this->salaryStructureData($validated);
            $structureData['notes'] = $validated['notes'] ?? null;

            SalaryStructure::updateOrCreate(
                ['staff_id' => $staff->id],
                $structureData
            );

            return redirect()->route('admin.staff.salary.structure', $staff)->with('success', 'Salary structure updated successfully.');
        }

        $structure = SalaryStructure::where('staff_id', $staff->id)->first();
        return view('admin.staff.salary.structure', compact('staff', 'structure'));
    }

    public function manageSalaryPayments_view(Request $request, ?Staff $staff = null)
{
    $start_date = $request->input('start_date', Carbon::today()->subMonth()->toDateString());
    $end_date = $request->input('end_date', Carbon::today()->toDateString());

    $query = SalaryPayment::whereBetween('payment_date', [$start_date, $end_date]);

    if ($staff) {
        $query->where('staff_id', $staff->id);
    } elseif ($request->filled('staff_id')) {
        $query->where('staff_id', $request->input('staff_id'));
    }

    $payments = $query->with('staff')->get();
    $staffList = Staff::orderBy('first_name')->orderBy('last_name')->get();

    return view('admin.staff.salary.payments_view', compact('payments', 'staff', 'staffList', 'start_date', 'end_date'));
}
    public function manageSalaryPayments(Request $request, ?Staff $staff = null)
    {
        $start_date = $request->input('start_date', Carbon::today()->subMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::today()->toDateString());

        $query = SalaryPayment::whereBetween('payment_date', [$start_date, $end_date]);

        if ($staff) {
            $query->where('staff_id', $staff->id);
        }

        $payments = $query->with('staff')->get();
        $staffList = Staff::with('salaryStructure')->orderBy('first_name')->orderBy('last_name')->get();
        $salaryStructures = $staffList->mapWithKeys(function ($member) {
            return [(string) $member->id => $member->salaryStructure
                ? $member->salaryStructure->only([
                    'basic_pay', 'da', 'hra', 'additional_earnings', 'epf',
                    'medical_deduction', 'additional_deductions', 'currency',
                ])
                : null];
        })->all();

        if ($request->isMethod('post')) {
            $request->merge([
                'currency' => 'INR',
                'manual_allowances' => 0,
                'manual_deductions' => 0,
            ]);
            $action = $request->input('submit_action', 'record_payment');
            $baseRules = array_merge([
                'staff_id' => 'required|exists:staff,id',
                'submit_action' => 'nullable|in:save_structure,record_payment',
            ], $this->salaryStructureRules());

            if ($action === 'save_structure') {
                $validated = $request->validate($baseRules);
                SalaryStructure::updateOrCreate(
                    ['staff_id' => $validated['staff_id']],
                    $this->salaryStructureData($validated)
                );

                return redirect()->route('admin.staff.salary.payments', $validated['staff_id'])
                    ->with('success', 'Salary details saved successfully.');
            }

            $validated = $request->validate(array_merge($baseRules, [
                'payment_date' => 'required|date',
                'status' => 'required|in:Paid,Pending,Failed',
                'month_year' => [
                    'required',
                    'date_format:Y-m',
                    Rule::unique('salary_payments')->where(fn ($query) => $query->where('staff_id', $request->input('staff_id'))),
                ],
                'remarks' => 'nullable|string',
                'manual_allowances' => 'nullable|numeric|min:0',
                'manual_deductions' => 'nullable|numeric|min:0',
            ]), [
                'month_year.unique' => 'A salary payment has already been recorded for this staff member and pay month.',
            ]);

            try {
                DB::transaction(function () use ($validated) {
                    $structure = SalaryStructure::updateOrCreate(
                        ['staff_id' => $validated['staff_id']],
                        $this->salaryStructureData($validated)
                    );
                    $calculated = $this->payrollCalculator->calculateForStructure(
                        $structure,
                        $validated['month_year'],
                        (float) ($validated['manual_allowances'] ?? 0),
                        (float) ($validated['manual_deductions'] ?? 0)
                    );
                    $details = $calculated['details'];

                    SalaryPayment::create([
                        'staff_id' => $validated['staff_id'],
                        'payment_date' => Carbon::parse($validated['payment_date'])->format('Y-m-d'),
                        'amount' => $calculated['amount'],
                        'currency' => $details['currency'],
                        'status' => $validated['status'],
                        'month_year' => $validated['month_year'],
                        'remarks' => $validated['remarks'] ?? null,
                        'manual_allowances' => $details['manual_allowances'],
                        'manual_deductions' => $details['manual_deductions'],
                        'basic_salary' => $details['basic_salary'],
                        'da' => $details['da'],
                        'hra' => $details['hra'],
                        'additional_earnings' => $details['additional_earnings'],
                        'epf' => $details['epf'],
                        'medical_deduction' => $details['medical_deduction'],
                        'additional_deductions' => $details['additional_deductions'],
                        'attendance_deduction' => $details['attendance_deduction'],
                        'total_income' => $details['total_income'],
                        'total_deductions' => $details['total_deductions'],
                        'net_salary' => $details['net_salary'],
                        'daily_rate' => $details['daily_rate'],
                        'working_days' => $details['working_days'],
                        'present_days' => $details['present_days'],
                        'absent_days' => $details['absent_days'],
                        'leave_days' => $details['leave_days'],
                        'paid_leave_days' => $details['paid_leave_days'],
                        'unpaid_leave_days' => $details['unpaid_leave_days'],
                        'holiday_days' => $details['holiday_days'],
                        'snapshot_version' => 1,
                    ]);
                });
            } catch (QueryException $exception) {
                if ((string) $exception->getCode() === '23000') {
                    throw ValidationException::withMessages([
                        'month_year' => 'A salary payment has already been recorded for this staff member and pay month.',
                    ]);
                }
                throw $exception;
            }

            return redirect()->route('admin.staff.salary.payments')->with('success', 'Salary payment recorded successfully.');
        }

        return view('admin.staff.salary.payments', compact('payments', 'staff', 'staffList', 'salaryStructures', 'start_date', 'end_date'));
    }

    public function generatePayslip(Request $request, Staff $staff, $month_year)
    {
        $payment = SalaryPayment::where('staff_id', $staff->id)->where('month_year', $month_year)->firstOrFail();
        $snapshot = $this->payrollCalculator->paymentSnapshot($payment);

        return view('admin.staff.salary.payslip', compact('staff', 'payment', 'month_year', 'snapshot'));
    }

    public function generateSalaryReport(Request $request)
    {
        $start_date = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->format('Y-m-d') : Carbon::today()->subMonth()->toDateString();
        $end_date = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->format('Y-m-d') : Carbon::today()->toDateString();
        $staff_id = $request->input('staff_id');

        $query = SalaryPayment::whereBetween('payment_date', [$start_date, $end_date]);

        if ($staff_id) {
            $query->where('staff_id', $staff_id);
        }

        $payments = $query->with('staff')->get();
        $staff = $staff_id ? Staff::withTrashed()->find($staff_id) : null;

        $payment_details = $payments->map(function ($payment) {
            return [
                'payment' => $payment,
                'details' => $this->payrollCalculator->paymentSnapshot($payment),
            ];
        });

        return view('admin.staff.salary.report', compact('payment_details', 'staff', 'start_date', 'end_date'));
    }

    public function exportSalaryCsv(Request $request)
    {
        $start_date = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->format('Y-m-d') : Carbon::today()->subMonth()->toDateString();
        $end_date = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->format('Y-m-d') : Carbon::today()->toDateString();
        $staff_id = $request->input('staff_id');

        $query = SalaryPayment::whereBetween('payment_date', [$start_date, $end_date]);
        if ($staff_id) {
            $query->where('staff_id', $staff_id);
        }

        $payments = $query->with('staff')->get();

        $filename = 'salary_report_' . ($staff_id ? $staff_id : 'all') . '_' . Carbon::parse($start_date)->format('Ymd') . '_to_' . Carbon::parse($end_date)->format('Ymd') . '.csv';
        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Payment Date', 'Staff ID', 'Name', 'Pay Month', 'Basic Salary', 'DA', 'HRA',
                'Additional Pay', 'Legacy Manual Earnings', 'Total Income', 'EPF',
                'Medical Deduction', 'Additional Deductions', 'Attendance Deduction',
                'Legacy Manual Deductions', 'Total Deductions', 'Net Salary', 'Currency',
                'Status', 'Present Days', 'Absent Days', 'Leave Days', 'Holiday Days', 'Record Type', 'Remarks',
            ]);

            foreach ($payments as $payment) {
                $details = $this->payrollCalculator->paymentSnapshot($payment);
                fputcsv($handle, [
                    $payment->payment_date->format('d-m-Y'),
                    $payment->staff?->staff_id ?? 'Archived #' . $payment->staff_id,
                    $payment->staff ? trim($payment->staff->first_name . ' ' . $payment->staff->last_name) : 'Staff record unavailable',
                    $payment->month_year,
                    $details['basic_salary'], $details['da'], $details['hra'], $details['additional_earnings'],
                    $details['manual_allowances'], $details['total_income'], $details['epf'],
                    $details['medical_deduction'], $details['additional_deductions'], $details['attendance_deduction'],
                    $details['manual_deductions'], $details['total_deductions'], $details['net_salary'],
                    $details['currency'], $payment->status, $details['present_days'] ?? null,
                    $details['absent_days'] ?? null, $details['leave_days'] ?? null,
                    $details['holiday_days'] ?? null, $details['legacy'] ? 'Legacy consolidated' : 'Component snapshot',
                    $payment->remarks ?? 'N/A',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
