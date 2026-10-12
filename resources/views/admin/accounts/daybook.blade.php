@extends('layouts.appadmin')

@section('content')
<div class="premium-page accounts-page">
    <section class="premium-page-header"><div><span class="premium-eyebrow">Accounts</span><h1>Daybook</h1><p>Review every income, expense, and fee transaction for a selected date range.</p></div><div class="premium-header-actions"><a href="{{ route('accounts.daybook.export_csv', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="premium-btn premium-btn-outline"><i class="bx bx-download"></i> Export CSV</a><a href="{{ route('accounts.daybook.export_pdf', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="premium-btn premium-btn-danger"><i class="bx bxs-file-pdf"></i> Download PDF</a></div></section>

    <section class="premium-content-card finance-filter-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Date range</span><h2>Filter transactions</h2></div><i class="fa fa-calendar"></i></div>
        <form method="GET" action="{{ route('accounts.daybook') }}" class="daybook-filter-form">
            <div class="finance-field"><label for="from_date">From date <span>*</span></label><input id="from_date" name="from_date" type="text" class="form-control premium-datepicker" value="{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }}" required></div>
            <div class="finance-field"><label for="to_date">To date <span>*</span></label><input id="to_date" name="to_date" type="text" class="form-control premium-datepicker" value="{{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}" required></div>
            <button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-filter"></i> Apply filter</button>
        </form>
    </section>

    @php
        $incomeTotal = collect($transactions)->where('amount', '>', 0)->sum('amount');
        $expenseTotal = abs(collect($transactions)->where('amount', '<', 0)->sum('amount'));
    @endphp
    <section class="finance-summary-grid daybook-summary-grid"><article><span>Transactions</span><strong>{{ count($transactions) }}</strong><small>{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</small></article><article><span>Credits</span><strong>₹{{ number_format($incomeTotal, 2) }}</strong><small>Income and collections</small></article><article><span>Debits</span><strong>₹{{ number_format($expenseTotal, 2) }}</strong><small>Recorded expenses</small></article></section>

    <section class="premium-content-card accounts-ledger-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Transaction register</span><h2>Daybook entries</h2></div></div>
        <div class="premium-table-wrap">
            <table class="premium-table" id="example2">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Category</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th>Short Description</th>
                        <th>Recorded by</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            <strong>{{ $transaction['student_name'] }}</strong>
                            @if(!empty($transaction['student_id']) && $transaction['student_id'] !== '-')
                                <small class="text-muted d-block" style="font-size:0.75rem;">ID: {{ $transaction['student_id'] }}</small>
                            @endif
                        </td>
                        <td>
                            @if(!empty($transaction['student_type']) && $transaction['student_type'] !== '-')
                                <span class="badge bg-light text-dark border">{{ $transaction['student_type'] }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d-m-Y') }}</td>
                        <td><span class="daybook-type-badge daybook-type-badge--{{ strtolower($transaction['type']) }}">{{ $transaction['type'] }}</span></td>
                        <td class="text-end {{ $transaction['amount'] < 0 ? 'accounts-expense-amount' : 'finance-amount' }}">{{ $transaction['amount'] < 0 ? '-' : '+' }}₹{{ number_format(abs($transaction['amount']), 2) }}</td>
                        <td>{{ $transaction['description'] ?: ($transaction['category'] ?: '-') }}</td>
                        <td>{{ $transaction['user'] ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No transactions found in this date range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
