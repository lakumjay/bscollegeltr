@extends('layouts.appadmin')

@section('content')
<div class="premium-page finance-page">
    <section class="premium-page-header">
        <div>
            <span class="premium-eyebrow">Fee management</span>
            <h1>Fee payment report</h1>
            <p>Find and export fee collections by course, student, semester, method, or date.</p>
        </div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="premium-content-card finance-filter-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Search & filter</span><h2>Payment filters</h2></div><i class="fa fa-filter"></i></div>
        <form method="GET" action="{{ route('fee_management.report') }}" id="reportForm" class="finance-filter-grid">
            <div class="finance-field finance-field--course">
                <label for="courseSelect">Course <span>*</span></label>
                <select name="course_id" id="courseSelect" required onchange="this.form.submit()">
                    <option value="">Select a course</option>
                    @foreach($courses as $course)<option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>@endforeach
                </select>
            </div>

            @if($selectedCourseId)
                <div class="finance-field"><label>Fee head</label><select name="fee_head"><option value="">All fee heads</option>@foreach($feeHeadNames as $head)<option value="{{ $head }}" {{ $filterFeeHead === $head ? 'selected' : '' }}>{{ $head }}</option>@endforeach</select></div>
                <div class="finance-field"><label>Student name / Roll no</label><input type="text" name="student_name" placeholder="Search student" value="{{ $filterStudent }}"></div>
                <div class="finance-field"><label>Semester</label><select name="semester"><option value="">All semesters</option>@foreach($semesterOptions as $semester)<option value="{{ $semester }}" {{ $filterSemester == $semester ? 'selected' : '' }}>Semester {{ $semester }}</option>@endforeach</select></div>
                <div class="finance-field"><label>Payment method</label><select name="payment_method"><option value="">All methods</option>@foreach($paymentMethods as $method)<option value="{{ $method }}" {{ $filterMethod === $method ? 'selected' : '' }}>{{ $method }}</option>@endforeach</select></div>
                <div class="finance-field"><label>Date from</label><input type="text" class="premium-datepicker" name="date_from" value="{{ $filterDateFrom ? \Carbon\Carbon::parse($filterDateFrom)->format('d-m-Y') : '' }}" autocomplete="off" placeholder="DD-MM-YYYY"></div>
                <div class="finance-field"><label>Date to</label><input type="text" class="premium-datepicker" name="date_to" value="{{ $filterDateTo ? \Carbon\Carbon::parse($filterDateTo)->format('d-m-Y') : '' }}" autocomplete="off" placeholder="DD-MM-YYYY"></div>
                <div class="finance-filter-actions"><button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-search"></i> Apply filters</button><a class="premium-btn premium-btn-outline" href="{{ route('fee_management.report', ['course_id' => $selectedCourseId]) }}"><i class="fa fa-times"></i> Reset</a></div>
            @endif
        </form>
    </section>

    @if($selectedCourseId)
        @if($payments->isEmpty())
            <section class="premium-empty-state"><i class="fa fa-file-text-o"></i><h3>No fee payments found</h3><p>Try changing the selected filters or date range.</p></section>
        @else
            <?php $totalAmount = $payments->sum('amount'); ?>
            <section class="finance-summary-grid">
                <article><span>Payment records</span><strong>{{ $payments->count() }}</strong><small>Matched collections</small></article>
                <article><span>Total collected</span><strong>₹{{ number_format($totalAmount, 2) }}</strong><small>For this result</small></article>
                <article><span>Payment methods</span><strong>{{ $payments->pluck('payment_method')->filter()->unique()->count() }}</strong><small>Methods used</small></article>
            </section>

            <section class="premium-content-card finance-table-card">
                <div class="premium-card-heading"><div><span class="premium-eyebrow">Collections</span><h2>Payment records <span class="premium-count-badge">{{ $payments->count() }}</span></h2></div><div class="premium-action-row"><a class="premium-btn premium-btn-outline" href="{{ route('fee_management.report_export', request()->query()) }}"><i class="fa fa-file-text-o"></i> CSV</a><a class="premium-btn premium-btn-gold" href="{{ route('fee_management.report_excel', request()->query()) }}"><i class="fa fa-file-excel-o"></i> Excel</a><a class="premium-btn premium-btn-primary" href="{{ route('fee_management.report_pdf', request()->query()) }}" target="_blank"><i class="fa fa-file-pdf-o"></i> PDF</a></div></div>
                <div class="premium-table-wrap"><table class="premium-table" id="reportTable"><thead><tr><th>#</th><th>Roll no</th><th>Student</th><th>Fee head</th><th>Payment date</th><th>Method</th><th>Transaction ID</th><th class="text-end">Amount</th></tr></thead><tbody>@foreach($payments as $index => $payment)<tr><td>{{ $index + 1 }}</td><td>{{ $payment->student->roll_no ?? '-' }}</td><td><strong>{{ trim(($payment->student->first_name ?? '') . ' ' . ($payment->student->last_name ?? '')) ?: '-' }}</strong></td><td>{{ $payment->notes ?? '-' }}</td><td>{{ $payment->payment_date ? $payment->payment_date->format('d-m-Y') : '-' }}</td><td><span class="finance-method-badge">{{ $payment->payment_method ?: '-' }}</span></td><td>{{ $payment->transaction_id ?? '-' }}</td><td class="text-end finance-amount">₹{{ number_format($payment->amount, 2) }}</td></tr>@endforeach</tbody><tfoot><tr><td colspan="7">Grand total</td><td class="text-end">₹{{ number_format($totalAmount, 2) }}</td></tr></tfoot></table></div>
            </section>
        @endif
    @else
        <section class="premium-empty-state"><i class="fa fa-arrow-up"></i><h3>Select a course to start</h3><p>Choose a course above to view its fee payment report.</p></section>
    @endif
</div>
@endsection
