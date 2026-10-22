@extends('layouts.appadmin')

@section('content')
@php
    $profile = $collegeProfile ?? \App\Models\Profile::first();
    $collegeName = $profile?->college_name ?? config('app.name', 'College Management');
    $startLabel = \Carbon\Carbon::parse($start_date)->format('d M Y');
    $endLabel = \Carbon\Carbon::parse($end_date)->format('d M Y');
    $staffLabel = $staff ? trim($staff->first_name . ' ' . $staff->last_name) . ' (' . $staff->staff_id . ')' : 'All staff';
    $brandAsset = $profile?->banner ?? $profile?->logo ?? null;
    $collegeLogo = null;
    if ($brandAsset && file_exists(public_path($brandAsset))) {
        $mime = @mime_content_type(public_path($brandAsset)) ?: 'image/png';
        $collegeLogo = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents(public_path($brandAsset)));
    } elseif ($brandAsset) {
        $collegeLogo = url($brandAsset);
    }
@endphp
<div class="container-fluid premium-page attendance-report-page"><div class="premium-page-header"><div><span class="premium-page-eyebrow">Staff attendance</span><h1 class="premium-page-title">Attendance report</h1><p class="premium-page-subtitle">Review attendance from {{ $startLabel }} to {{ $endLabel }}.</p></div><div class="premium-header-actions"><a href="{{ route('admin.staff.attendance.view', ['start_date' => $start_date, 'end_date' => $end_date, 'staff_id' => $staff?->id]) }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Records</a><a href="{{ route('admin.staff.attendance.export_csv', ['start_date' => $start_date, 'end_date' => $end_date, 'staff_id' => $staff?->id]) }}" class="premium-btn premium-btn-outline"><i class="bx bx-download"></i> Export CSV</a><button id="download-pdf" type="button" class="premium-btn premium-btn-gold"><i class="bx bx-download"></i> Download PDF</button></div></div>
    <article id="report-content" class="attendance-report-sheet">
        <header>
            @if($collegeLogo)
                <div style="text-align: center; margin-bottom: 10px;">
                    <img src="{{ $collegeLogo }}" alt="{{ $collegeName }}" style="max-height: 55px; max-width: 220px; object-fit: contain; display: inline-block;">
                </div>
            @endif
            <span>Official attendance record</span>
            <h2>{{ $collegeName }}</h2>
            <h3>Staff attendance report</h3>
        </header>
        <div class="attendance-report-meta"><div><span>Period</span><strong>{{ $startLabel }} — {{ $endLabel }}</strong></div><div><span>Staff member</span><strong>{{ $staffLabel }}</strong></div><div><span>Total records</span><strong>{{ $attendance->count() }}</strong></div></div><div class="table-responsive"><table class="table attendance-report-table mb-0"><thead><tr><th>Date</th><th>Staff ID</th><th>Name</th><th>Status</th><th>Remarks</th></tr></thead><tbody>@forelse($attendance as $record)<?php $member = $record->staff; ?><tr><td>{{ $record->date->format('d M Y') }}</td><td>{{ $member?->staff_id ?? 'Archived #' . $record->staff_id }}</td><td>{{ $member ? trim($member->first_name . ' ' . $member->last_name) : 'Staff record unavailable' }}</td><td><span class="premium-status premium-status-{{ strtolower($record->status) }}">{{ $record->status }}</span></td><td>{{ $record->remarks ?: '—' }}</td></tr>@empty<tr><td colspan="5" class="text-center py-5 text-muted">No attendance records found for this period.</td></tr>@endforelse</tbody></table></div><footer>Generated on {{ now()->format('d M Y, h:i A') }} · {{ $collegeName }}</footer></article>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>document.getElementById('download-pdf').addEventListener('click', function () { const button=this; button.disabled=true; button.innerHTML='<i class="bx bx-loader-alt bx-spin"></i> Preparing PDF'; html2canvas(document.getElementById('report-content'), { scale:2, backgroundColor:'#ffffff', useCORS:true }).then(function(canvas){ const {jsPDF}=window.jspdf; const doc=new jsPDF({orientation:'portrait',unit:'mm',format:'a4'}); const width=190; const height=canvas.height*width/canvas.width; doc.addImage(canvas.toDataURL('image/png'),'PNG',10,10,width,height); doc.save('attendance_report_{{ $staff ? $staff->staff_id : 'all' }}_{{ \Carbon\Carbon::parse($start_date)->format('Ymd') }}_to_{{ \Carbon\Carbon::parse($end_date)->format('Ymd') }}.pdf'); }).finally(function(){ button.disabled=false; button.innerHTML='<i class="bx bx-download"></i> Download PDF'; }); });</script>
@endsection
