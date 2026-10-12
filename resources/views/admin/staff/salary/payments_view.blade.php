@extends('layouts.appadmin')

@section('content')
<div class="container-fluid premium-page salary-payments-history-page">
    <div class="premium-page-header"><div><span class="premium-page-eyebrow">Payroll</span><h1 class="premium-page-title">Salary Payment History</h1><p class="premium-page-subtitle">Search, review and export recorded staff payments.</p></div><div class="premium-header-actions"><a href="{{ route('admin.staff.salary.payments') }}" class="premium-btn premium-btn-gold"><i class="bx bx-plus-circle"></i> Add payment</a></div></div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="premium-card salary-payments-history-card">
                <div class="premium-card-header"><div><h2>Payment records {{ $staff ? 'for ' . $staff->first_name . ' ' . $staff->last_name : '' }}</h2><p>Use filters to narrow the payment history.</p></div></div>

                <div class="premium-card-body">
                     

                    <!-- Filter Form -->
                    <form method="get" action="{{ route('admin.staff.salary.payments_view', $staff) }}" class="premium-form-grid mb-4">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="start_date">Start Date</label>
                                <input type="text" name="start_date" id="start_date" class="form-control premium-datepicker" value="{{ $start_date ? \Carbon\Carbon::parse($start_date)->format('d-m-Y') : '' }}" autocomplete="off" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="end_date">End Date</label>
                                <input type="text" name="end_date" id="end_date" class="form-control premium-datepicker" value="{{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d-m-Y') : '' }}" autocomplete="off" required>
                            </div>
                            @if(!$staff)
                            <div class="col-md-3 form-group">
                                <label for="staff_id">Staff</label>
                                <select name="staff_id" id="staff_id" class="form-control">
                                    <option value="">All Staff</option>
                                    @foreach($staffList as $member)
                                        <option value="{{ $member->id }}" {{ request('staff_id') == $member->id ? 'selected' : '' }}>{{ $member->first_name }} {{ $member->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-3 form-group salary-payment-filter-action">
                                <button type="submit" class="premium-btn premium-btn-gold"><i class="bx bx-filter-alt"></i> Filter</button>

                            </div>
                        </div>
                        
                    </form>

                    <!-- Payments Table -->
                    <div class="salary-payments-table-area"><table class="table premium-table salary-payment-history-table js-premium-datatable">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Staff ID</th>
                                <th>Name</th>
                                <th>Total income</th>
                                <th>Total deductions</th>
                                <th>Net salary</th>
                                <th>Currency</th>
                                <th>Status</th>
                                <th>Month-Year</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            @php($member = $payment->staff)
                            <tr>
                                <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                                <td>{{ $member?->staff_id ?? 'Archived #' . $payment->staff_id }}</td>
                                <td>{{ $member ? trim($member->first_name . ' ' . $member->last_name) : 'Staff record unavailable' }}</td>
                                <td>{{ number_format((float) ($payment->total_income ?? $payment->amount), 2) }}</td>
                                <td>{{ number_format((float) ($payment->total_deductions ?? 0), 2) }}</td>
                                <td>{{ number_format((float) ($payment->net_salary ?? $payment->amount), 2) }}</td>
                                <td>{{ $payment->currency }}</td>
                                <td><span class="premium-status premium-status-{{ strtolower($payment->status) }}">{{ $payment->status }}</span></td>
                                <td>{{ $payment->month_year }}</td>
                                <td>{{ $payment->remarks ?? 'N/A' }}</td>
                                <td>
                                    @if($member)
                                        <a href="{{ route('admin.staff.salary.payslip', [$member, $payment->month_year]) }}" class="premium-btn premium-btn-outline">Payslip</a>
                                    @else
                                        <span class="text-muted">Unavailable</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @if($payments->isEmpty())
                                <tr>
                                    <td colspan="11" class="text-center">No salary payments found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table></div>
                    <div class="salary-payments-history-actions">
                        <a href="{{ route('admin.staff.salary.report', ['start_date' => $start_date, 'end_date' => $end_date, 'staff_id' => $staff ? $staff->id : request('staff_id')]) }}" class="premium-btn premium-btn-success"><i class="bx bx-file"></i> Generate report</a>
                        <a href="{{ route('admin.staff.salary.export_csv', ['start_date' => $start_date, 'end_date' => $end_date, 'staff_id' => $staff ? $staff->id : request('staff_id')]) }}" class="premium-btn premium-btn-outline"><i class="bx bx-download"></i> Export CSV</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
