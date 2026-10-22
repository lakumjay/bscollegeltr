@extends('layouts.appadmin')

@section('css')
<link rel="stylesheet" href="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="container-fluid premium-page student-admission-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">Student management</span><h1 class="premium-page-title">New Student Admission</h1><p class="premium-page-subtitle">Create a complete student profile with academic details and supporting documents.</p></div>
        <div class="premium-header-actions"><a href="{{ url('admin/students/strength') }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Student strength</a></div>
    </div>

    @if($errors->any())<div class="alert alert-danger border-0 shadow-sm"><strong>Please correct the highlighted fields.</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form id="createStudentForm" method="post" action="{{ url('admin/students') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <div id="student-validation-summary" class="student-validation-summary d-none" role="alert" tabindex="-1"></div>
        <div class="premium-card">
            <div class="premium-card-header"><div><h2>Student admission details</h2><p>Complete the personal, academic and document information in one form.</p></div></div>
            <div class="premium-card-body"><div class="row g-4">
            <div class="col-xl-7">
                <div class="student-section h-100">
                    <div class="student-section"><h3 class="student-section-title"><i class="bx bx-id-card"></i> Identity</h3><div class="premium-form-grid">
                        <div class="premium-field"><label class="premium-label" for="reg_no">Registration number</label><input type="text" class="form-control" id="reg_no" name="reg_no" value="{{ old('reg_no') }}"></div>
                        <div class="premium-field"><label class="premium-label" for="roll_no">Roll number <span class="text-danger">*</span></label><input type="text" class="form-control" id="roll_no" name="roll_no" value="{{ old('roll_no', $next_roll_no) }}" required></div>
                        <div class="premium-field"><label class="premium-label" for="student_id">Student ID <span class="text-danger">*</span></label><input type="text" class="form-control" id="student_id" name="student_id" value="{{ old('student_id', $next_student_id) }}" required></div>
                        <div class="premium-field"><label class="premium-label" for="first_name">First name <span class="text-danger">*</span></label><input type="text" class="form-control name-input" id="first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" maxlength="100" required></div>
                        <div class="premium-field"><label class="premium-label" for="last_name">Last name</label><input type="text" class="form-control name-input" id="last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" maxlength="100"></div>
                        <div class="premium-field"><label class="premium-label" for="gender">Gender <span class="text-danger">*</span></label><select class="form-select" id="gender" name="gender" required><option value="">Select gender</option><option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option><option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option><option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option></select></div>
                        <div class="premium-field"><label class="premium-label" for="date_of_birth">Date of birth <span class="text-danger">*</span></label><div class="premium-date-control"><input type="text" class="form-control premium-datepicker" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" autocomplete="off" placeholder="Select date of birth" required><i class="bx bx-calendar"></i></div></div>
                        <div class="premium-field"><label class="premium-label" for="phone_number">Phone number <span class="text-danger">*</span></label><input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" inputmode="numeric" autocomplete="tel" pattern="[0-9]{10,15}" minlength="10" maxlength="15" required><p class="premium-muted mb-0 mt-2">Enter 10 to 15 digits only.</p></div>
                        <div class="premium-field"><label class="premium-label" for="email">Email <span class="text-danger">*</span></label><input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required></div>
                    </div></div>
                    <div class="student-section"><h3 class="student-section-title"><i class="bx bx-group"></i> Family and address</h3><div class="premium-form-grid">
                        <div class="premium-field"><label class="premium-label" for="father_name">Father's name <span class="text-danger">*</span></label><input type="text" class="form-control name-input" id="father_name" name="father_name" value="{{ old('father_name') }}" autocomplete="name" maxlength="100" required></div>
                        <div class="premium-field"><label class="premium-label" for="mother_name">Mother's name <span class="text-danger">*</span></label><input type="text" class="form-control name-input" id="mother_name" name="mother_name" value="{{ old('mother_name') }}" autocomplete="name" maxlength="100" required></div>
                        <div class="premium-field student-address-field"><label class="premium-label" for="permanent_address">Permanent address <span class="text-danger">*</span></label><textarea class="form-control" id="permanent_address" name="permanent_address" required>{{ old('permanent_address') }}</textarea></div>
                        <div class="premium-field student-address-field"><label class="premium-label" for="correspondence_address">Correspondence address <span class="text-danger">*</span></label><textarea class="form-control" id="correspondence_address" name="correspondence_address" required>{{ old('correspondence_address') }}</textarea></div>
                    </div></div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="student-section"><h3 class="student-section-title"><i class="bx bx-book-reader"></i> Academic details</h3><p class="premium-muted mb-3">Select course first; subject selection is optional.</p><div class="premium-form-grid">
                    <div class="premium-field-wide"><label class="premium-label" for="course">Course <span class="text-danger">*</span></label><select class="form-select" id="course" name="course" required><option value="">Select course</option>@foreach($courses as $course)<option value="{{ $course->id }}" {{ old('course') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>@endforeach</select></div>
                    <div class="premium-field-wide"><label class="premium-label">Subjects <span class="premium-muted">(optional)</span></label><div id="subjects-container" class="subject-options"><span class="premium-muted">Select a course to load subjects.</span></div></div>
                    <div class="premium-field"><label class="premium-label" for="batch">Batch <span class="text-danger">*</span></label><input type="text" class="form-control" id="batch" name="batch" list="batchOptions" placeholder="e.g. 2026-2030" value="{{ old('batch', $defaultBatch) }}" required>
                    <datalist id="batchOptions">
                        @foreach($allBatches ?? ['2026-2030', '2026-30'] as $b)
                            <option value="{{ $b }}">
                        @endforeach
                    </datalist></div>
                    <div class="premium-field"><label class="premium-label" for="admission_date">Admission date <span class="text-danger">*</span></label><div class="premium-date-control"><input type="text" class="form-control premium-datepicker" id="admission_date" name="admission_date" value="{{ old('admission_date', now()->format('d-m-Y')) }}" autocomplete="off" placeholder="Select admission date" required><i class="bx bx-calendar"></i></div></div>
                    <div class="premium-field"><label class="premium-label" for="current_year">Current year <span class="text-danger">*</span></label><input type="number" class="form-control" id="current_year" name="current_year" min="1" max="4" value="{{ old('current_year', 1) }}" required></div>
                    <div class="premium-field"><label class="premium-label" for="current_semester">Current semester <span class="text-danger">*</span></label><input type="number" class="form-control" id="current_semester" name="current_semester" min="1" max="8" value="{{ old('current_semester') }}" required></div>
                    <div class="premium-field"><label class="premium-label" for="status">Status <span class="text-danger">*</span></label><select class="form-select" id="status" name="status" required><option value="">Select status</option><option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option><option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option></select></div>
                    <div class="premium-field"><label class="premium-label" for="special_category">Special category</label><select class="form-select" id="special_category" name="special_category"><option value="">None</option>@foreach($special_categories as $item)<option value="{{ $item->id }}" {{ old('special_category') == $item->id ? 'selected' : '' }}>{{ $item->category_name }}</option>@endforeach</select></div>
                    <div class="premium-field"><label class="premium-label" for="category">Category</label><select class="form-select" id="category" name="category"><option value="General" {{ old('category', 'General') === 'General' ? 'selected' : '' }}>General</option><option value="OBC" {{ old('category') === 'OBC' ? 'selected' : '' }}>OBC</option><option value="SC" {{ old('category') === 'SC' ? 'selected' : '' }}>SC</option><option value="ST" {{ old('category') === 'ST' ? 'selected' : '' }}>ST</option></select></div>
                    <div class="premium-field"><label class="premium-label" for="caste">Caste</label><input type="text" class="form-control" id="caste" name="caste" value="{{ old('caste') }}"></div>
                </div></div>
            </div>
            <div class="col-12"><div class="student-section"><h3 class="student-section-title"><i class="bx bx-folder"></i>Documents</h3><p class="premium-muted mb-3">Choose a file to preview it before saving the student.</p><div class="premium-form-grid">
                <div class="student-document-column"><div><label class="premium-label" for="photo">Student photo</label><input type="file" class="form-control document-upload" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif" data-preview="photo-preview"><div id="photo-preview" class="student-document-preview" aria-live="polite"><div class="student-document-preview-empty"><i class="bx bx-user"></i><span>Choose a JPG, PNG or GIF to preview the student photo here.</span></div></div><p class="premium-muted mb-0 mt-2">Image files only, up to 2 MB.</p></div><div><label class="premium-label" for="aadhar_card">Aadhaar card</label><input type="file" class="form-control document-upload" id="aadhar_card" name="aadhar_card" accept=".pdf,image/jpeg,image/png" data-preview="aadhar-preview"><div id="aadhar-preview" class="student-document-preview" aria-live="polite"><div class="student-document-preview-empty"><i class="bx bx-id-card"></i><span>Choose a JPG, PNG or PDF to preview it here.</span></div></div></div></div>
                <div class="student-document-column"><div><label class="premium-label" for="id_proof">ID proof</label><input type="file" class="form-control document-upload" id="id_proof" name="id_proof" accept=".pdf,image/jpeg,image/png" data-preview="id-proof-preview"><div id="id-proof-preview" class="student-document-preview" aria-live="polite"><div class="student-document-preview-empty"><i class="bx bx-file"></i><span>Choose a JPG, PNG or PDF to preview it here.</span></div></div></div><div><label class="premium-label" for="category_certificate">Category certificate</label><input type="file" class="form-control document-upload" id="category_certificate" name="category_certificate" accept=".pdf,image/jpeg,image/png" data-preview="category-certificate-preview"><div id="category-certificate-preview" class="student-document-preview" aria-live="polite"><div class="student-document-preview-empty"><i class="bx bx-award"></i><span>Choose a JPG, PNG or PDF to preview it here.</span></div></div></div></div>
            </div></div></div>
            </div></div>
        </div>
        <div class="premium-inline-actions mt-4"><button type="submit" class="premium-btn premium-btn-gold"><i class="bx bx-check-circle"></i> Create student</button><a href="{{ url('admin/students/strength') }}" class="premium-btn premium-btn-outline">Cancel</a></div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>
<script src="{{ url('vertical/assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$(function () {
    const courseSubjects = @json($courses->mapWithKeys(fn ($course) => [$course->id => $course->subjects->map(fn ($subject) => ['id' => $subject->id, 'name' => $subject->name])->values()]));
    const selectedSubjects = @json(old('subjects', []));
    const subjectsContainer = $('#subjects-container');

    $('.premium-datepicker').bootstrapMaterialDatePicker({
        time: false,
        format: 'DD-MM-YYYY',
        clearButton: true,
        cancelText: 'Cancel',
        okText: 'Select'
    });
    $('#date_of_birth').bootstrapMaterialDatePicker('setMaxDate', new Date());

    $('#phone_number').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 15);
    });

    function escapeHtml(value) { return $('<div>').text(value || '').html(); }
    function emptyDocumentPreview(preview) {
        preview.html('<div class="student-document-preview-empty"><i class="bx bx-file"></i><span>Choose a JPG, PNG or PDF to preview it here.</span></div>');
    }
    function loadSubjects(courseId) {
        if (!courseId) { subjectsContainer.html('<span class="premium-muted">Select a course to load subjects.</span>'); return; }
        subjectsContainer.html('<span class="premium-muted"><i class="bx bx-loader-alt bx-spin"></i> Loading subjects...</span>');
        const subjects = courseSubjects[courseId] || [];
        if (!subjects.length) { subjectsContainer.html('<span class="premium-muted">No subjects are available for this course.</span>'); return; }
        subjectsContainer.html(subjects.map(function (subject) {
            const checked = selectedSubjects.map(String).includes(String(subject.id)) ? ' checked' : '';
            return `<span class="subject-choice"><input type="checkbox" name="subjects[]" value="${subject.id}" id="subject_${subject.id}"${checked}><label for="subject_${subject.id}">${escapeHtml(subject.name)}</label></span>`;
        }).join(''));
    }

    $('#course').on('change', function () { loadSubjects(this.value); });
    if ($('#course').val()) loadSubjects($('#course').val());

    const documentUrls = {};
    $('.document-upload').on('change', function () {
        const file = this.files && this.files[0];
        const preview = $('#' + $(this).data('preview'));
        const previewId = preview.attr('id');

        if (documentUrls[previewId]) URL.revokeObjectURL(documentUrls[previewId]);
        preview.empty();
        if (!file) { emptyDocumentPreview(preview); return; }

        const objectUrl = URL.createObjectURL(file);
        documentUrls[previewId] = objectUrl;
        const fileName = escapeHtml(file.name);
        const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        let content = `<div class="student-document-preview-meta"><i class="bx bx-file"></i><span><strong>${fileName}</strong><small>${fileSize}</small></span></div>`;

        if (file.type.indexOf('image/') === 0) {
            content += `<img src="${objectUrl}" alt="Preview of ${fileName}">`;
        } else if (file.type === 'application/pdf') {
            content += `<iframe src="${objectUrl}" title="Preview of ${fileName}"></iframe>`;
        }

        preview.html(content);
    });

    $.validator.addMethod('notFutureDate', function (value) {
        if (!value) return true;
        const selectedDate = new Date(value + 'T00:00:00');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return !Number.isNaN(selectedDate.getTime()) && selectedDate <= today;
    }, 'Date of birth cannot be in the future.');
    $.validator.addMethod('personName', function (value, element) {
        return this.optional(element) || /^[A-Za-z]+(?:[ '\-][A-Za-z]+)*$/.test(value.trim());
    }, 'Use letters, spaces, hyphens or apostrophes only.');
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

    $('#createStudentForm').validate({
        errorElement: 'label', errorClass: 'error',
        onkeyup: function (element) { $(element).valid(); },
        onclick: function (element) { $(element).valid(); },
        rules: {
            roll_no: { required: true, maxlength: 255 }, student_id: { required: true, maxlength: 255 }, first_name: { required: true, maxlength: 100, personName: true }, last_name: { maxlength: 100, personName: true }, gender: { required: true }, date_of_birth: { required: true, date: true, notFutureDate: true }, phone_number: { required: true, digits: true, minlength: 10, maxlength: 15 }, email: { required: true, email: true }, father_name: { required: true, maxlength: 100, personName: true }, mother_name: { required: true, maxlength: 100, personName: true }, permanent_address: { required: true }, correspondence_address: { required: true }, course: { required: true }, batch: { required: true }, admission_date: { required: true, date: true }, current_year: { required: true, range: [1, 4] }, current_semester: { required: true, range: [1, 8] }, status: { required: true }, photo: { studentPhoto: true }, aadhar_card: { studentDocument: true }, id_proof: { studentDocument: true }, category_certificate: { studentDocument: true }
        },
        messages: { first_name: { required: 'Please enter the first name.' }, father_name: { required: "Please enter the father's name." }, mother_name: { required: "Please enter the mother's name." }, date_of_birth: { required: 'Please select the date of birth.', date: 'Please choose a valid date.' }, phone_number: { required: 'Please enter a phone number.', digits: 'Phone number can contain digits only.', minlength: 'Enter at least 10 digits.', maxlength: 'Phone number cannot exceed 15 digits.' }, course: 'Please select a course.', admission_date: 'Please choose the admission date.' },
        errorPlacement: function (error, element) {
            const dateControl = element.closest('.premium-date-control');
            if (dateControl.length) error.insertAfter(dateControl);
            else error.insertAfter(element);
        },
        invalidHandler: function (event, validator) {
            const summary = $('#student-validation-summary');
            const count = validator.numberOfInvalids();
            summary.text(`Please correct ${count} highlighted ${count === 1 ? 'field' : 'fields'} before creating the student.`).removeClass('d-none');
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
