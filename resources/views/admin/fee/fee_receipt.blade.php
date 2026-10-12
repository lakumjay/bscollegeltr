<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

<div class="simple-receipt">
     @php 
if (!function_exists('convertNumberToWordsIndian')) {
    function convertNumberToWordsIndian($number) {
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

$brandAsset = $profile?->logo ?? $profile?->banner ?? null;
$logoUrl = $brandAsset && preg_match('/^(?:data:|https?:\/\/)/i', $brandAsset)
    ? $brandAsset
    : ($brandAsset && file_exists(public_path($brandAsset)) ? url($brandAsset) : asset('images/default-college-logo.svg'));
$collegeName = $profile?->college_name ?? 'Vananchal College';
$collegeAddress = $profile?->address ?? '';
$affiliation = $profile?->affiliation ?? 'Permanent Affiliated to Vinoba Bhave University, Hazaribag';

    @endphp
    <!-- Header matching CLC Certificate layout -->
    <header class="receipt-header">
        <div class="header-body-row">
            @if($logoUrl)
                <div class="college-logo-box">
                    <img class="college-logo" src="{{ $logoUrl }}" onerror="this.onerror=null;this.src='{{ asset('images/default-college-logo.svg') }}';" alt="{{ $collegeName }} emblem">
                </div>
            @else
                <div class="header-side-spacer"></div>
            @endif
            <div class="college-info">
                <h1 class="college-name">{{ $collegeName }}</h1>
                @if($collegeAddress)<p class="college-address">{{ $collegeAddress }}</p>@endif
                @if($affiliation)<p class="college-affiliation">{{ $affiliation }}</p>@endif
            </div>
            <div class="header-side-spacer"></div>
        </div>
    </header>

    <div class="receipt-title-cell">
        <h2 class="receipt-title">FEE RECEIPT</h2>
    </div>

    <!-- Student & Session Info -->
    <div class="row" style="margin-bottom: 25px;">
        <div class="col-6">
            <p style="margin: 0; font-size: 13px; color: #666;">STUDENT DETAILS</p>
            <p style="margin: 5px 0;"><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></p>
            <p style="margin: 3px 0;">Roll No: {{ $student->roll_no }}</p>
            <p style="margin: 3px 0;">Course: {{ $course->name }}</p>
        </div>
        <div class="col-6 text-right">
            <p style="margin: 0; font-size: 13px; color: #666;">RECEIPT DETAILS</p>
            <p style="margin: 5px 0;">Session: {{ optional(Auth::user()->session)->session_name ?? 'Current' }}</p>
            <p style="margin: 3px 0;">Date: {{ now()->format('d-m-Y') }}</p>
        </div>
    </div>

    <!-- Payment Table -->
    <table class="table table-bordered" style="margin-bottom: 20px;">
        <thead style="background: #f5f5f5;">
            <tr>
                            <th>Reciept Id</th>

                <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Payment Method</th>
                <th style="padding: 10px;">Transaction ID</th>
                <th style="padding: 10px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                            <th>#{{ str_pad($payment->id ?? 1, 5, '0', STR_PAD_LEFT) }}</th>

                <td style="padding: 10px;">{{ $payment->payment_date->format('d-m-Y') }}</td>
                <td style="padding: 10px;">{{ ucfirst($payment->payment_method) }}</td>
                <td style="padding: 10px;">{{ $payment->transaction_id ?? '-' }}</td>
                <td style="padding: 10px; text-align: right;">₹{{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div style="border: 1px solid #ddd; padding: 15px; background: #fafafa;">
        <div class="row" style="margin-bottom: 8px;">
            <div class="col-8 text-right"><strong>Total Fee:</strong></div>
            <div class="col-4 text-right">₹{{ number_format($total_fees, 2) }}</div>
        </div>
        <div class="row" style="margin-bottom: 8px;">
            <div class="col-8 text-right"><strong>Total Paid:</strong></div>
            <div class="col-4 text-right">₹{{ number_format($payments->sum('amount'), 2) }}</div>
        </div>
        <hr style="margin: 10px 0; border-top: 1px dashed #999;">
        <div class="row">
            <div class="col-8 text-right" style="font-size: 18px;"><strong>Due Amount:</strong></div>
            <div class="col-4 text-right" style="font-size: 18px; font-weight: bold;">₹{{ number_format($total_fees - $payments->sum('amount'), 2) }}</div>
        </div>
        <hr style="margin: 10px 0; border-top: 1px dashed #999;">

         <div class="row" style="margin-bottom: 8px;">
            <div class="col-4 text-right"><strong>Total Paid in Words:</strong></div>
            <div class="col-8 text-right">{{ convertNumberToWordsIndian($payments->sum('amount') )}} </div>
        </div>
    </div>

    <!-- Signature -->
    <div class="row" style="margin-top: 50px;">
        <div class="col-6">
            <div style="border-top: 1px solid #000; width: 150px; margin-top: 40px;"></div>
            <p style="margin-top: 5px; font-size: 13px;">Student/Parent</p>
        </div>
        <div class="col-6 text-right">
            <div style="border-top: 1px solid #000; width: 150px; margin-top: 40px; margin-left: auto;"></div>
            <p style="margin-top: 5px; font-size: 13px;">Head Accountant</p>
        </div>
    </div>

    <!-- Print Button -->
    <div class="text-center no-print" style="margin-top: 30px;">
        <button onclick="window.print()" class="btn btn-dark">Print Receipt</button>
    </div>
</div>

<style>
    .simple-receipt {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px 40px;
        background: white;
        border: 1px solid #ddd;
    }

    .receipt-header {
        border-bottom: 2px solid #111;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .header-body-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .college-logo-box {
        flex: 0 0 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .college-logo {
        width: 90px;
        height: 90px;
        max-height: 90px;
        max-width: 90px;
        padding: 3px;
        border: 1.5px solid #222;
        border-radius: 50%;
        object-fit: contain;
        background: #fff;
        display: block;
    }

    .college-info {
        flex: 1 1 auto;
        text-align: center;
        padding: 0 10px;
    }

    .college-name {
        margin: 0 0 4px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 24px;
        font-weight: 900;
        line-height: 1.15;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #111;
        text-align: center;
    }

    .college-address { 
        margin: 0; 
        font-size: 13.5px; 
        font-weight: 700; 
        letter-spacing: .3px;
        line-height: 1.25;
        color: #111;
    }

    .college-affiliation { 
        margin: 4px 0 0; 
        font-size: 12.5px; 
        font-weight: 600;
        font-style: italic; 
        color: #222;
    }

    .header-side-spacer {
        flex: 0 0 90px;
    }

    .receipt-title-cell {
        text-align: center;
        margin: 12px 0 18px;
    }

    .receipt-title {
        display: inline-block;
        margin: 0 auto;
        padding: 4px 18px;
        border: 2px solid #111;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: .5px;
        text-transform: uppercase;
        white-space: nowrap;
        color: #111;
    }

    .simple-receipt p {
        line-height: 1.6;
    }

    .simple-receipt .table {
        font-size: 14px;
    }

    .simple-receipt .table th {
        font-weight: 600;
        border-top: none;
    }

    .simple-receipt .table td,
    .simple-receipt .table th {
        border-color: #ddd;
    }

    @media print {
        body {
            margin: 0;
            padding: 0;
        }

        .simple-receipt {
            margin: 0;
            padding: 20px;
            border: none;
        }

        .no-print {
            display: none !important;
        }

        @page {
            margin: 1cm;
        }
    }
</style>
