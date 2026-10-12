@extends('layouts.appadmin')

@section('content')
@php
    $profile = $collegeProfile ?? \App\Models\Profile::first();
    $collegeName = $profile?->college_name ?? config('app.name', 'College Management');
    $staffName = trim(collect([$staff->first_name, $staff->last_name])->filter()->implode(' '));
    $joiningDate = $staff->joining_date ? \Illuminate\Support\Carbon::parse($staff->joining_date)->format('d M Y') : 'Not available';
    $issueDate = now()->format('d M Y');
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
@endphp
<div class="container-fluid premium-page transfer-certificate-page">
    <div class="premium-page-header"><div><span class="premium-page-eyebrow">Staff records</span><h1 class="premium-page-title">Transfer certificate</h1><p class="premium-page-subtitle">Review and download the certificate for {{ $staffName }}.</p></div><div class="premium-header-actions"><a href="{{ route('admin.staff.show', $staff) }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Staff profile</a><button id="download-pdf" type="button" class="premium-btn premium-btn-gold"><i class="bx bx-download"></i> Download PDF</button></div></div>

    <article id="certificate-content" class="transfer-certificate-sheet">
        <header>
            @if($collegeLogo)
                <div style="text-align: center; margin-bottom: 10px;">
                    <img src="{{ $collegeLogo }}" alt="{{ $collegeName }}" style="max-height: 60px; max-width: 220px; object-fit: contain; display: inline-block;">
                </div>
            @endif
            <span>Official staff record</span>
            <h2>Transfer Certificate</h2>
            <h3>{{ $collegeName }}</h3>
        </header>
        <div class="transfer-certificate-meta"><div><span>Staff ID</span><strong>{{ $staff->staff_id }}</strong></div><div><span>Date of issue</span><strong>{{ $issueDate }}</strong></div></div>
        <div class="transfer-certificate-details"><p><strong>Name:</strong> {{ $staffName }}</p><p><strong>Role:</strong> {{ $staff->role ?: 'Staff member' }}</p><p><strong>Joining date:</strong> {{ $joiningDate }}</p></div>
        <p class="transfer-certificate-statement">This is to certify that <strong>{{ $staffName }}</strong> has been relieved from their duties as <strong>{{ $staff->role ?: 'staff member' }}</strong> at <strong>{{ $collegeName }}</strong>, effective from {{ $issueDate }}. All dues have been cleared.</p>
        <footer><div><span class="transfer-signature-line"></span><strong>Principal / Authorized signatory</strong><small>{{ $collegeName }}</small></div></footer>
    </article>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById('download-pdf').addEventListener('click', function () {
    const button = this;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Preparing PDF';

    const element = document.getElementById('certificate-content');
    html2canvas(element, { scale: 2, backgroundColor: '#ffffff', useCORS: true })
        .then(function (canvas) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
            const margin = 10;
            const pageWidth = doc.internal.pageSize.getWidth() - (margin * 2);
            const imgHeight = canvas.height * pageWidth / canvas.width;
            doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, pageWidth, imgHeight);
            doc.save('transfer_certificate_{{ $staff->staff_id }}.pdf');
        })
        .catch(function (error) {
            console.error('Error generating PDF:', error);
            alert('Could not generate PDF. Please try again.');
        })
        .finally(function () {
            button.disabled = false;
            button.innerHTML = originalText;
        });
});
</script>
@endsection
