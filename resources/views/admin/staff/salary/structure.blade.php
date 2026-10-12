@extends('layouts.appadmin')

@section('content')
<div class="premium-page salary-structure-page">
    <section class="premium-page-header">
        <div>
            <span class="premium-eyebrow">Payroll configuration</span>
            <h1>Salary structure</h1>
            <p>Set the recurring pay components for {{ trim($staff->first_name . ' ' . $staff->last_name) }}.</p>
        </div>
        <div class="salary-structure-staff-id"><span>Staff ID</span><strong>{{ $staff->staff_id }}</strong></div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <section class="premium-content-card salary-structure-card">
        <div class="premium-card-heading">
            <div><span class="premium-eyebrow">Monthly salary</span><h2>Pay components</h2></div>
            <i class="bx bx-wallet"></i>
        </div>

        <form method="post" action="{{ route('admin.staff.salary.structure', $staff) }}" class="salary-structure-form">
            @csrf
            <div class="salary-structure-grid">
                <div class="finance-field">
                    <label for="basic_pay">Basic pay <span>*</span></label>
                    <input type="number" name="basic_pay" id="basic_pay" min="0" step="0.01" value="{{ old('basic_pay', $structure?->basic_pay) }}" required>
                    @error('basic_pay')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="finance-field"><label for="da">Dearness allowance (DA)</label><input type="number" name="da" id="da" min="0" step="0.01" value="{{ old('da', $structure?->da ?? 0) }}">@error('da')<small class="text-danger">{{ $message }}</small>@enderror</div>
                <div class="finance-field"><label for="hra">House rent allowance (HRA)</label><input type="number" name="hra" id="hra" min="0" step="0.01" value="{{ old('hra', $structure?->hra ?? 0) }}">@error('hra')<small class="text-danger">{{ $message }}</small>@enderror</div>
                <div class="finance-field"><label for="additional_earnings">Additional Pay</label><input type="number" name="additional_earnings" id="additional_earnings" min="0" step="0.01" value="{{ old('additional_earnings', $structure?->additional_earnings ?? $structure?->allowances ?? 0) }}">@error('additional_earnings')<small class="text-danger">{{ $message }}</small>@enderror</div>
                <div class="finance-field"><label for="epf">EPF deduction</label><input type="number" name="epf" id="epf" min="0" step="0.01" value="{{ old('epf', $structure?->epf ?? 0) }}">@error('epf')<small class="text-danger">{{ $message }}</small>@enderror</div>
                <div class="finance-field"><label for="medical_deduction">Medical deduction</label><input type="number" name="medical_deduction" id="medical_deduction" min="0" step="0.01" value="{{ old('medical_deduction', $structure?->medical_deduction ?? 0) }}">@error('medical_deduction')<small class="text-danger">{{ $message }}</small>@enderror</div>
                <div class="finance-field"><label for="additional_deductions">Additional deductions</label><input type="number" name="additional_deductions" id="additional_deductions" min="0" step="0.01" value="{{ old('additional_deductions', $structure?->additional_deductions ?? $structure?->deductions ?? 0) }}">@error('additional_deductions')<small class="text-danger">{{ $message }}</small>@enderror</div>
                <div class="finance-field salary-currency-field">
                    <label for="currency">Currency</label>
                    <input type="text" name="currency" id="currency" minlength="3" maxlength="3" value="{{ old('currency', $structure?->currency ?? 'INR') }}" required>
                    @error('currency')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="finance-field salary-notes-field">
                    <label for="notes">Notes <small>Optional</small></label>
                    <textarea name="notes" id="notes" rows="3">{{ old('notes', $structure?->notes) }}</textarea>
                    @error('notes')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
            </div>
            <div class="premium-action-row salary-structure-actions">
                <button type="submit" class="premium-btn premium-btn-primary me-2"><i class="bx bx-save"></i> Save structure</button>
                <a href="{{ route('admin.staff.salary.payments', $staff) }}" class="premium-btn premium-btn-outline">Back to payment</a>
            </div>
        </form>
    </section>
</div>
@endsection
