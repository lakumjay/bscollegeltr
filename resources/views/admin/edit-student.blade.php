@extends('layouts.appadmin')

@section('css')
<link rel="stylesheet" href="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
@php
    $fileUrl = fn ($path) => $path ? '/uploads/' . basename($path) : null;
    $isImage = fn ($path) => $path && preg_match('/\.(jpe?g|png|gif)$/i', $path);
    $selectedSubjects = old('subjects', $student->subjects ?? []);
@endphp
<div class="container-fluid premium-page student-admission-page">
    <div class="premium-page-header"><div><span class="premium-page-eyebrow">Student management</span><h1 class="premium-page-title">Edit Student</h1><p class="premium-page-subtitle">Update {{ $student->first_name }} {{ $student->last_name }}’s personal, academic and document details.</p></div><div class="premium-header-actions"><a href="{{ url('admin/students/'.$student->id) }}" class="premium-btn premium-btn-outline"><i class="bx bx-show"></i> View student</a><a href="{{ url('admin/students') }}?course={{ $student->course }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Student list</a></div></div>

    @if($errors->any())<div class="alert alert-danger border-0 shadow-sm"><strong>Please correct the highlighted fields.</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form id="editStudentForm" method="post" action="{{ url('admin/students/'.$student->id) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        <div id="student-validation-summary" class="student-validation-summary d-none" role="alert" tabindex="-1"></div>
        <div class="premium-card"><div class="premium-card-header"><div><h2>Student admission details</h2><p>Existing documents remain unchanged unless you select a replacement.</p></div></div><div class="premium-card-body"><div class="row g-4">
            <div class="col-xl-7"><div class="student-section h-100">
                <div class="student-section"><h3 class="student-section-title"><i class="bx bx-id-card"></i>Identity</h3><div class="premium-form-grid">
                    <div class="premium-field"><label class="premium-label" for="reg_no">Registration number</label><input type="text" class="form-control" id="reg_no" name="reg_no" value="{{ old('reg_no', $student->reg_no) }}"></div>
                    <div class="premium-field"><label class="premium-label" for="roll_no">Roll number <span class="text-danger">*</span></label><input type="text" class="form-control" id="roll_no" name="roll_no" value="{{ old('roll_no', $student->roll_no) }}" required></div>
                    <div class="premium-field"><label class="premium-label" for="student_id">Student ID <span class="text-danger">*</span></label><input type="text" class="form-control" id="student_id" name="student_id" value="{{ old('student_id', $student->student_id) }}" required></div>
                    <div class="premium-field"><label class="premium-label" for="first_name">First name <span class="text-danger">*</span></label><input type="text" class="form-control name-input" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" maxlength="100" required></div>
                    <div class="premium-field"><label class="premium-label" for="last_name">Last name</label><input type="text" class="form-control name-input" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" maxlength="100"></div>
                    <div class="premium-field"><label class="premium-label" for="gender">Gender <span class="text-danger">*</span></label><select class="form-select" id="gender" name="gender" required><option value="">Select gender</option>@foreach(['Male', 'Female', 'Other'] as $gender)<option value="{{ $gender }}" {{ old('gender', $student->gender) === $gender ? 'selected' : '' }}>{{ $gender }}</option>@endforeach</select></div>
                    <div class="premium-field"><label class="premium-label" for="date_of_birth">Date of birth <span class="text-danger">*</span></label><div class="premium-date-control"><input type="text" class="form-control premium-datepicker" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->formatted_date_of_birth ?: (optional($student->date_of_birth)->format('d-m-Y') ?? '')) }}" autocomplete="off" required><i class="bx bx-calendar"></i></div></div>
                    <div class="premium-field"><label class="premium-label" for="phone_number">Phone number <span class="text-danger">*</span></label><input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $student->phone_number) }}" inputmode="numeric" maxlength="15" required><p class="premium-muted mb-0 mt-2">Enter 10 to 15 digits only.</p></div>
                    <div class="premium-field"><label class="premium-label" for="email">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email) }}" required></div>
                </div></div>
                <div class="student-section"><h3 class="student-section-title"><i class="bx bx-group"></i>Family and address</h3><div class="premium-form-grid">
                    <div class="premium-field"><label class="premium-label" for="father_name">Father's name <span class="text-danger">*</span></label><input type="text" class="form-control name-input" id="father_name" name="father_name" value="{{ old('father_name', $student->father_name) }}" maxlength="100" required></div>
                    <div class="premium-field"><label class="premium-label" for="mother_name">Mother's name <span class="text-danger">*</span></label><input type="text" class="form-control name-input" id="mother_name" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" maxlength="100" required></div>
                    <div class="premium-field student-address-field"><label class="premium-label" for="permanent_address">Permanent address <span class="text-danger">*</span></label><textarea class="form-control" id="permanent_address" name="permanent_address" required>{{ old('permanent_address', $student->permanent_address) }}</textarea></div>
                    <div class="premium-field student-address-field"><label class="premium-label" for="correspondence_address">Correspondence address <span class="text-danger">*</span></label><textarea class="form-control" id="correspondence_address" name="correspondence_address" required>{{ old('correspondence_address', $student->correspondence_address) }}</textarea></div>
                </div></div>
            </div></div>
            <div class="col-xl-5"><div class="student-section"><h3 class="student-section-title"><i class="bx bx-book-reader"></i>Academic details</h3><p class="premium-muted mb-3">Subject selection is optional.</p><div class="premium-form-grid">
                <div class="premium-field-wide"><label class="premium-label" for="course">Course <span class="text-danger">*</span></label><select class="form-select" id="course" name="course" required><option value="">Select course</option>@foreach($courses as $course)<option value="{{ $course->id }}" {{ (old('course', $student->course) == $course->id || old('course', $student->course) == $course->name) ? 'selected' : '' }}>{{ $course->name }}</option>@endforeach</select></div>
                <div class="premium-field-wide"><label class="premium-label">Subjects <span class="premium-muted">(optional)</span></label><div id="subjects-container" class="subject-options"></div></div>
                <div class="premium-field"><label class="premium-label" for="batch">Batch <span class="text-danger">*</span></label><input type="text" class="form-control" id="batch" name="batch" list="batchOptions" value="{{ old('batch', $student->batch ?: $defaultBatch) }}" required>
                <datalist id="batchOptions">
                    @foreach($allBatches ?? ['2026-2030', '2026-30'] as $b)
                        <option value="{{ $b }}">
                    @endforeach
                </datalist></div>
                <div class="premium-field"><label class="premium-label" for="admission_date">Admission date <span class="text-danger">*</span></label><div class="premium-date-control"><input type="text" class="form-control premium-datepicker" id="admission_date" name="admission_date" value="{{ old('admission_date', $student->formatted_admission_date ?: (optional($student->admission_date)->format('d-m-Y') ?? '')) }}" autocomplete="off" required><i class="bx bx-calendar"></i></div></div>
                <div class="premium-field"><label class="premium-label" for="current_year">Current year <span class="text-danger">*</span></label><input type="number" class="form-control" id="current_year" name="current_year" min="1" max="4" value="{{ old('current_year', $student->current_year) }}" required></div>
                <div class="premium-field"><label class="premium-label" for="current_semester">Current semester <span class="text-danger">*</span></label><input type="number" class="form-control" id="current_semester" name="current_semester" min="1" max="8" value="{{ old('current_semester', $student->current_semester) }}" required></div>
                <div class="premium-field"><label class="premium-label" for="status">Status <span class="text-danger">*</span></label><select class="form-select" id="status" name="status" required><option value="">Select status</option>@foreach(['Active', 'Inactive'] as $status)<option value="{{ $status }}" {{ old('status', $student->status) === $status ? 'selected' : '' }}>{{ $status }}</option>@endforeach</select></div>
                <div class="premium-field"><label class="premium-label" for="special_category">Special category</label><select class="form-select" id="special_category" name="special_category"><option value="">None</option>@foreach($special_categories as $item)<option value="{{ $item->id }}" {{ old('special_category', $student->special_category) == $item->id ? 'selected' : '' }}>{{ $item->category_name }}</option>@endforeach</select></div>
                <div class="premium-field"><label class="premium-label" for="category">Category</label><input type="text" class="form-control" id="category" name="category" value="{{ old('category', $student->category) }}"></div>
                <div class="premium-field"><label class="premium-label" for="caste">Caste</label><input type="text" class="form-control" id="caste" name="caste" value="{{ old('caste', $student->caste) }}"></div>
            </div></div></div>
            <div class="col-12"><div class="student-section"><h3 class="student-section-title"><i class="bx bx-folder"></i>Documents</h3><p class="premium-muted mb-3">Preview the current upload or choose a replacement before saving.</p><div class="premium-form-grid">
                <div class="student-document-column">@foreach(['photo' => ['Student photo', 'image/jpeg,image/png,image/jpg,image/gif', 'bx-user'], 'aadhar_card' => ['Aadhaar card', '.pdf,image/jpeg,image/png', 'bx-id-card']] as $field => $details)
                <div><label class="premium-label" for="{{ $field }}">{{ $details[0] }}</label><input type="file" class="form-control document-upload" id="{{ $field }}" name="{{ $field }}" accept="{{ $details[1] }}" data-preview="{{ $field }}-preview"><div id="{{ $field }}-preview" class="student-document-preview">@if($student->$field) <div class="student-document-preview-meta"><i class="bx {{ $details[2] }}"></i><span><strong>Current upload</strong><small>Select a replacement only if needed.</small></span></div>@if($isImage($student->$field))<img src="{{ $fileUrl($student->$field) }}" alt="Current {{ strtolower($details[0]) }}">@else<iframe src="{{ $fileUrl($student->$field) }}" title="Current {{ strtolower($details[0]) }}"></iframe>@endif @else<div class="student-document-preview-empty"><i class="bx {{ $details[2] }}"></i><span>No upload yet. Choose a file to preview it here.</span></div>@endif</div></div>
                @endforeach</div>
                <div class="student-document-column">@foreach(['id_proof' => ['ID proof', 'bx-file'], 'category_certificate' => ['Category certificate', 'bx-award']] as $field => $details)
                <div><label class="premium-label" for="{{ $field }}">{{ $details[0] }}</label><input type="file" class="form-control document-upload" id="{{ $field }}" name="{{ $field }}" accept=".pdf,image/jpeg,image/png" data-preview="{{ $field }}-preview"><div id="{{ $field }}-preview" class="student-document-preview">@if($student->$field)<div class="student-document-preview-meta"><i class="bx {{ $details[1] }}"></i><span><strong>Current upload</strong><small>Select a replacement only if needed.</small></span></div>@if($isImage($student->$field))<img src="{{ $fileUrl($student->$field) }}" alt="Current {{ strtolower($details[0]) }}">@else<iframe src="{{ $fileUrl($student->$field) }}" title="Current {{ strtolower($details[0]) }}"></iframe>@endif @else<div class="student-document-preview-empty"><i class="bx {{ $details[1] }}"></i><span>No upload yet. Choose a file to preview it here.</span></div>@endif</div></div>
                @endforeach</div>
            </div></div></div>
        </div></div></div>
        <div class="premium-inline-actions mt-4"><button type="submit" class="premium-btn premium-btn-gold"><i class="bx bx-check-circle"></i> Update student</button><a href="{{ url('admin/students/'.$student->id) }}" class="premium-btn premium-btn-outline">Cancel</a></div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script><script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script><script src="{{ url('vertical/assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$(function () {
    const courseSubjects = @json($courses->mapWithKeys(fn ($course) => [$course->id => $course->subjects->map(fn ($subject) => ['id' => $subject->id, 'name' => $subject->name])->values()]));
    const selectedSubjects = @json($selectedSubjects); const subjectsContainer = $('#subjects-container'); const documentUrls = {};
    $('.premium-datepicker').bootstrapMaterialDatePicker({ time:false, format:'DD-MM-YYYY', clearButton:true, cancelText:'Cancel', okText:'Select' }); $('#date_of_birth').bootstrapMaterialDatePicker('setMaxDate', new Date());
    function escapeHtml(value) { return $('<div>').text(value || '').html(); }
    function renderSubjects(courseId) { const subjects = courseSubjects[courseId] || []; if (!subjects.length) { subjectsContainer.html('<span class="premium-muted">No subjects are available for this course.</span>'); return; } subjectsContainer.html(subjects.map(function (subject) { const checked = selectedSubjects.map(String).includes(String(subject.id)) ? ' checked' : ''; return `<span class="subject-choice"><input type="checkbox" name="subjects[]" value="${subject.id}" id="subject_${subject.id}"${checked}><label for="subject_${subject.id}">${escapeHtml(subject.name)}</label></span>`; }).join('')); }
    $('#course').on('change', function () { renderSubjects(this.value); }); renderSubjects($('#course').val());
    $('#phone_number').on('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 15); });
    // Support DD-MM-YYYY, YYYY-MM-DD and DD/MM/YYYY dates
    $.validator.methods.date = function (value, element) {
        if (this.optional(element)) return true;
        if (typeof moment !== 'undefined') {
            return moment(value, ['DD-MM-YYYY', 'YYYY-MM-DD', 'D-M-YYYY', 'DD/MM/YYYY', 'D/M/YYYY', 'YYYY/MM/DD'], true).isValid();
        }
        var norm = value.replace(/\//g, '-').split('-');
        if (norm.length === 3) {
            var d = parseInt(norm[0], 10), m = parseInt(norm[1], 10), y = parseInt(norm[2], 10);
            if (norm[0].length === 4) { y = parseInt(norm[0], 10); m = parseInt(norm[1], 10); d = parseInt(norm[2], 10); }
            var dt = new Date(y, m - 1, d);
            return dt.getFullYear() === y && (dt.getMonth() + 1) === m && dt.getDate() === d;
        }
        return !/Invalid|NaN/.test(new Date(value).toString());
    };

    $('.premium-datepicker').on('change', function () {
        if ($(this).valid) $(this).valid();
    });

    $.validator.addMethod('notFutureDate', function (value, element) {
        if (this.optional(element)) return true;
        if (typeof moment !== 'undefined') {
            const m = moment(value, ['DD-MM-YYYY', 'YYYY-MM-DD', 'DD/MM/YYYY'], true);
            return m.isValid() && m.isSameOrBefore(moment(), 'day');
        }
        return true;
    }, 'Date of birth cannot be in the future.');

    $.validator.addMethod('personName', function (value, element) {
        return this.optional(element) || /^[A-Za-z0-9 .\'-]+$/.test(value.trim());
    }, 'Use letters, numbers, spaces, dots, hyphens or apostrophes only.');

    $.validator.addMethod('studentPhoto', function (value, element) {
        if (this.optional(element)) return true;
        const file = element.files && element.files[0];
        return !!file && /image\/(jpeg|png|gif)/.test(file.type) && file.size <= 2 * 1024 * 1024;
    }, 'Upload a JPG, PNG or GIF image no larger than 2 MB.');

    $.validator.addMethod('studentDocument', function (value, element) {
        if (this.optional(element)) return true;
        const file = element.files && element.files[0];
        return !!file && (/application\/pdf|image\/(jpeg|png)/.test(file.type)) && file.size <= 2 * 1024 * 1024;
    }, 'Upload a PDF, JPG or PNG file no larger than 2 MB.');

    $('#editStudentForm').validate({
        errorElement: 'label',
        errorClass: 'error',
        rules: {
            roll_no: { required: true, maxlength: 255 },
            student_id: { required: true, maxlength: 255 },
            first_name: { required: true, maxlength: 100, personName: true },
            last_name: { maxlength: 100, personName: true },
            gender: { required: true },
            date_of_birth: { required: true, date: true, notFutureDate: true },
            phone_number: { required: true, digits: true, minlength: 10, maxlength: 15 },
            email: { required: true, email: true },
            father_name: { required: true, maxlength: 100, personName: true },
            mother_name: { required: true, maxlength: 100, personName: true },
            permanent_address: { required: true },
            correspondence_address: { required: true },
            course: { required: true },
            batch: { required: true },
            admission_date: { required: true, date: true },
            current_year: { required: true, range: [1, 4] },
            current_semester: { required: true, range: [1, 8] },
            status: { required: true },
            photo: { studentPhoto: true },
            aadhar_card: { studentDocument: true },
            id_proof: { studentDocument: true },
            category_certificate: { studentDocument: true }
        },
        messages: {
            first_name: { required: 'Please enter the first name.' },
            father_name: { required: "Please enter the father's name." },
            mother_name: { required: "Please enter the mother's name." },
            date_of_birth: { required: 'Please select or enter the date of birth.', date: 'Please enter a valid date (DD-MM-YYYY).' },
            admission_date: { required: 'Please select or enter the admission date.', date: 'Please enter a valid date (DD-MM-YYYY).' },
            phone_number: { required: 'Please enter a phone number.', digits: 'Phone number can contain digits only.', minlength: 'Enter at least 10 digits.', maxlength: 'Phone number cannot exceed 15 digits.' },
            course: 'Please select a course.'
        },
        errorPlacement: function (error, element) {
            const control = element.closest('.premium-date-control');
            control.length ? error.insertAfter(control) : error.insertAfter(element);
        },
        invalidHandler: function (event, validator) {
            const summary = $('#student-validation-summary');
            const count = validator.numberOfInvalids();
            summary.text(`Please correct ${count} highlighted ${count === 1 ? 'field' : 'fields'} before updating the student.`).removeClass('d-none');
            summary[0].focus();
        },
        submitHandler: function (form) {
            $('#student-validation-summary').addClass('d-none').empty();
            form.submit();
        },
        highlight: function (element) { $(element).addClass('is-invalid'); },
        unhighlight: function (element) { $(element).removeClass('is-invalid'); }
    });
});
</script>
@endsection
