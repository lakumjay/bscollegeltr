@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $editing = !empty($staff);
    $formAction = $editing ? route('admin.staff.update', $staff) : route('admin.staff.store');
    $dateValue = static fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('d-m-Y') : '';
    $fileUrl = static fn ($path) => $path ? asset($path) : null;
    $roles = ['Teacher' => 'Teacher', 'Admin' => 'Admin', 'Librarian' => 'Librarian', 'Accountant' => 'Accountant', 'NonTeaching' => 'Non teaching'];
    $genders = ['Male', 'Female', 'Other'];
@endphp

@if($errors->any())
    <div class="staff-form-errors"><strong>Please correct the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<form method="post" action="{{ $formAction }}" enctype="multipart/form-data" class="premium-form-grid staff-form-grid">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="staff-form-section"><h3><i class="bx bx-id-card"></i>Identity and contact</h3><p>Core information used to identify and contact the staff member.</p></div>
    <div class="premium-field"><label class="premium-label" for="role">Staff category <span>*</span></label><select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required><option value="">Select category</option>@foreach($roles as $value => $label)<option value="{{ $value }}" {{ old('role', $staff->role ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>@error('role')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field"><label class="premium-label" for="staff_id">Staff ID <span>*</span></label><input type="text" name="staff_id" id="staff_id" class="form-control @error('staff_id') is-invalid @enderror" value="{{ old('staff_id', $staff->staff_id ?? '') }}" required>@error('staff_id')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field"><label class="premium-label" for="email">Email address <span>*</span></label><input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staff->email ?? '') }}" required>@error('email')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field staff-field-half"><label class="premium-label" for="first_name">First name <span>*</span></label><input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $staff->first_name ?? '') }}" required>@error('first_name')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field staff-field-half"><label class="premium-label" for="last_name">Last name <span>*</span></label><input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $staff->last_name ?? '') }}" required>@error('last_name')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field"><label class="premium-label" for="phone">Phone number</label><input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $staff->phone ?? '') }}" inputmode="numeric">@error('phone')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field"><label class="premium-label" for="gender">Gender</label><select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror"><option value="">Select gender</option>@foreach($genders as $gender)<option value="{{ $gender }}" {{ old('gender', $staff->gender ?? '') === $gender ? 'selected' : '' }}>{{ $gender }}</option>@endforeach</select>@error('gender')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field"><label class="premium-label" for="date_of_birth">Date of birth</label><div class="premium-date-control"><input type="text" name="date_of_birth" id="date_of_birth" class="form-control staff-premium-datepicker @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $editing ? $dateValue($staff->date_of_birth) : '') }}" autocomplete="off" placeholder="Select date of birth"><i class="bx bx-calendar"></i></div>@error('date_of_birth')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field staff-field-wide"><label class="premium-label" for="address">Address</label><textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $staff->address ?? '') }}</textarea>@error('address')<span class="staff-field-error">{{ $message }}</span>@enderror</div>

    <div class="staff-form-section"><h3><i class="bx bx-briefcase-alt-2"></i>Employment details</h3><p>Current employment category, qualification and joining information.</p></div>
    <div class="premium-field staff-field-half"><label class="premium-label" for="qualification">Qualification</label><input type="text" name="qualification" id="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $staff->qualification ?? '') }}">@error('qualification')<span class="staff-field-error">{{ $message }}</span>@enderror</div>
    <div class="premium-field staff-field-half"><label class="premium-label" for="joining_date">Joining date</label><div class="premium-date-control"><input type="text" name="joining_date" id="joining_date" class="form-control staff-premium-datepicker @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', $editing ? $dateValue($staff->joining_date) : '') }}" autocomplete="off" placeholder="Select joining date"><i class="bx bx-calendar"></i></div>@error('joining_date')<span class="staff-field-error">{{ $message }}</span>@enderror</div>

    <div class="staff-form-section"><h3><i class="bx bx-folder-open"></i>Documents</h3><p>Upload JPG, PNG or PDF files up to 2 MB. Existing files remain unchanged unless replaced.</p></div>
    @foreach([
        ['name' => 'photo', 'label' => 'Staff photo', 'accept' => 'image/jpeg,image/png', 'image' => true],
        ['name' => 'id_proof', 'label' => 'ID proof', 'accept' => '.pdf,image/jpeg,image/png', 'image' => false],
        ['name' => 'certificate', 'label' => 'Certificate', 'accept' => '.pdf,image/jpeg,image/png', 'image' => false],
    ] as $document)
        @php $existingFile = $editing ? $staff->{$document['name']} : null; @endphp
        <div class="premium-field staff-document-field"><label class="premium-label" for="{{ $document['name'] }}">{{ $document['label'] }}</label><input type="file" name="{{ $document['name'] }}" id="{{ $document['name'] }}" class="form-control staff-document-upload @error($document['name']) is-invalid @enderror" accept="{{ $document['accept'] }}" data-preview="{{ $document['name'] }}-preview">@error($document['name'])<span class="staff-field-error">{{ $message }}</span>@enderror
            <div id="{{ $document['name'] }}-preview" class="staff-document-preview" aria-live="polite">@if($existingFile)<div class="staff-existing-file">@if($document['image'])<img src="{{ $fileUrl($existingFile) }}" alt="{{ $document['label'] }}">@else<i class="bx bx-file"></i>@endif<div><strong>Current {{ strtolower($document['label']) }}</strong><a href="{{ $fileUrl($existingFile) }}" target="_blank" rel="noopener">View file <i class="bx bx-link-external"></i></a></div></div>@else<div class="staff-document-preview-empty"><i class="bx {{ $document['image'] ? 'bx-image-alt' : 'bx-file' }}"></i><span>Choose a file to preview it here.</span></div>@endif</div>
        </div>
    @endforeach

    <div class="staff-form-actions"><button type="submit" class="premium-btn premium-btn-navy"><i class="bx {{ $editing ? 'bx-save' : 'bx-user-plus' }}"></i> {{ $editing ? 'Save changes' : 'Add staff' }}</button><a href="{{ route('admin.staff.index') }}" class="premium-btn premium-btn-outline">Cancel</a></div>
</form>
