<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

<div class="receipt-a4">

      @php 
    function convertNumberToWordsIndian($number) {
        $number = floatval($number); // FIX

        $no = floor($number);
        $point = round($number - $no, 2) * 100;

        $words = array(
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
            50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );

        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        $str = array();
        $i = 0;

        while ($no > 0) {
            $divider = ($i == 1) ? 10 : 100;
            $numberPart = $no % $divider;
            $no = floor($no / $divider);

            if ($numberPart) {
                if ($numberPart < 21) {
                    $str[] = $words[$numberPart] . " " . $digits[$i];
                } else {
                    $tens = floor($numberPart / 10) * 10;
                    $units = $numberPart % 10;
                    $str[] = $words[$tens] . " " . $words[$units] . " " . $digits[$i];
                }
            } else {
                $str[] = null;
            }
            $i++;
        }

        $result = implode(' ', array_reverse(array_filter($str)));

        if ($point > 0) {
            return trim($result) . " Rupees and " . $words[floor($point / 10) * 10] . " " . $words[$point % 10] . " Paise";
        } else {
            return trim($result) . " Rupees Only";
        }
    }
    @endphp

    <!-- TOP HEADER SECTION -->
    <div class="d-flex justify-content-between align-items-center header-box">
        <div>
            <h2 class="mb-1 font-weight-bold">PAYMENT RECEIPT</h2>
            <small class="text-muted">
                Receipt No: {{ str_pad($payments->first()->id ?? 1, 5, '0', STR_PAD_LEFT) }}
            </small>
        </div>

        <div class="text-right">
            <img src="{{ url(Auth::user()->profile->banner) }}"
                 style="max-height:70px;">
        </div>
    </div>

    <hr class="divider">

    <!-- STUDENT & RECEIPT INFO -->
    <div class="info-grid">
        <div class="info-card">
            <h6 class="title">STUDENT INFORMATION</h6>
            <p><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></p>
            <p>Roll No: {{ $student->roll_no }}</p>
            <p>Father: {{ $student->father_name }}</p>
            <p>Course: {{ $course->name }} <br>( @if($student->subjects)
                                        @php
                                            $subjectNames = \App\Models\Subject::whereIn('id', $student->subjects)
                                                ->whereHas('course', function ($query) use ($student) {
                                                    $query->where('id', $student->course);
                                                })
                                                ->pluck('name')
                                                ->toArray();
                                        @endphp
                                        {{ implode(', ', $subjectNames) ?: 'None' }}
                                    @else
                                        None
                                    @endif)</p>
        </div>

        <div class="info-card text-right">
            <h6 class="title">RECEIPT INFORMATION</h6>
            <p>Session: {{ \App\Models\Session::find(Auth::user()->active_session_id)->session_name ?? 'Current' }}</p>
            <p>Date: {{ now()->format('d-m-Y') }}</p>
        </div>
    </div>

    <!-- PAYMENT TABLE -->
    <table class="table table-bordered payment-table mt-4">
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Details</th>
                <th class="text-right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                <td>{{ ucfirst($payment->payment_method) }}</td>
                <td>{{ $payment->notes ?? '-' }}</td>
                <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTAL SUMMARY CARD -->
    <div class="summary-card mt-4">
        <div class="row">
            <div class="col-6">
                <h5 class="font-weight-bold">Total Paid</h5>
                <h2 class="amount">₹{{ number_format($payments->sum('amount'), 2) }}</h2>
            </div>
            <div class="col-6 text-right">
                <small class="text-muted">In Words</small>
                <p class="font-weight-bold">
                    {{ convertNumberToWordsIndian($payments->sum('amount')) }}
                </p>
            </div>
        </div>
    </div>

    <!-- SIGNATURE SECTION -->
    <div class="signature-grid mt-5">
        <div class="signature-box">
            <div class="line"></div>
            <p>Student / Parent</p>
        </div>

        <div class="signature-box text-right">
            <div class="line"></div>
            <p>Assistant</p>
        </div>
    </div>

    <div class="text-center no-print mt-4">
        <button onclick="window.print()" class="btn btn-dark px-4">Print Receipt</button>
    </div>
</div>

<style>
/* A4 WRAPPER */
.receipt-a4 {
    width: 100%;
    max-width: 900px;
    margin: auto;
    padding: 30px 40px;
    background: #fff;
    border: 1px solid #ddd;
}

/****** HEADER ******/
.header-box {
    padding-bottom: 10px;
}

.divider {
    border-top: 2px solid #222;
    margin: 15px 0 25px;
}

/****** INFO GRID ******/
.info-grid {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
}

.info-card {
    width: 48%;
}

.title {
    font-size: 12px;
    color: #666;
    font-weight: bold;
    letter-spacing: .5px;
    margin-bottom: 5px;
}

/****** TABLE STYLING ******/
.payment-table th {
    background: #f0f0f0;
    font-weight: 600;
    padding: 10px;
}

.payment-table td {
    padding: 10px;
}

/****** SUMMARY CARD ******/
.summary-card {
    border: 1px solid #ccc;
    padding: 20px;
    background: #fdfdfd;
    border-radius: 6px;
}

.summary-card .amount {
    font-size: 26px;
    margin-top: 5px;
}

/****** SIGNATURE ******/
.signature-grid {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
}

.signature-box {
    width: 40%;
}

.signature-box .line {
    height: 1px;
    background: #000;
    margin-bottom: 5px;
}

/****** PRINT MODE ******/
@media print {
    body {
        margin: 0;
        padding: 0;
        background: white;
    }

    .receipt-a4 {
        border: none;
        padding: 10mm;
        width: 100%;
    }

    @page {
        size: A4;
        margin: 10mm;
    }

    .no-print {
        display: none;
    }
}
</style>
