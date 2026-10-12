<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Fee Payment Receipt</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: #f0f0f0; font-family: 'Arial', sans-serif; }

.page-wrapper {
    width: 297mm;
    margin: 20px auto;
    background: white;
    display: flex;
    flex-direction: row;
    align-items: stretch;
}

/* ─── Each Receipt Copy ─── */
.receipt-copy {
    width: 146mm;
    flex-shrink: 0;
    padding: 10mm 11mm 8mm 11mm;
    position: relative;
}

.college-logo-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 2px solid #222;
    padding-bottom: 8px;
    margin-bottom: 8px;
    gap: 12px;
}
.college-logo-row img {
    height: 55px;
    max-width: 65%;
    object-fit: contain;
}
.receipt-title {
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #1a1a6e;
    margin-bottom: 3px;
}
.copy-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 10px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.student-badge { background: #007bff; color: white; }
.college-badge { background: #28a745; color: white; }

.meta-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin: 8px 0 6px 0;
    font-size: 10.5px;
}
.meta-box {
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 5px 8px;
    min-width: 48%;
}
.meta-box p { margin: 1px 0; line-height: 1.4; }
.meta-box strong { color: #222; }

table.fee-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5px;
    margin-bottom: 6px;
}
table.fee-table th {
    background: #1a1a6e;
    color: white;
    padding: 5px 7px;
    text-align: left;
    font-size: 10px;
    font-weight: 600;
}
table.fee-table td {
    padding: 4px 7px;
    border-bottom: 1px solid #e0e0e0;
    vertical-align: middle;
}
table.fee-table tr:nth-child(even) td { background: #f8f8fc; }
table.fee-table td:last-child { text-align: right; font-weight: 600; }
table.fee-table tfoot td {
    border-top: 2px solid #1a1a6e;
    background: #eef0ff;
    font-weight: 700;
    font-size: 11px;
}

.amount-words {
    background: #fff8e1;
    border: 1px dashed #f0ad4e;
    border-radius: 4px;
    padding: 5px 8px;
    font-size: 9.5px;
    color: #555;
    margin-bottom: 8px;
}
.amount-words strong { color: #222; font-size: 10px; }

.sig-row {
    display: flex;
    justify-content: space-between;
    margin-top: 12px;
    font-size: 9.5px;
}
.sig-box { text-align: center; width: 40%; }
.sig-line { border-top: 1px solid #333; margin-bottom: 3px; padding-top: 0; }

/* ─── Cutting Line (vertical between side-by-side copies) ─── */
.cutting-line {
    width: 5mm;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px 0;
    background: white;
}
.cutting-line::before,
.cutting-line::after {
    content: '';
    flex: 1;
    border-left: 2px dashed #999;
}
.scissors-icon {
    font-size: 16px;
    margin: 6px 0;
    color: #666;
    transform: rotate(90deg);
    user-select: none;
}

/* ─── No-print button ─── */
.no-print {
    text-align: center;
    padding: 15px;
    background: #f8f8f8;
}

/* ─── Print Styles ─── */
@media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }
    body { background: white; }
    .no-print { display: none !important; }
    .page-wrapper {
        margin: 0;
        box-shadow: none;
        width: 297mm;
        height: 210mm;
        display: flex;
        flex-direction: row;
    }
    .receipt-copy {
        width: 146mm;
        height: 210mm;
        overflow: hidden;
    }
    .cutting-line {
        width: 5mm;
        height: 210mm;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    table.fee-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>

@php
if (!function_exists('convertToWords')) {
    function convertToWords($number) {
        $number = (float) $number;
        if ($number <= 0) {
            return 'Zero Rupees Only';
        }

        $no = (int) floor($number);
        $point = (int) round(($number - $no) * 100);
        if ($point >= 100) {
            $no += 1;
            $point = 0;
        }

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
            50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        ];

        $convertUnderThousand = function ($num) use (&$convertUnderThousand, $words) {
            $str = '';
            if ($num >= 100) {
                $hundreds = (int) floor($num / 100);
                $str .= ($words[$hundreds] ?? '') . ' Hundred ';
                $num %= 100;
            }
            if ($num > 0) {
                if ($num < 20) {
                    $str .= $words[$num] ?? '';
                } else {
                    $tens = ((int) floor($num / 10)) * 10;
                    $units = $num % 10;
                    $str .= ($words[$tens] ?? '') . ($units ? ' ' . ($words[$units] ?? '') : '');
                }
            }
            return trim($str);
        };

        $crores = (int) floor($no / 10000000);
        $no %= 10000000;
        $lakhs = (int) floor($no / 100000);
        $no %= 100000;
        $thousands = (int) floor($no / 1000);
        $no %= 1000;
        $hundreds = $no;

        $parts = [];
        if ($crores > 0) {
            $parts[] = $convertUnderThousand($crores) . ' Crore';
        }
        if ($lakhs > 0) {
            $parts[] = $convertUnderThousand($lakhs) . ' Lakh';
        }
        if ($thousands > 0) {
            $parts[] = $convertUnderThousand($thousands) . ' Thousand';
        }
        if ($hundreds > 0) {
            $parts[] = $convertUnderThousand($hundreds);
        }

        $result = trim(implode(' ', $parts));
        if (empty($result)) {
            $result = 'Zero';
        }

        $paiseStr = '';
        if ($point > 0) {
            $paiseWord = $convertUnderThousand($point);
            if (!empty($paiseWord)) {
                $paiseStr = ' and ' . $paiseWord . ' Paise';
            }
        }

        return $result . ' Rupees' . $paiseStr . ' Only';
    }
}

$totalPaid    = $payments->sum('amount');
$receiptNo    = str_pad($payments->first()->id ?? 1, 6, '0', STR_PAD_LEFT);
$payDate      = $payments->first()->payment_date ? $payments->first()->payment_date->format('d-m-Y') : now()->format('d-m-Y');
$payMethod    = $payments->first()->payment_method ?? 'Cash';
$txnId        = $payments->first()->transaction_id ?? '-';
$sessionName  = optional(Auth::user()->session)->session_name ?? 'Current';
$brandAsset   = $profile?->banner ?? $profile?->logo ?? null;
$bannerUrl    = $brandAsset ? url($brandAsset) : '';
$copies = [
    ['label' => 'STUDENT COPY', 'class' => 'student-badge'],
    ['label' => 'COLLEGE COPY', 'class' => 'college-badge'],
];
@endphp

<div class="no-print mb-3 pt-3">
    <button onclick="window.print()" class="btn btn-dark px-5 py-2" style="font-size:14px;">
        🖨️ Print Receipt (2 Copies)
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary px-4 py-2 ml-2">← Back</a>
</div>

<div class="page-wrapper">

@foreach($copies as $copyIndex => $copy)

{{-- ═══════════════════ RECEIPT COPY ═══════════════════ --}}
<div class="receipt-copy">

    {{-- Header --}}
    <div class="college-logo-row">
        @if($bannerUrl)
            <img src="{{ $bannerUrl }}" alt="College Logo">
        @endif
        <div style="text-align:right;">
            <div class="receipt-title">Payment Receipt</div>
            <div><span class="copy-badge {{ $copy['class'] }}">{{ $copy['label'] }}</span></div>
            <div style="font-size:9px; margin-top:3px; color:#555;">Receipt No: <strong>{{ $receiptNo }}</strong></div>
        </div>
    </div>

    {{-- Student + Payment Info --}}
    <div class="meta-row">
        <div class="meta-box">
            <p><strong>Student:</strong> {{ Str::upper($student->first_name . ' ' . $student->last_name) }}</p>
            <p><strong>Father:</strong> {{ Str::upper($student->father_name) }}</p>
            <p><strong>Roll No:</strong> {{ $student->roll_no }}</p>
            <p><strong>Course:</strong> {{ $course->name }}</p>
            <p><strong>Semester:</strong> {{ $sem ?? '-' }}</p>
        </div>
        <div class="meta-box" style="text-align:right;">
            <p><strong>Receipt No:</strong> {{ $receiptNo }}</p>
            <p><strong>Date:</strong> {{ $payDate }}</p>
            <p><strong>Session:</strong> {{ $sessionName }}</p>
            <p><strong>Method:</strong> {{ $payMethod }}</p>
            @if($txnId && $txnId !== '-')
            <p><strong>Txn ID:</strong> {{ $txnId }}</p>
            @endif
        </div>
    </div>

    {{-- Fee Heads Table --}}
    <table class="fee-table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th>Fee Head</th>
                <th style="width:20%;">Method</th>
                <th style="width:22%; text-align:right;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $payment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payment->notes ?? 'Fee Payment' }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right; padding-right:10px;">Total Paid:</td>
                <td>₹{{ number_format($totalPaid, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Amount in words --}}
    <div class="amount-words">
        <strong>Amount in Words:</strong> {{ convertToWords($totalPaid) }}
    </div>

    {{-- Signatures --}}
    <div class="sig-row">
        <div class="sig-box">
            <div style="height: 28px;"></div>
            <div class="sig-line"></div>
            <div>Student / Parent Signature</div>
        </div>
        <div class="sig-box">
            <div style="height: 28px;"></div>
            <div class="sig-line"></div>
            <div>Cashier / Principal</div>
        </div>
    </div>

</div>
{{-- ═══════════════════ END COPY ═══════════════════ --}}

{{-- Cutting Line between copies --}}
@if($copyIndex === 0)
<div class="cutting-line">
    <span class="scissors-icon">✂</span>
</div>
@endif

@endforeach

</div>{{-- end page-wrapper --}}

<div class="no-print mt-3 pb-4">
    <button onclick="window.print()" class="btn btn-dark px-5 py-2" style="font-size:14px;">
        🖨️ Print Receipt (2 Copies)
    </button>
    <a href="javascript:history.back()" class="btn btn-secondary px-4 py-2 ml-2">← Back</a>
</div>

</body>
</html>
