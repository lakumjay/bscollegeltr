@extends('layouts.appadmin')

@section('content')
<div class="container-fluid premium-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">Institution settings</span><h1 class="premium-page-title">College Profile</h1><p class="premium-page-subtitle">Keep your college details, logo and certificate banner up to date.</p></div>
    </div>

    <form method="post" action="{{ url('admin/profile') }}" enctype="multipart/form-data">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if(session('success'))<div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>@endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="premium-card h-100">
                    <div class="premium-card-header"><div><h2>Brand visuals</h2><p>These images appear across the login, dashboard and certificates.</p></div></div>
                    <div class="premium-card-body premium-profile-media">
                        <div>
                            <label class="premium-label" for="logo">College logo</label>
                            <div class="premium-image-frame"><img src="{{ !empty($profile?->logo) ? url($profile->logo) : '' }}" class="{{ empty($profile?->logo) ? 'd-none' : '' }}" id="logo-preview" alt="College logo preview"><span id="logo-placeholder" class="premium-muted {{ !empty($profile?->logo) ? 'd-none' : '' }}">Logo preview will appear here</span></div>
                            <input type="file" class="form-control mt-3 @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">@error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="premium-label" for="banner">Certificate banner</label>
                            <div class="premium-image-frame premium-banner-frame"><img src="{{ !empty($profile?->banner) ? url($profile->banner) : '' }}" class="{{ empty($profile?->banner) ? 'd-none' : '' }}" id="banner-preview" alt="Certificate banner preview"><span id="banner-placeholder" class="premium-muted {{ !empty($profile?->banner) ? 'd-none' : '' }}">Banner preview will appear here</span></div>
                            <input type="file" class="form-control mt-3 @error('banner') is-invalid @enderror" id="banner" name="banner" accept="image/*">@error('banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="premium-card h-100">
                    <div class="premium-card-header"><div><h2>Institution details</h2><p>Information displayed in your college records and communications.</p></div></div>
                    <div class="premium-card-body">
                        <div class="premium-form-grid">
                            <div class="premium-field" style="grid-column:span 6"><label for="college_name" class="premium-label">College name (English) <span class="text-danger">*</span></label><input type="text" class="form-control @error('college_name') is-invalid @enderror" id="college_name" name="college_name" value="{{ old('college_name', $profile->college_name ?? '') }}" required>@error('college_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="premium-field" style="grid-column:span 6"><label for="name_in_hindi" class="premium-label">College name (Hindi)</label><input type="text" class="form-control @error('name_in_hindi') is-invalid @enderror" id="name_in_hindi" name="name_in_hindi" value="{{ old('name_in_hindi', $profile->name_in_hindi ?? '') }}">@error('name_in_hindi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="premium-field" style="grid-column:span 6"><label for="contact_number" class="premium-label">Contact number <span class="text-danger">*</span></label><input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number', $profile->contact_number ?? '') }}" required>@error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="premium-field" style="grid-column:span 6"><label for="email" class="premium-label">Email <span class="text-danger">*</span></label><input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $profile->email ?? '') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="premium-field" style="grid-column:span 6"><label for="address" class="premium-label">Address</label><textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address', $profile->address ?? '') }}</textarea>@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="premium-field" style="grid-column:span 6"><label for="website" class="premium-label">Website</label><input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $profile->website ?? '') }}">@error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            <div class="premium-field" style="grid-column:span 6">
                                <label for="lc_prefix" class="premium-label">L.C prefix</label>
                                <input type="text" class="form-control text-uppercase @error('lc_prefix') is-invalid @enderror" id="lc_prefix" name="lc_prefix" maxlength="20" placeholder="BSM/LTR" value="{{ old('lc_prefix', $profile->lc_prefix ?? '') }}" aria-describedby="lc-prefix-help">
                                @error('lc_prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small id="lc-prefix-help" class="premium-muted">Used for CLC numbers, for example BSM/LTR/{{ now()->year }}/0009.</small>
                            </div>
                            <div class="premium-field" style="grid-column:span 6; display: flex; flex-direction: column; justify-content: center;">
                                <label class="premium-label">Certificate Serial Number Mode</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="auto_cert_number" name="auto_cert_number" value="1" {{ old('auto_cert_number', $profile->auto_cert_number ?? true) ? 'checked' : '' }} style="width: 2.8em; height: 1.4em; cursor: pointer;">
                                    <label class="form-check-label fw-bold ms-2" for="auto_cert_number" style="cursor: pointer; padding-top: 3px;">
                                        Auto-generate Certificate Serial Number (e.g. /0009)
                                    </label>
                                </div>
                                <small class="premium-muted mt-1">If enabled, serial numbers are assigned automatically. If turned off, college staff can manually type the custom patrak/certificate number when generating certificates.</small>
                            </div>
                            <div class="premium-field-wide"><button type="submit" class="premium-btn premium-btn-gold"><i class="bx bx-save"></i> Save profile</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function bindImagePreview(inputId, previewId, placeholderId) {
        document.getElementById(inputId).addEventListener('change', function () {
            const file = this.files[0], preview = document.getElementById(previewId), placeholder = document.getElementById(placeholderId);
            if (!file) return;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            placeholder.classList.add('d-none');
        });
    }
    bindImagePreview('logo', 'logo-preview', 'logo-placeholder');
    bindImagePreview('banner', 'banner-preview', 'banner-placeholder');
</script>
@endsection
