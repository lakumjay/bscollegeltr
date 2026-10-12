<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style>
    body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
    table { border-collapse: collapse; width: 100%; }
    th { background-color: #1a1a6e; color: white; font-weight: bold; padding: 6px 8px;
         border: 1px solid #999; text-align: left; }
    td { padding: 5px 8px; border: 1px solid #ddd; vertical-align: middle; }
    tr:nth-child(even) td { background-color: #f0f0f8; }
    .amt { text-align: right; font-weight: 600; }
    .header-cell { background-color: #1a1a6e; color: white; font-size: 14pt;
                   font-weight: bold; padding: 10px; text-align: center; border: none; }
    .sub-header  { font-size: 10pt; color: #555; text-align: center; padding: 4px; }
    .filter-row td { background-color: #eff0ff; font-size: 9pt; color: #333; }
    .total-row td  { background-color: #e8eaf6; font-weight: bold; border-top: 2px solid #1a1a6e; }
    .summary-header th { background-color: #28a745; font-size: 10pt; }
    .summary-row td    { background-color: #e8f5e9; font-size: 10pt; text-align: center; }
</style>
</head>
<body>

<table>
    {{-- College Header --}}
    <tr>
        <td colspan="8" class="header-cell">
            {{ Auth::user()->profile->college_name ?? 'AKS College, Japla' }}
        </td>
    </tr>
    <tr>
        <td colspan="8" class="sub-header">
            Fee Payment Report — {{ $course->name }}
            | Generated: {{ now()->format('d-m-Y, h:i A') }}
        </td>
    </tr>

    {{-- Applied Filters Row --}}
    @php
        $activeFilters = array_filter([
            'Fee Head'  => $filters['fee_head'] ?? '',
            'Method'    => $filters['payment_method'] ?? '',
            'Date From' => $filters['date_from'] ?? '',
            'Date To'   => $filters['date_to'] ?? '',
            'Semester'  => !empty($filters['semester']) ? 'Sem ' . $filters['semester'] : '',
            'Student'   => $filters['student_name'] ?? '',
        ]);
        $totalAmount = $payments->sum('amount');
        $byMethod    = $payments->groupBy('payment_method');
    @endphp
    @if($activeFilters)
    <tr class="filter-row">
        <td colspan="8">
            <b>Filters:</b>
            @foreach($activeFilters as $label => $val)
                {{ $label }}: {{ $val }} &nbsp;&nbsp;
            @endforeach
        </td>
    </tr>
    @endif

    {{-- Summary --}}
    <tr><td colspan="8" style="padding:4px;"></td></tr>
    <tr class="summary-header">
        <th colspan="2">Total Records</th>
        <th colspan="3">Total Collected</th>
        @foreach($byMethod as $method => $grp)
            <th>{{ $method }}</th>
        @endforeach
        @for($i = count($byMethod); $i < 3; $i++)<th></th>@endfor
    </tr>
    <tr class="summary-row">
        <td colspan="2" style="font-size:14pt; font-weight:bold; text-align:center;">
            {{ $payments->count() }}
        </td>
        <td colspan="3" style="font-size:14pt; font-weight:bold; text-align:center;">
            Rs. {{ number_format($totalAmount, 2) }}
        </td>
        @foreach($byMethod as $method => $grp)
            <td>Rs. {{ number_format($grp->sum('amount'), 2) }} ({{ $grp->count() }})</td>
        @endforeach
        @for($i = count($byMethod); $i < 3; $i++)<td></td>@endfor
    </tr>

    {{-- Spacer --}}
    <tr><td colspan="8" style="padding:4px;"></td></tr>

    {{-- Data Table Header --}}
    <tr>
        <th>#</th>
        <th>Roll No</th>
        <th>Student Name</th>
        <th>Fee Head</th>
        <th>Date</th>
        <th>Method</th>
        <th>Txn ID</th>
        <th class="amt">Amount (Rs.)</th>
    </tr>

    {{-- Data Rows --}}
    @foreach($payments as $i => $p)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $p->student->roll_no ?? '' }}</td>
        <td>{{ trim(($p->student->first_name ?? '') . ' ' . ($p->student->last_name ?? '')) }}</td>
        <td>{{ $p->notes ?? '' }}</td>
        <td>{{ $p->payment_date ? $p->payment_date->format('d-m-Y') : '' }}</td>
        <td>{{ $p->payment_method }}</td>
        <td>{{ $p->transaction_id ?? '' }}</td>
        <td class="amt">{{ number_format($p->amount, 2) }}</td>
    </tr>
    @endforeach

    {{-- Grand Total --}}
    <tr class="total-row">
        <td colspan="7" style="text-align:right;">Grand Total</td>
        <td class="amt">Rs. {{ number_format($totalAmount, 2) }}</td>
    </tr>
</table>

</body>
</html>
