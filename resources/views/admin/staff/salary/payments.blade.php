@extends('layouts.appadmin')

@section('content')
<?php $selectedStructure = $staff ? optional($staffList->firstWhere('id', $staff->id))->salaryStructure : null; ?>
<div class="container-fluid premium-page salary-payment-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Payroll</span><h1>Salary Payment</h1><p>Edit the salary components, review the attendance-adjusted calculation, and record the payment.</p></div>
        <div class="premium-header-actions">
            <a href="{{ route('admin.staff.salary.payments_view') }}" class="premium-btn premium-btn-gold"><i class="bx bx-list-ul"></i> Payment history</a>
        </div>
    </section>
    <section class="premium-content-card salary-payment-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Payment details</span><h2>{{ $staff ? 'Record payment for ' . trim($staff->first_name . ' ' . $staff->last_name) : 'Record a staff payment' }}</h2></div><i class="bx bx-credit-card"></i></div>
        <div class="salary-payment-card-body">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            <form method="post" action="{{ route('admin.staff.salary.payments') }}" class="salary-payment-form">
                @csrf
                <div class="salary-payment-grid">
                    <div class="finance-field"><label for="staff_id">Staff member <span>*</span></label><select name="staff_id" id="staff_id" required><option value="">Select staff</option>@foreach($staffList as $member)<option value="{{ $member->id }}" {{ (string) old('staff_id', optional($staff)->id) === (string) $member->id ? 'selected' : '' }}>{{ trim($member->first_name . ' ' . $member->last_name) }} ({{ $member->staff_id }})</option>@endforeach</select>@error('staff_id')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="month_year">Pay month <span>*</span></label><input type="month" name="month_year" id="month_year" value="{{ old('month_year', now()->format('Y-m')) }}" required>@error('month_year')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="payment_date">Payment date <span>*</span></label><input type="text" name="payment_date" id="payment_date" class="premium-datepicker" value="{{ old('payment_date', now()->format('d-m-Y')) }}" autocomplete="off" required>@error('payment_date')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="basic_pay">Basic Salary <span>*</span></label><input type="number" name="basic_pay" id="basic_pay" min="0" step="0.01" value="{{ old('basic_pay', optional($selectedStructure)->basic_pay ?? 0) }}" required>@error('basic_pay')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="da">DA <span>*</span></label><input type="number" name="da" id="da" min="0" step="0.01" value="{{ old('da', optional($selectedStructure)->da ?? 0) }}" required>@error('da')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="hra">HRA <span>*</span></label><input type="number" name="hra" id="hra" min="0" step="0.01" value="{{ old('hra', optional($selectedStructure)->hra ?? 0) }}" required>@error('hra')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="additional_earnings">Additional Pay <span>*</span></label><input type="number" name="additional_earnings" id="additional_earnings" min="0" step="0.01" value="{{ old('additional_earnings', optional($selectedStructure)->additional_earnings ?? 0) }}" required>@error('additional_earnings')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="epf">EPF <span>*</span></label><input type="number" name="epf" id="epf" min="0" step="0.01" value="{{ old('epf', optional($selectedStructure)->epf ?? 0) }}" required>@error('epf')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="medical_deduction">Medical Deduction <span>*</span></label><input type="number" name="medical_deduction" id="medical_deduction" min="0" step="0.01" value="{{ old('medical_deduction', optional($selectedStructure)->medical_deduction ?? 0) }}" required>@error('medical_deduction')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="additional_deductions">Additional Deductions <span>*</span></label><input type="number" name="additional_deductions" id="additional_deductions" min="0" step="0.01" value="{{ old('additional_deductions', optional($selectedStructure)->additional_deductions ?? 0) }}" required>@error('additional_deductions')<small class="text-danger">{{ $message }}</small>@enderror</div>
                    <div class="finance-field"><label for="amount">Net Payment</label><input type="number" id="amount" step="0.01" value="" readonly><small>Calculated and verified by the server when saved.</small></div>
                    <div class="finance-field"><label for="status">Status <span>*</span></label><select name="status" id="status" required><option value="Paid" {{ old('status', 'Paid') === 'Paid' ? 'selected' : '' }}>Paid</option><option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option><option value="Failed" {{ old('status') === 'Failed' ? 'selected' : '' }}>Failed</option></select></div>
                    <div class="finance-field salary-remarks-field"><label for="remarks">Remarks <small>Optional</small></label><textarea name="remarks" id="remarks" rows="3">{{ old('remarks') }}</textarea></div>
                </div>
                <section id="salary-component-preview" class="salary-component-preview" hidden>
                    <div class="salary-component-preview-heading"><div><span class="premium-eyebrow">Server calculation</span><h3>Complete salary breakdown</h3></div><span id="preview-currency" class="salary-preview-currency">INR</span></div>
                    <div class="salary-component-columns">
                        <article class="salary-component-group salary-component-group--earnings">
                            <h4><i class="bx bx-trending-up"></i> Income</h4>
                            <dl>
                                <div><dt>Basic Salary</dt><dd id="preview-basic-salary">INR 0.00</dd></div>
                                <div><dt>DA</dt><dd id="preview-da">INR 0.00</dd></div>
                                <div><dt>HRA</dt><dd id="preview-hra">INR 0.00</dd></div>
                                <div><dt>Additional Pay</dt><dd id="preview-additional-earnings">INR 0.00</dd></div>
                            </dl>
                        </article>
                        <article class="salary-component-group salary-component-group--deductions">
                            <h4><i class="bx bx-trending-down"></i> Deductions</h4>
                            <dl>
                                <div><dt>EPF</dt><dd id="preview-epf">INR 0.00</dd></div>
                                <div><dt>Medical Deduction</dt><dd id="preview-medical-deduction">INR 0.00</dd></div>
                                <div><dt>Additional Deductions</dt><dd id="preview-additional-deductions">INR 0.00</dd></div>
                                <div><dt>Attendance Deduction</dt><dd id="preview-attendance-deduction">INR 0.00</dd></div>
                            </dl>
                        </article>
                    </div>
                    <div class="salary-component-totals">
                        <div><span>Total Income</span><strong id="preview-total-income">INR 0.00</strong></div>
                        <div><span>Total Deductions</span><strong id="preview-total-deductions">INR 0.00</strong></div>
                        <div class="salary-component-net"><span>Net Salary</span><strong id="preview-net-salary">INR 0.00</strong></div>
                    </div>
                </section>
                <section id="attendance-summary" class="salary-attendance-summary" hidden><div><span>Present days</span><strong id="present-days">0</strong></div><div><span>Absent days</span><strong id="absent-days">0</strong></div><div><span>Leave days</span><strong id="leave-days">0</strong></div><div><span>Paid leaves</span><strong id="paid-leave-days">0</strong></div><div><span>Unpaid leaves</span><strong id="unpaid-leave-days">0</strong></div><div><span>Holiday days</span><strong id="holiday-days">0</strong></div><div><span>Daily rate</span><strong id="daily-rate">INR 0.00</strong></div></section>
                <div id="salary-error" class="alert alert-warning mt-3" hidden></div>
                <div class="premium-action-row salary-payment-actions">
                    <span class="salary-save-note">Salary component changes are saved with the payment.</span>
                    <button type="submit" name="submit_action" value="save_structure" class="premium-btn premium-btn-outline"><i class="bx bx-save"></i> Save salary details</button>
                    <button type="submit" name="submit_action" value="record_payment" class="premium-btn premium-btn-primary"><i class="bx bx-check-circle"></i> Record payment</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const staff = document.getElementById('staff_id'), month = document.getElementById('month_year'), amount = document.getElementById('amount'), preview = document.getElementById('salary-component-preview'), summary = document.getElementById('attendance-summary'), error = document.getElementById('salary-error');
    const componentFields = ['basic_pay', 'da', 'hra', 'additional_earnings', 'epf', 'medical_deduction', 'additional_deductions'];
    const structures = @json($salaryStructures);
    let refreshTimer;
    const setError = (message = '') => { error.textContent = message; error.hidden = !message; };
    const currencyCode = () => 'INR';
    const setMoney = (id, value) => { document.getElementById(id).textContent = currencyCode() + ' ' + (Number(value) || 0).toFixed(2); };
    const populateStructure = () => {
        const structure = structures[staff.value];
        componentFields.forEach((field) => {
            document.getElementById(field).value = structure?.[field] ?? 0;
        });
    };
    async function loadStructureAmount() {
        if (!staff.value || !month.value) { amount.value = ''; preview.hidden = true; summary.hidden = true; setError(); return; }
        setError();
        try {
            const salaryComponents = Object.fromEntries(componentFields.map((field) => [field, document.getElementById(field).value || 0]));
            const response = await fetch('{{ route('admin.staff.salary.calculate') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ staff_id: staff.value, month_year: month.value, currency: 'INR', ...salaryComponents }) });
            const result = await response.json();
            if (!response.ok || result.details?.error) {
                const validationError = Object.values(result.errors || {})[0]?.[0];
                throw new Error(result.details?.error || validationError || result.message || 'Unable to calculate the salary.');
            }
            amount.value = (Number(result.details.net_salary) || 0).toFixed(2);
            document.getElementById('preview-currency').textContent = result.details.currency || currencyCode();
            setMoney('preview-basic-salary', result.details.basic_salary);
            setMoney('preview-da', result.details.da);
            setMoney('preview-hra', result.details.hra);
            setMoney('preview-additional-earnings', result.details.additional_earnings);
            setMoney('preview-epf', result.details.epf);
            setMoney('preview-medical-deduction', result.details.medical_deduction);
            setMoney('preview-additional-deductions', result.details.additional_deductions);
            setMoney('preview-attendance-deduction', result.details.attendance_deduction);
            setMoney('preview-total-income', result.details.total_income);
            setMoney('preview-total-deductions', result.details.total_deductions);
            setMoney('preview-net-salary', result.details.net_salary);
            document.getElementById('present-days').textContent = result.details.present_days ?? 0;
            document.getElementById('absent-days').textContent = result.details.absent_days ?? 0;
            document.getElementById('leave-days').textContent = result.details.leave_days ?? 0;
            document.getElementById('paid-leave-days').textContent = result.details.paid_leave_days ?? 0;
            document.getElementById('unpaid-leave-days').textContent = result.details.unpaid_leave_days ?? 0;
            document.getElementById('holiday-days').textContent = result.details.holiday_days ?? 0;
            setMoney('daily-rate', result.details.daily_rate);
            preview.hidden = false;
            summary.hidden = false;
        } catch (exception) { amount.value = ''; preview.hidden = true; summary.hidden = true; setError(exception.message || 'Unable to calculate the salary.'); }
    }
    const queueCalculation = () => { clearTimeout(refreshTimer); refreshTimer = setTimeout(loadStructureAmount, 250); };
    staff.addEventListener('change', () => { populateStructure(); loadStructureAmount(); });
    month.addEventListener('change', loadStructureAmount);
    componentFields.map((field) => document.getElementById(field)).forEach((field) => field.addEventListener('input', queueCalculation));
    if (staff.value) loadStructureAmount();
});
</script>
@endsection
