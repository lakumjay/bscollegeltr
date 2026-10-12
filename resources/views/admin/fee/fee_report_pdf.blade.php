<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fee Payment Report</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #222; }

.header { text-align: center; border-bottom: 2px solid #1a1a6e; padding-bottom: 8px; margin-bottom: 10px; }
.header h2 { font-size: 16px; color: #1a1a6e; font-weight: 800; }
.header p  { font-size: 10px; color: #555; margin-top: 2px; }

.filters-row {
    background: #f0f0f8;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 6px 10px;
    margin-bottom: 10px;
    font-size: 9px;
    color: #444;
}
.filters-row span { margin-right: 14px; }
.filters-row strong { color: #1a1a6e; }

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9.5px;
    margin-bottom: 10px;
}
thead th {
    background: #1a1a6e;
    color: white;
    padding: 5px 6px;
    text-align: left;
    font-weight: 600;
}
tbody tr:nth-child(even) td { background: #f5f5fc; }
tbody td { padding: 4px 6px; border-bottom: 1px solid #e0e0e0; }
tbody td:last-child { text-align: right; }
tfoot td {
    background: #e8eaf6;
    font-weight: 700;
    padding: 5px 6px;
    border-top: 2px solid #1a1a6e;
}
tfoot td:last-child { text-align: right; }

.summary {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 9.5px;
}
.summary-box {
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 6px 12px;
    text-align: center;
    min-width: 18%;
}
.summary-box .label { color: #666; font-size: 8.5px; }
.summary-box .value { font-size: 13px; font-weight: 700; color: #1a1a6e; }

.footer {
    border-top: 1px solid #ccc;
    padding-top: 5px;
    font-size: 8px;
    color: #888;
    text-align: right;
}
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h2>{{ Auth::user()->profile->college_name ?? 'AKS College' }}</h2>
    <p>Fee Payment Report — {{ $course->name }} | Session: {{ $sessionName }}</p>
    <p>Generated: {{ now()->format('d-m-Y, h:i A') }}</p>
</div>

{{-- Active Filters --}}
@php
    $activeFilters = array_filter([
        'Fee Head'  => $filters['fee_head'] ?? '',
        'Method'    => $filters['payment_method'] ?? '',
        'From'      => $filters['date_from'] ?? '',
        'To'        => $filters['date_to'] ?? '',
        'Semester'  => !empty($filters['semester']) ? 'Sem ' . $filters['semester'] : '',
        'Student'   => $filters['student_name'] ?? '',
    ]);
    $totalAmount = $payments->sum('amount');
    $byMethod    = $payments->groupBy('payment_method');
@endphp

@if($activeFilters)
<div class="filters-row">
    <strong>Filters Applied:</strong>
    @foreach($activeFilters as $label => $value)
        <span><strong>{{ $label }}:</strong> {{ $value }}</span>
    @endforeach
</div>
@endif

{{-- Summary --}}
<table style="width:100%; margin-bottom:10px; font-size:9px;">
    <tr>
        <td style="background:#1a1a6e; color:white; padding:5px 8px; border-radius:3px; text-align:center; width:16%;">
            <div style="font-size:8px;">Total Records</div>
            <div style="font-size:14px; font-weight:700;">{{ $payments->count() }}</div>
        </td>
        <td style="width:2%;"></td>
        <td style="background:#28a745; color:white; padding:5px 8px; border-radius:3px; text-align:center; width:20%;">
            <div style="font-size:8px;">Total Collected</div>
            <div style="font-size:14px; font-weight:700;">Rs. {{ number_format($totalAmount, 2) }}</div>
        </td>
        <td style="width:2%;"></td>
        @foreach($byMethod as $method => $grp)
        <td style="background:#f0f0f8; padding:5px 8px; border-radius:3px; border:1px solid #ccc; text-align:center; width:16%;">
            <div style="font-size:8px; color:#555;">{{ $method }}</div>
            <div style="font-size:12px; font-weight:700; color:#1a1a6e;">Rs. {{ number_format($grp->sum('amount'), 2) }}</div>
            <div style="font-size:7.5px; color:#888;">{{ $grp->count() }} records</div>
        </td>
        <td style="width:1%;"></td>
        @endforeach
    </tr>
</table>

{{-- Data Table --}}
<table>
    <thead>
        <tr>
            <th style="width:3%;">#</th>
            <th style="width:10%;">Roll No</th>
            <th style="width:20%;">Student Name</th>
            <th style="width:18%;">Fee Head</th>
            <th style="width:9%;">Date</th>
            <th style="width:10%;">Method</th>
            <th style="width:15%;">Txn ID</th>
            <th style="width:10%; text-align:right;">Amount (Rs.)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $p->student->roll_no ?? '-' }}</td>
            <td>{{ trim(($p->student->first_name ?? '') . ' ' . ($p->student->last_name ?? '')) }}</td>
            <td>{{ $p->notes ?? '-' }}</td>
            <td>{{ $p->payment_date ? $p->payment_date->format('d-m-Y') : '-' }}</td>
            <td>{{ $p->payment_method }}</td>
            <td>{{ $p->transaction_id ?? '-' }}</td>
            <td>{{ number_format($p->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align:right;">Grand Total:</td>
            <td>Rs. {{ number_format($totalAmount, 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    {{ $course->name }} | {{ $sessionName }} | Printed on {{ now()->format('d-m-Y H:i') }}
</div>

</body>
</html>
