@extends('layouts.appadmin')

@section('content')
@php
    $staffName = trim(collect([$staff->first_name, $staff->last_name])->filter()->implode(' '));
    $dateValue = static fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('d M Y') : 'Not provided';
    $fileUrl = static fn ($path) => $path ? asset($path) : null;
    $isImage = static fn ($path) => in_array(strtolower(pathinfo((string) $path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
@endphp

<div class="container-fluid premium-page staff-profile-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">People operations</span><h1 class="premium-page-title">Staff profile</h1><p class="premium-page-subtitle">Review employment details and supporting documents for {{ $staffName ?: 'this staff member' }}.</p></div>
        <div class="premium-header-actions"><a href="{{ route('admin.staff.index') }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Staff directory</a><a href="{{ route('admin.staff.edit', $staff) }}" class="premium-btn premium-btn-gold"><i class="bx bx-edit-alt"></i> Edit staff</a></div>
    </div>

    <section class="premium-card staff-profile-hero mb-4"><div class="premium-card-body"><div class="staff-profile-hero-content">
        <div class="staff-profile-photo">@if($staff->photo)<img src="{{ $fileUrl($staff->photo) }}" alt="{{ $staffName }} photo">@else<img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $staffName }} photo">@endif</div>
        <div class="staff-profile-identity"><span class="premium-page-eyebrow">{{ $staff->role ?: 'Staff member' }}</span><h2>{{ $staffName ?: 'Unnamed staff member' }}</h2><p>Staff ID: <strong>{{ $staff->staff_id ?: 'Not assigned' }}</strong></p></div>
        <span class="premium-status {{ strtolower((string) $staff->status) === 'active' ? 'premium-status-active' : 'premium-status-inactive' }}">{{ $staff->status ?: 'Not set' }}</span>
    </div></div></section>

    <div class="row g-4">
        <div class="col-xl-7"><section class="premium-card h-100"><div class="premium-card-header"><div><h2><i class="bx bx-id-card me-2"></i>Contact and personal details</h2><p>Identity and communication information.</p></div></div><div class="premium-card-body"><div class="staff-profile-grid">
            <div><span>Email address</span><strong>{{ $staff->email ?: 'Not provided' }}</strong></div><div><span>Phone number</span><strong>{{ $staff->phone ?: 'Not provided' }}</strong></div><div><span>Date of birth</span><strong>{{ $dateValue($staff->date_of_birth) }}</strong></div><div><span>Gender</span><strong>{{ $staff->gender ?: 'Not provided' }}</strong></div><div class="staff-profile-wide"><span>Address</span><strong>{{ $staff->address ?: 'Not provided' }}</strong></div>
        </div></div></section></div>
        <div class="col-xl-5"><section class="premium-card h-100"><div class="premium-card-header"><div><h2><i class="bx bx-briefcase-alt-2 me-2"></i>Employment details</h2><p>Current role and employment record.</p></div></div><div class="premium-card-body"><div class="staff-profile-grid staff-profile-grid-single">
            <div><span>Staff category</span><strong>{{ $staff->role ?: 'Not provided' }}</strong></div><div><span>Joining date</span><strong>{{ $dateValue($staff->joining_date) }}</strong></div><div><span>Qualification</span><strong>{{ $staff->qualification ?: 'Not provided' }}</strong></div><div><span>Recurring income</span><strong>@if($staff->salaryStructure){{ $staff->salaryStructure->currency }} {{ number_format((float) $staff->salaryStructure->basic_pay + (float) $staff->salaryStructure->da + (float) $staff->salaryStructure->hra + (float) $staff->salaryStructure->additional_earnings, 2) }}@else Not configured @endif</strong></div>
        </div></div></section></div>
        <div class="col-12"><section class="premium-card"><div class="premium-card-header"><div><h2><i class="bx bx-folder-open me-2"></i>Documents</h2><p>Open submitted staff documents in a separate tab.</p></div></div><div class="premium-card-body"><div class="row g-3">
            @foreach(['ID proof' => $staff->id_proof, 'Certificate' => $staff->certificate] as $label => $path)
                <div class="col-md-6"><article class="staff-profile-document"><div><i class="bx bx-file"></i><span><strong>{{ $label }}</strong><small>{{ $path ? 'Document submitted' : 'Not submitted' }}</small></span></div>@if($path)<a href="{{ $fileUrl($path) }}" target="_blank" rel="noopener">View <i class="bx bx-link-external"></i></a>@endif</article></div>
            @endforeach
        </div></div></section></div>
    </div>

    <div class="premium-inline-actions mt-4"><a href="{{ route('admin.staff.transfer_certificate', $staff) }}" class="premium-btn premium-btn-navy"><i class="bx bx-file"></i> Transfer certificate</a><a href="{{ route('admin.staff.id_card', $staff) }}" class="premium-btn premium-btn-outline"><i class="bx bx-id-card"></i> Generate ID card</a></div>
</div>
@endsection
