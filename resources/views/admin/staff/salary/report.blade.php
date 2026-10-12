@extends('layouts.appadmin')

@section('content')
@php
    $collegeName = $collegeProfile?->college_name ?? config('app.name', 'College Management');
    $staffLabel = $staff
        ? trim($staff->first_name . ' ' . $staff->last_name) . ' (' . $staff->staff_id . ')'
        : 'All staff members';
@endphp

<div class="premium-page salary-report-page">
    <section class="premium-page-header no-print">
        <div>
            <span class="premium-eyebrow">Payroll</span>
            <h1>Salary payment report</h1>
            <p>Review payment, attendance, and deduction details for the selected period.</p>
        </div>
        <div class="premium-header-actions">
            <a class="premium-btn premium-btn-outline" href="{{ route('admin.staff.salary.payments_view', $staff) }}"><i class="fa fa-arrow-left"></i> Back to payments</a>
        </div>
    </section>

    <section id="report-content" class="salary-report-sheet">
        <header class="salary-report-sheet__header">
            <div>
                <span class="premium-eyebrow">Payroll statement</span>
                <h2>{{ $collegeName }}</h2>
                <p>Salary Payment Report</p>
            </div>
            <div class="salary-report-sheet__period">
                <span>Reporting period</span>
                <strong>{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</strong>
                <small>{{ $staffLabel }}</small>
            </div>
        </header>

        <div class="salary-report-summary">
            <div><span>Payment records</span><strong>{{ $payment_details->count() }}</strong></div>
            <div><span>Total paid</span><strong>{{ number_format($payment_details->where('payment.status', 'Paid')->sum(fn ($item) => $item['details']['net_salary'] ?? 0), 2) }}</strong></div>
            <div><span>Attendance deduction</span><strong>{{ number_format($payment_details->sum(fn ($item) => $item['details']['attendance_deduction'] ?? 0), 2) }}</strong></div>
        </div>

        @forelse($payment_details as $item)
            @php
                $payment = $item['payment'];
                $details = $item['details'];
                $staffMember = $payment->staff;
            @endphp
            <article class="salary-report-entry">
                <div class="salary-report-entry__identity">
                    <div class="salary-report-entry__avatar">{{ strtoupper(substr($staffMember->first_name ?? 'S', 0, 1)) }}</div>
                    <div>
                        <strong>{{ trim(($staffMember->first_name ?? '') . ' ' . ($staffMember->last_name ?? '')) ?: 'Staff member' }}</strong>
                        <span>{{ $staffMember?->staff_id ?? 'Archived #' . $payment->staff_id }}</span>
                    </div>
                    <span class="salary-report-status salary-report-status--{{ strtolower($payment->status) }}">{{ $payment->status }}</span>
                </div>

                <dl class="salary-report-entry__details">
                    <div><dt>Payment date</dt><dd>{{ optional($payment->payment_date)->format('d M Y') }}</dd></div>
                    <div><dt>Pay month</dt><dd>{{ $payment->month_year }}</dd></div>
                    <div><dt>Basic salary</dt><dd>{{ is_null($details['basic_salary']) ? 'Legacy N/A' : number_format($details['basic_salary'], 2) }}</dd></div>
                    <div><dt>DA / HRA</dt><dd>{{ is_null($details['da']) ? 'Legacy N/A' : number_format($details['da'], 2) . ' / ' . number_format($details['hra'], 2) }}</dd></div>
                    <div><dt>Additional Pay</dt><dd>{{ is_null($details['additional_earnings']) ? 'Legacy N/A' : number_format($details['additional_earnings'], 2) }}</dd></div>
                    @if((float) ($details['manual_allowances'] ?? 0) !== 0.0)<div><dt>Legacy manual earnings</dt><dd>{{ number_format($details['manual_allowances'], 2) }}</dd></div>@endif
                    <div><dt>Total income</dt><dd>{{ $details['currency'] }} {{ number_format($details['total_income'] ?? 0, 2) }}</dd></div>
                    <div><dt>EPF / Medical</dt><dd>{{ is_null($details['epf']) ? 'Legacy N/A' : number_format($details['epf'], 2) . ' / ' . number_format($details['medical_deduction'], 2) }}</dd></div>
                    <div><dt>Additional deductions</dt><dd>{{ is_null($details['additional_deductions']) ? 'Legacy N/A' : number_format($details['additional_deductions'], 2) }}</dd></div>
                    @if((float) ($details['manual_deductions'] ?? 0) !== 0.0)<div><dt>Legacy manual deductions</dt><dd>{{ number_format($details['manual_deductions'], 2) }}</dd></div>@endif
                    <div><dt>Total deductions</dt><dd class="is-deduction">{{ $details['currency'] }} {{ number_format($details['total_deductions'] ?? 0, 2) }}</dd></div>
                    <div><dt>Net salary</dt><dd class="is-amount">{{ $details['currency'] }} {{ number_format($details['net_salary'] ?? $payment->amount, 2) }}</dd></div>
                    <div><dt>Present days</dt><dd>{{ $details['present_days'] ?? 0 }}</dd></div>
                    <div><dt>Absent days</dt><dd>{{ $details['absent_days'] ?? 0 }}</dd></div>
                    <div><dt>Leave days</dt><dd>{{ $details['leave_days'] ?? 0 }}</dd></div>
                    <div><dt>Holiday days</dt><dd>{{ $details['holiday_days'] ?? 0 }}</dd></div>
                    <div><dt>Attendance deduction</dt><dd class="is-deduction">{{ is_null($details['attendance_deduction']) ? 'Legacy N/A' : $details['currency'] . ' ' . number_format($details['attendance_deduction'], 2) }}</dd></div>
                    <div><dt>Record type</dt><dd>{{ $details['legacy'] ? 'Legacy consolidated' : 'Component snapshot' }}</dd></div>
                </dl>

                @if(filled($payment->remarks))
                    <p class="salary-report-entry__remarks"><strong>Remarks:</strong> {{ $payment->remarks }}</p>
                @endif
            </article>
        @empty
            <div class="premium-empty-state">
                <i class="fa fa-wallet"></i>
                <h3>No salary payments found</h3>
                <p>There are no payment records for the selected period.</p>
            </div>
        @endforelse

        <footer class="salary-report-sheet__footer">Generated on {{ now()->format('d M Y, h:i A') }} · This is a system-generated report.</footer>
    </section>

    <div class="premium-action-row no-print">
        <button id="download-pdf" type="button" class="premium-btn premium-btn-primary"><i class="fa fa-download"></i> Download PDF</button>
        <a class="premium-btn premium-btn-gold" href="{{ route('admin.staff.salary.export_csv', ['start_date' => $start_date, 'end_date' => $end_date, 'staff_id' => $staff?->id ?? request('staff_id')]) }}"><i class="fa fa-file-text-o"></i> Export CSV</a>
        <a class="premium-btn premium-btn-outline" href="{{ route('admin.staff.salary.payments_view', $staff) }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById('download-pdf').addEventListener('click', async function () {
    const button = this;
    const report = document.getElementById('report-content');
    const originalLabel = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Preparing PDF…';

    try {
        const canvas = await html2canvas(report, { scale: 2, useCORS: true, backgroundColor: '#ffffff', windowWidth: report.scrollWidth, windowHeight: report.scrollHeight });
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const margin = 9;
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const usableHeight = pageHeight - (margin * 2);
        const imageWidth = pageWidth - (margin * 2);
        const sourcePageHeight = Math.floor(canvas.width * usableHeight / imageWidth);

        for (let offset = 0, page = 0; offset < canvas.height; offset += sourcePageHeight, page++) {
            const pageCanvas = document.createElement('canvas');
            pageCanvas.width = canvas.width;
            pageCanvas.height = Math.min(sourcePageHeight, canvas.height - offset);
            pageCanvas.getContext('2d').drawImage(canvas, 0, offset, canvas.width, pageCanvas.height, 0, 0, canvas.width, pageCanvas.height);
            if (page) pdf.addPage();
            pdf.addImage(pageCanvas.toDataURL('image/jpeg', 0.94), 'JPEG', margin, margin, imageWidth, pageCanvas.height * imageWidth / canvas.width, undefined, 'FAST');
        }

        pdf.save('salary_report_{{ $staff?->staff_id ?? 'all' }}_{{ \Carbon\Carbon::parse($start_date)->format('Ymd') }}_to_{{ \Carbon\Carbon::parse($end_date)->format('Ymd') }}.pdf');
    } catch (error) {
        console.error('Salary report PDF generation failed:', error);
        if (window.Swal) Swal.fire({icon: 'error', title: 'PDF could not be created', text: 'Please try again.'});
        else alert('PDF could not be created. Please try again.');
    } finally {
        button.disabled = false;
        button.innerHTML = originalLabel;
    }
});
</script>
@endsection
