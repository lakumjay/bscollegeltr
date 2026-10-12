@extends('layouts.appadmin')

@section('content')
@php
    $collegeName = $collegeProfile?->college_name ?? config('app.name', 'College Management');
    $collegeLogo = $collegeProfile?->logo ? asset($collegeProfile->logo) : null;
    $staffName = trim(collect([$staff->first_name, $staff->last_name])->filter()->implode(' '));
    $validTill = now()->addYear()->format('d M Y');
@endphp
<div class="container-fluid premium-page staff-id-card-page">
    <div class="premium-page-header"><div><span class="premium-page-eyebrow">Staff records</span><h1 class="premium-page-title">Staff ID card</h1><p class="premium-page-subtitle">Review the digital card for {{ $staffName }} before downloading it.</p></div><div class="premium-header-actions"><a href="{{ route('admin.staff.show', $staff) }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Staff profile</a><button id="download-pdf" type="button" class="premium-btn premium-btn-gold"><i class="bx bx-download"></i> Download PDF</button></div></div>

    <section class="premium-card"><div class="premium-card-header"><div><h2>ID card preview</h2><p>The downloaded card uses the same design and college profile details.</p></div></div><div class="premium-card-body staff-id-card-preview-wrap">
        <article id="id-card-content" class="staff-id-card">
            <header><div class="staff-id-brand">@if($collegeLogo)<img src="{{ $collegeLogo }}" alt="{{ $collegeName }} logo">@else<img src="{{ asset('images/default-college-logo.svg') }}" alt="{{ $collegeName }} logo">@endif</div><div><span>Employee identity card</span><h2>{{ $collegeName }}</h2></div></header>
            <div class="staff-id-card-body"><div class="staff-id-photo">@if($staff->photo)<img src="{{ asset($staff->photo) }}" alt="{{ $staffName }} photo">@else<img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $staffName }} photo">@endif</div><div class="staff-id-details"><h3>{{ $staffName ?: 'Staff member' }}</h3><p>{{ $staff->role ?: 'Staff member' }}</p><dl><div><dt>Staff ID</dt><dd>{{ $staff->staff_id }}</dd></div><div><dt>Email</dt><dd>{{ $staff->email }}</dd></div></dl></div></div>
            <footer><span>Valid through {{ $validTill }}</span><strong>Authorized staff</strong></footer>
        </article>
    </div></section>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
document.getElementById('download-pdf').addEventListener('click', function () {
    const button = this;
    const card = document.getElementById('id-card-content');
    button.disabled = true;
    button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Preparing PDF';
    html2canvas(card, { scale: 3, backgroundColor: '#102a4f', useCORS: true }).then(function (canvas) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: [85.6, 54] });
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, 85.6, 54);
        doc.save('staff_id_card_{{ $staff->staff_id }}.pdf');
    }).finally(function () {
        button.disabled = false;
        button.innerHTML = '<i class="bx bx-download"></i> Download PDF';
    });
});
</script>
@endsection
