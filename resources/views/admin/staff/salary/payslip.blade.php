@extends('layouts.appadmin')

@section('content')
@php
    $profile = $collegeProfile ?? \App\Models\Profile::first();
    $collegeName = $profile?->college_name ?? config('app.name', 'College Management');
    $collegeAddress = $profile?->address ?? '';
    $collegeContact = $profile?->contact_number ?? '';
    $collegeEmail = $profile?->email ?? '';
    $staffName = trim(collect([$staff->first_name, $staff->last_name])->filter()->implode(' '));
    $period = \Carbon\Carbon::createFromFormat('Y-m', $month_year)->format('F Y');
    $joiningDate = $staff->joining_date ? \Illuminate\Support\Carbon::parse($staff->joining_date)->format('d M Y') : 'N/A';

    $brandAsset = $profile?->banner ?? $profile?->logo ?? null;
    $collegeLogo = null;
    if ($brandAsset && file_exists(public_path($brandAsset))) {
        $mime = @mime_content_type(public_path($brandAsset)) ?: 'image/png';
        $collegeLogo = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents(public_path($brandAsset)));
    } elseif ($brandAsset) {
        $collegeLogo = url($brandAsset);
    } else {
        $collegeLogo = asset('images/default-college-logo.svg');
    }

    $details = $snapshot;
    $legacyPayment = (bool) ($details['legacy'] ?? false);
    $basicPay = (float) ($details['basic_salary'] ?? 0);
    $da = (float) ($details['da'] ?? 0);
    $hra = (float) ($details['hra'] ?? 0);
    $additionalEarnings = (float) ($details['additional_earnings'] ?? 0);
    $manualAllowances = (float) ($details['manual_allowances'] ?? 0);
    $totalEarnings = (float) ($details['total_income'] ?? $payment->amount);
    $epf = (float) ($details['epf'] ?? 0);
    $medical = (float) ($details['medical_deduction'] ?? 0);
    $additionalDeductions = (float) ($details['additional_deductions'] ?? 0);
    $attendanceDeduction = (float) ($details['attendance_deduction'] ?? 0);
    $manualDeductions = (float) ($details['manual_deductions'] ?? 0);
    $totalDeductions = (float) ($details['total_deductions'] ?? 0);
    $currency = $details['currency'] ?? 'INR';
    $netSalary = (float) ($details['net_salary'] ?? $payment->amount);
@endphp

<style>
.payslip-container {
    max-width: 900px;
    margin: 0 auto 2.5rem;
}
.payslip-card {
    background: #ffffff;
    border: 1px solid #dce4ed;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(18, 38, 63, 0.08);
    padding: 2.2rem 2.5rem;
    color: #1a2a3a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.payslip-header-grid {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 1.25rem;
    border-bottom: 2px solid #1a365d;
    margin-bottom: 1.25rem;
}
.payslip-institution {
    display: flex;
    align-items: center;
    gap: 1.2rem;
}
.payslip-logo-frame {
    max-width: 140px;
    max-height: 65px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.payslip-logo-frame img {
    max-width: 140px;
    max-height: 65px;
    object-fit: contain;
}
.payslip-inst-details h2 {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
    color: #1a365d;
    letter-spacing: 0.3px;
}
.payslip-inst-details p {
    margin: 2px 0 0;
    font-size: 0.8rem;
    color: #5a6a7e;
}
.payslip-badge-block {
    text-align: right;
}
.payslip-title-tag {
    display: inline-block;
    background: #1a365d;
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 4px;
    margin-bottom: 4px;
}
.payslip-period-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a365d;
    margin: 3px 0 0;
}
.payslip-date-sub {
    font-size: 0.75rem;
    color: #64748b;
    margin: 2px 0 0;
}
.payslip-meta-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.85rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.9rem 1.1rem;
    margin-bottom: 1.25rem;
}
.payslip-meta-item dt {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 2px;
}
.payslip-meta-item dd {
    margin: 0;
    font-size: 0.88rem;
    font-weight: 600;
    color: #1e293b;
}
.payslip-attendance-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.55rem 1rem;
    font-size: 0.76rem;
    color: #334155;
    margin-bottom: 1.25rem;
}
.payslip-attendance-bar strong {
    color: #0f172a;
}
.payslip-breakdown-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}
.payslip-table-box {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    overflow: hidden;
}
.payslip-table-box table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.84rem;
}
.payslip-table-box thead th {
    background: #1a365d;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 0.55rem 0.85rem;
}
.payslip-table-box thead th:last-child {
    text-align: right;
}
.payslip-table-box tbody td {
    padding: 0.5rem 0.85rem;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}
.payslip-table-box tbody td:last-child {
    text-align: right;
    font-weight: 600;
    color: #0f172a;
}
.payslip-table-box tbody tr:last-child td {
    border-bottom: 0;
}
.payslip-table-box tfoot td {
    background: #e2e8f0;
    font-weight: 700;
    color: #0f172a;
    padding: 0.6rem 0.85rem;
    border-top: 1px solid #cbd5e1;
    font-size: 0.86rem;
}
.payslip-table-box tfoot td:last-child {
    text-align: right;
    color: #1a365d;
}
.payslip-net-card {
    background: linear-gradient(135deg, #1a365d 0%, #0f172a 100%);
    color: #ffffff;
    border-radius: 6px;
    padding: 1.1rem 1.4rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.payslip-net-card .net-label-group h3 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: #f8fafc;
}
.payslip-net-card .net-label-group p {
    margin: 2px 0 0;
    font-size: 0.78rem;
    color: #94a3b8;
}
.payslip-net-card .net-amount {
    font-size: 1.6rem;
    font-weight: 800;
    color: #38bdf8;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.payslip-signatures-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-top: 3.2rem;
    margin-bottom: 1.2rem;
    text-align: center;
}
.payslip-sig-box {
    border-top: 1px solid #475569;
    padding-top: 0.45rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.payslip-footer-note {
    border-top: 1px dashed #cbd5e1;
    padding-top: 0.75rem;
    text-align: center;
    font-size: 0.7rem;
    color: #64748b;
}
@media print {
    body * {
        visibility: hidden;
    }
    .payslip-container, .payslip-container * {
        visibility: visible;
    }
    .payslip-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
    }
    .premium-page-header, .navbar, .sidebar-wrapper, .footer, .btn, .premium-btn {
        display: none !important;
    }
    .payslip-card {
        box-shadow: none !important;
        border: none !important;
        padding: 5mm 8mm !important;
    }
    .payslip-breakdown-grid, .payslip-table-box, .payslip-net-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>

<div class="container-fluid premium-page payslip-page">
    <div class="premium-page-header">
        <div>
            <span class="premium-page-eyebrow">Payroll</span>
            <h1 class="premium-page-title">Salary payslip</h1>
            <p class="premium-page-subtitle">Official payment statement for {{ $staffName }} ({{ $period }}).</p>
        </div>
        <div class="premium-header-actions">
            <a href="{{ route('admin.staff.salary.payments_view') }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Payment history</a>
            <button id="download-pdf" type="button" class="premium-btn premium-btn-gold"><i class="bx bx-download"></i> Download PDF</button>
            <button type="button" class="premium-btn premium-btn-primary js-print-payslip"><i class="bx bx-printer"></i> Print</button>
        </div>
    </div>

    <div class="payslip-container">
        <article id="payslip-content" class="payslip-card">
            <!-- Header -->
            <header class="payslip-header-grid">
                <div class="payslip-institution">
                    @if($collegeLogo)
                        <div class="payslip-logo-frame">
                            <img src="{{ $collegeLogo }}" alt="{{ $collegeName }} logo">
                        </div>
                    @endif
                    <div class="payslip-inst-details">
                        <h2>{{ $collegeName }}</h2>
                        @if($collegeAddress)<p>{{ $collegeAddress }}</p>@endif
                        @if($collegeContact || $collegeEmail)<p>@if($collegeContact)Phone: {{ $collegeContact }}@endif @if($collegeContact && $collegeEmail) • @endif @if($collegeEmail)Email: {{ $collegeEmail }}@endif</p>@endif
                    </div>
                </div>
                <div class="payslip-badge-block">
                    <span class="payslip-title-tag">Salary Payslip</span>
                    <div class="payslip-period-title">{{ $period }}</div>
                    <div class="payslip-date-sub">Payment Date: {{ $payment->payment_date->format('d M Y') }}</div>
                    <div class="mt-1"><span class="badge bg-{{ strtolower($payment->status) === 'paid' ? 'success' : 'warning' }} text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">{{ $payment->status }}</span></div>
                </div>
            </header>

            <!-- Employee Meta Grid -->
            <dl class="payslip-meta-grid">
                <div class="payslip-meta-item">
                    <dt>Staff Name</dt>
                    <dd>{{ $staffName }}</dd>
                </div>
                <div class="payslip-meta-item">
                    <dt>Staff ID</dt>
                    <dd>{{ $staff->staff_id }}</dd>
                </div>
                <div class="payslip-meta-item">
                    <dt>Designation / Role</dt>
                    <dd>{{ $staff->role ?: 'Staff member' }}</dd>
                </div>
                <div class="payslip-meta-item">
                    <dt>Joining Date</dt>
                    <dd>{{ $joiningDate }}</dd>
                </div>
            </dl>

            @if($legacyPayment)
                <div class="alert alert-info py-2">Legacy consolidated payment: the original final amount is preserved; component and attendance details were not stored for this record.</div>
            @endif

            <!-- Attendance Metrics Bar -->
            @if(isset($details['present_days']) || isset($details['absent_days']))
            <div class="payslip-attendance-bar">
                <span>Working Days: <strong>{{ $details['working_days'] ?? 22 }}</strong></span>
                <span>Present: <strong>{{ $details['present_days'] ?? 0 }}</strong></span>
                <span>Absent: <strong>{{ $details['absent_days'] ?? 0 }}</strong></span>
            </div>
            @endif

            <!-- Income vs Deductions 2-Column Table -->
            <div class="payslip-breakdown-grid">
                <!-- Income Table -->
                <div class="payslip-table-box">
                    <table>
                        <thead>
                            <tr>
                                <th>Income Component</th>
                                <th>Amount ({{ $currency }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($basicPay, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Dearness Allowance (DA)</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($da, 2) }}</td>
                            </tr>
                            <tr>
                                <td>House Rent Allowance (HRA)</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($hra, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Additional Pay</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($additionalEarnings, 2) }}</td>
                            </tr>
                            @if($manualAllowances != 0)
                            <tr>
                                <td>Legacy Manual Earnings</td>
                                <td>{{ number_format($manualAllowances, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Gross Payment</td>
                                <td>{{ $currency }} {{ number_format($totalEarnings, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Deductions Table -->
                <div class="payslip-table-box">
                    <table>
                        <thead>
                            <tr>
                                <th>Deduction Component</th>
                                <th>Amount ({{ $currency }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Employee Provident Fund (EPF)</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($epf, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Medical Deduction</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($medical, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Additional Deductions</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($additionalDeductions, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Attendance Deduction</td>
                                <td>{{ $legacyPayment ? 'Not stored' : number_format($attendanceDeduction, 2) }}</td>
                            </tr>
                            @if($manualDeductions != 0)
                            <tr>
                                <td>Legacy Manual Deductions</td>
                                <td>{{ number_format($manualDeductions, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Deductions</td>
                                <td>{{ $currency }} {{ number_format($totalDeductions, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Net Salary Card -->
            <div class="payslip-net-card">
                <div class="net-label-group">
                    <h3>Net Take Salary</h3>
                    <p>Status: {{ $payment->status }} @if($payment->remarks) • Remarks: {{ $payment->remarks }} @endif</p>
                </div>
                <div class="net-amount">
                    {{ $currency }} {{ number_format($netSalary, 2) }}
                </div>
            </div>

            <!-- Signatures Row -->
            <div class="payslip-signatures-row">
                <div class="payslip-sig-box">Employee Signature</div>
                <div class="payslip-sig-box">Prepared By (Accounts)</div>
                <div class="payslip-sig-box">Principal / Authorized Signatory</div>
            </div>

            <!-- Footer Note -->
            <div class="payslip-footer-note">
                Generated on {{ now()->format('d M Y, h:i A') }} • This is a computer-generated salary statement and requires no signature when verified electronically.
            </div>
        </article>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.querySelector('.js-print-payslip')?.addEventListener('click', function () {
    window.print();
});

document.getElementById('download-pdf')?.addEventListener('click', function () {
    const button = this;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Generating PDF...';

    const element = document.getElementById('payslip-content');
    html2canvas(element, { scale: 2, backgroundColor: '#ffffff', useCORS: true })
        .then(function (canvas) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
            const margin = 10;
            const pageWidth = doc.internal.pageSize.getWidth() - (margin * 2);
            const usableHeight = doc.internal.pageSize.getHeight() - (margin * 2);
            const sourcePageHeight = Math.floor(canvas.width * usableHeight / pageWidth);

            for (let offset = 0, page = 0; offset < canvas.height; offset += sourcePageHeight, page++) {
                const pageCanvas = document.createElement('canvas');
                pageCanvas.width = canvas.width;
                pageCanvas.height = Math.min(sourcePageHeight, canvas.height - offset);
                pageCanvas.getContext('2d').drawImage(canvas, 0, offset, canvas.width, pageCanvas.height, 0, 0, canvas.width, pageCanvas.height);
                if (page) doc.addPage();
                doc.addImage(pageCanvas.toDataURL('image/png'), 'PNG', margin, margin, pageWidth, pageCanvas.height * pageWidth / canvas.width);
            }
            doc.save('payslip_{{ $staff->staff_id }}_{{ $month_year }}.pdf');
        })
        .catch(function (error) {
            console.error('PDF export failed:', error);
            alert('Could not export PDF. Please try printing or refresh.');
        })
        .finally(function () {
            button.disabled = false;
            button.innerHTML = originalText;
        });
});
</script>
@endsection
