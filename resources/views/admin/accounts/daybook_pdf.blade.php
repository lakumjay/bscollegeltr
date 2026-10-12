<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daybook Report</title>
    <style>
        @page { margin: 14mm 10mm 16mm; }
        * { box-sizing: border-box; }
        body { color: #20324a; font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 0; }
        .report-header { border-bottom: 2px solid #17365f; margin-bottom: 12px; padding-bottom: 10px; width: 100%; }
        .report-header td { vertical-align: middle; }
        .brand-logo { max-height: 54px; max-width: 135px; }
        .college-name { color: #17365f; font-size: 19px; font-weight: bold; margin: 0 0 3px; }
        .college-address { color: #596a80; font-size: 9px; line-height: 1.45; margin: 0; }
        .report-title { color: #17365f; font-size: 16px; font-weight: bold; margin: 0 0 4px; text-align: right; }
        .report-period { color: #6b778c; font-size: 9px; text-align: right; }
        .summary { border-collapse: separate; border-spacing: 5px 0; margin: 0 0 12px; table-layout: fixed; width: 100%; }
        .summary td { background: #f4f7fb; border: 1px solid #dae3ee; border-radius: 4px; padding: 8px 10px; }
        .summary span { color: #718096; display: block; font-size: 7px; font-weight: bold; letter-spacing: .5px; text-transform: uppercase; }
        .summary strong { color: #17365f; display: block; font-size: 13px; margin-top: 3px; }
        .report-table { border-collapse: collapse; table-layout: fixed; width: 100%; }
        .report-table thead { display: table-header-group; }
        .report-table tr { page-break-inside: avoid; }
        .report-table th { background: #17365f; border: 1px solid #17365f; color: #fff; font-size: 7.5px; letter-spacing: .25px; padding: 6px 5px; text-align: left; text-transform: uppercase; }
        .report-table td { border: 1px solid #dce4ed; padding: 5px; vertical-align: top; word-wrap: break-word; }
        .report-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .amount { font-size: 8px; font-weight: bold; text-align: right; white-space: nowrap; }
        .credit { color: #147a52; }
        .debit { color: #b23a48; }
        .empty { color: #718096; padding: 22px !important; text-align: center; }
        .report-footer { bottom: -10mm; color: #7b8798; font-size: 7.5px; left: 0; position: fixed; right: 0; text-align: center; }
    </style>
</head>
<body>
@php
    $collegeName = $profile?->college_name ?? config('app.name', 'College Management System');
    $collegeAddress = $profile?->address ?? '';
    $credits = $transactions->where('amount', '>', 0)->sum('amount');
    $debits = abs($transactions->where('amount', '<', 0)->sum('amount'));
    $net = $credits - $debits;
@endphp

<table class="report-header">
    <tr>
        <td style="width: 18%;">@if($logoData)<img src="{{ $logoData }}" class="brand-logo" alt="College logo">@endif</td>
        <td style="width: 52%;">
            <h1 class="college-name">{{ $collegeName }}</h1>
            @if($collegeAddress)<p class="college-address">{{ $collegeAddress }}</p>@endif
        </td>
        <td style="width: 30%;">
            <div class="report-title">Daybook Report</div>
            <div class="report-period">{{ \Carbon\Carbon::parse($fromDate)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d-m-Y') }}</div>
        </td>
    </tr>
</table>

<table class="summary">
    <tr>
        <td><span>Transactions</span><strong>{{ $transactions->count() }}</strong></td>
        <td><span>Total Credits</span><strong>INR {{ number_format($credits, 2) }}</strong></td>
        <td><span>Total Debits</span><strong>INR {{ number_format($debits, 2) }}</strong></td>
        <td><span>Net Balance</span><strong>INR {{ number_format($net, 2) }}</strong></td>
    </tr>
</table>

<table class="report-table">
    <thead>
        <tr>
            <th style="width: 18%;">Student Name</th>
            <th style="width: 14%;">Category</th>
            <th style="width: 10%;">Date</th>
            <th style="width: 9%;">Type</th>
            <th style="width: 13%; text-align: right;">Amount</th>
            <th style="width: 22%;">Short Description</th>
            <th style="width: 14%;">Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $transaction)
            <tr>
                <td>
                    <strong>{{ $transaction['student_name'] }}</strong>
                    @if(!empty($transaction['student_id']) && $transaction['student_id'] !== '-')
                        <br><span style="color:#64748b;font-size:7px;">ID: {{ $transaction['student_id'] }}</span>
                    @endif
                </td>
                <td>{{ $transaction['student_type'] ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d-m-Y') }}</td>
                <td>{{ $transaction['type'] }}</td>
                <td class="amount {{ $transaction['amount'] < 0 ? 'debit' : 'credit' }}">{{ $transaction['amount'] < 0 ? '-' : '+' }} INR {{ number_format(abs($transaction['amount']), 2) }}</td>
                <td>{{ $transaction['description'] ?: ($transaction['category'] ?: '-') }}</td>
                <td>{{ $transaction['user'] ?: '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">No transactions found for this date range.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="report-footer">{{ $collegeName }} | Generated {{ now()->format('d-m-Y, h:i A') }}</div>
</body>
</html>
