@extends('layouts.appadmin')

@section('content')
@php
    $studentName = trim(collect([$student->first_name, $student->last_name])->filter()->implode(' '));
    $dateValue = static fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('d-m-Y') : 'N/A';
    $fileUrl = static fn ($path) => $path ? url('uploads/' . basename($path)) : null;
    $profile = $collegeProfile ?? \App\Models\Profile::first();
    $collegeName = $profile?->college_name ?? config('app.name', 'College Management');
    $brandAsset = $profile?->logo ?? $profile?->banner ?? null;
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

<div class="container-fluid premium-page classic-student-page">
    <div class="classic-student-actions">
        <a href="{{ url('admin/students') }}?course={{ $student->course }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Back</a>
        <a href="{{ url('admin/students/' . $student->id . '/edit') }}" class="premium-btn premium-btn-gold"><i class="bx bx-edit-alt"></i> Edit student</a>
        <button type="button" class="premium-btn premium-btn-primary js-print-student"><i class="bx bx-printer"></i> Print form</button>
    </div>

    <article class="classic-admission-form" style="position: relative; overflow: hidden;">
        @if($collegeLogo)
            <img src="{{ $collegeLogo }}" alt="Watermark" style="position: absolute; top: 52%; left: 50%; transform: translate(-50%, -50%); width: 140mm; height: 140mm; opacity: 0.07; pointer-events: none; z-index: 0; object-fit: contain;">
        @endif

        <div style="position: relative; z-index: 1;">
            <header class="classic-form-header" style="border-bottom: 2px solid #111; padding-bottom: 12px; margin-bottom: 16px;">
                <h1 style="margin: 0 0 6px; font-family: Arial, sans-serif; font-size: 24px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; color: #111; text-align: center;">{{ $collegeName }}</h1>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 6px;">
                    @if($collegeLogo)
                        <div style="flex: 0 0 130px; min-height: 80px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ $collegeLogo }}" onerror="this.onerror=null;this.src='{{ asset('images/default-college-logo.svg') }}';" alt="{{ $collegeName }}" style="max-height: 80px; max-width: 130px; object-fit: contain;">
                        </div>
                    @endif
                    <div style="flex: 1 1 auto; text-align: center;">
                        <p style="margin: 0; font-size: 14px; font-weight: 700; color: #111; line-height: 1.3;">{{ $profile?->address ?? '' }}</p>
                        @if($profile?->affiliation)
                            <p style="margin: 3px 0 0; font-size: 12.5px; font-style: italic; color: #333;">{{ $profile->affiliation }}</p>
                        @endif
                        <div style="margin-top: 6px;">
                            <span style="display: inline-block; font-size: 13px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; border: 1.5px solid #111; padding: 2px 12px; background: #fafafa;">Student Admission Form</span>
                        </div>
                        <p style="margin: 4px 0 0; font-size: 12px; font-weight: 600; color: #444;">Academic Session: {{ optional($student->session)->session_name ?: ($student->batch ?? 'N/A') }}</p>
                    </div>
                    <div style="flex: 0 0 130px;"></div>
                </div>
            </header>

            <aside class="classic-photo-section">
                @php
                    $studentPhotoUrl = asset('images/default-avatar.svg');
                    if (!empty($student->photo)) {
                        if (str_starts_with($student->photo, 'http')) {
                            $studentPhotoUrl = $student->photo;
                        } elseif (file_exists(public_path($student->photo))) {
                            $studentPhotoUrl = url($student->photo);
                        } elseif (file_exists(public_path('uploads/' . basename($student->photo)))) {
                            $studentPhotoUrl = url('uploads/' . basename($student->photo));
                        }
                    }
                @endphp
                <img src="{{ $studentPhotoUrl }}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';" alt="{{ $studentName }} photo" style="width: 100%; height: 100%; object-fit: cover;">
                <p>Student photograph</p>
            </aside>

        <section><h2 class="classic-section-title">Basic information</h2><dl class="classic-info-list">
            <div><dt>Registration Number</dt><dd>{{ $student->reg_no ?: 'N/A' }}</dd></div><div><dt>Roll Number</dt><dd>{{ $student->roll_no ?: 'N/A' }}</dd></div><div><dt>Student ID</dt><dd>{{ $student->student_id ?: 'N/A' }}</dd></div><div><dt>First Name</dt><dd>{{ $student->first_name ?: 'N/A' }}</dd></div><div><dt>Last Name</dt><dd>{{ $student->last_name ?: 'N/A' }}</dd></div><div><dt>Gender</dt><dd>{{ $student->gender ?: 'N/A' }}</dd></div><div><dt>Date of Birth</dt><dd>{{ $dateValue($student->date_of_birth) }}</dd></div>
        </dl></section>
        <section><h2 class="classic-section-title">Contact information</h2><dl class="classic-info-list"><div><dt>Phone Number</dt><dd>{{ $student->phone_number ?: 'N/A' }}</dd></div><div><dt>Email Address</dt><dd>{{ $student->email ?: 'N/A' }}</dd></div></dl></section>
        <section><h2 class="classic-section-title">Family information</h2><dl class="classic-info-list"><div><dt>Father's Name</dt><dd>{{ $student->father_name ?: 'N/A' }}</dd></div><div><dt>Mother's Name</dt><dd>{{ $student->mother_name ?: 'N/A' }}</dd></div><div><dt>Category</dt><dd>{{ $student->category ?: 'N/A' }}</dd></div><div><dt>Caste</dt><dd>{{ $student->caste ?: 'N/A' }}</dd></div></dl></section>
        <section><h2 class="classic-section-title">Address information</h2><div class="classic-address-grid"><div><h3>Permanent Address</h3><p>{{ $student->permanent_address ?: 'N/A' }}</p></div><div><h3>Correspondence Address</h3><p>{{ $student->correspondence_address ?: 'N/A' }}</p></div></div></section>
        <section><h2 class="classic-section-title">Academic information</h2><table class="classic-info-table"><tbody>
            <tr><th>Course Name</th><td>{{ optional($student->course_details)->name ?? 'N/A' }}</td><th>Session</th><td>{{ optional($student->session)->session_name ?? 'N/A' }}</td></tr><tr><th>Admission Date</th><td>{{ $dateValue($student->admission_date) }}</td><th>Status</th><td>{{ $student->status ?: 'N/A' }}</td></tr><tr><th>Current Year</th><td>{{ $student->current_year ?: 'N/A' }}</td><th>Current Semester</th><td>{{ $student->current_semester ?: 'N/A' }}</td></tr>
        </tbody></table></section>
        <section><h2 class="classic-section-title">Subjects enrolled</h2><div class="classic-subject-list">@forelse($subjects as $subject)<span>{{ $loop->iteration }}. {{ $subject->name }}</span>@empty No subjects enrolled @endforelse</div></section>
        <section><h2 class="classic-section-title">Documents submitted</h2><div class="classic-document-list">@foreach(['Aadhaar card' => $student->aadhar_card, 'ID proof' => $student->id_proof, 'Category certificate' => $student->category_certificate] as $label => $path)<div><strong>{{ $label }}</strong>@if($path)<span class="classic-document-yes"><i class="bx bx-check"></i> Submitted</span><a href="{{ $fileUrl($path) }}" target="_blank" rel="noopener">View</a>@else<span class="classic-document-no"><i class="bx bx-x"></i> Not submitted</span>@endif</div>@endforeach</div></section>
        <footer class="classic-form-footer"><p><strong>Date of issue:</strong> {{ now()->format('d-m-Y') }}</p><div class="classic-signatures"><span>Student's Signature</span><span>Parent/Guardian Signature</span><span>Authorized Signature</span></div><small>This is a computer-generated form. Please verify all information carefully.</small></footer>
        </div>
    </article>
</div>
@endsection

@section('scripts')
<script>document.querySelector('.js-print-student')?.addEventListener('click', function () { window.print(); });</script>
@endsection
