@extends('layouts.appadmin')


@section('content')
@php
    $subjectTotal = $courses->sum(function ($course) { return $course->subjects->count(); });
@endphp

<div class="container-fluid course-module">
    <div class="course-hero">
        <div class="course-heading-wrap">
            <span class="course-eyebrow">Academic configuration</span>
            <h1 class="course-title">Courses &amp; Subjects</h1>
            <p class="course-subtitle">Organise academic programmes and their subject structure.</p>
        </div>
        <div class="course-hero-actions">
            <button type="button" class="btn-premium-gold" data-bs-toggle="modal" data-bs-target="#AddCourseModal">
                <i class='bx bx-plus-circle'></i> Add new course
            </button>
        </div>
    </div>

    <div class="course-stats">
        <div class="course-stat">
            <span class="course-stat-icon"><i class='bx bx-book-bookmark'></i></span>
            <div><strong>{{ $courses->count() }}</strong><span>Total courses</span></div>
        </div>
        <div class="course-stat">
            <span class="course-stat-icon"><i class='bx bx-library'></i></span>
            <div><strong>{{ $subjectTotal }}</strong><span>Total subjects</span></div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="premium-data-card">
        <div class="premium-data-card-header">
            <div>
                <h3>Course directory</h3>
                <p>Search, review and manage your academic course catalogue.</p>
            </div>
        </div>

        <div class="premium-data-card-body">
            <div class="table-responsive">
                <table class="table course-table" id="coursesTable">
                    <thead>
                        <tr>
                            <th style="width:72px;">Sr.</th>
                            <th>Course name</th>
                            <th class="text-end" style="width:310px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr>
                                <td><span class="serial-badge">{{ $loop->iteration }}</span></td>
                                <td>
                                    <div class="course-name-cell">{{ $course->name }}</div>
                                    <span class="subject-count"><i class='bx bx-book-open'></i> {{ $course->subjects->count() }} {{ Str::plural('subject', $course->subjects->count()) }}</span>
                                </td>
                                <td>
                                    <div class="course-actions">
                                        <button type="button" class="course-action-btn btn-edit-gold" data-bs-toggle="modal" data-bs-target="#EditCourse{{ $course->id }}">
                                            <i class='bx bx-edit-alt'></i> Edit
                                        </button>
                                        <button type="button" class="course-action-btn btn-subject-gold" data-bs-toggle="modal" data-bs-target="#Subjects{{ $course->id }}">
                                            <i class='bx bx-book-content'></i> Subjects
                                        </button>
                                        {{-- Course deletion is intentionally hidden from the interface. Keep the route/controller available for authorised maintenance. --}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Course -->
<div class="modal fade course-modal" id="AddCourseModal" tabindex="-1" aria-labelledby="addCourseTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="modal-kicker">Course catalogue</span>
                    <h2 class="modal-title" id="addCourseTitle">Add a new course</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCourseForm" method="POST" action="{{ url('admin/course') }}" novalidate>
                @csrf
                <div class="modal-body">
                    <label for="courseName" class="premium-label">Course name</label>
                    <input type="text" class="form-control premium-control" id="courseName" name="name" placeholder="e.g. Bachelor of Commerce" maxlength="255" autocomplete="off">
                    <small class="d-block mt-2 text-muted">The serial number will be generated automatically.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-primary"><i class='bx bx-plus'></i> Add course</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($courses as $course)
    <!-- Edit Course -->
    <div class="modal fade course-modal" id="EditCourse{{ $course->id }}" tabindex="-1" aria-labelledby="editCourseTitle{{ $course->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <span class="modal-kicker">Course catalogue</span>
                        <h2 class="modal-title" id="editCourseTitle{{ $course->id }}">Edit course</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="edit-course-form" method="POST" action="{{ url('admin/course/'.$course->id) }}" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <label for="courseName{{ $course->id }}" class="premium-label">Course name</label>
                        <input type="text" class="form-control premium-control" id="courseName{{ $course->id }}" name="name" value="{{ $course->name }}" maxlength="255" autocomplete="off">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modal-primary"><i class='bx bx-check'></i> Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Subjects -->
    <div class="modal fade course-modal" id="Subjects{{ $course->id }}" tabindex="-1" aria-labelledby="subjectsTitle{{ $course->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <span class="modal-kicker">Subject management</span>
                        <h2 class="modal-title" id="subjectsTitle{{ $course->id }}">{{ $course->name }}</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="subjectMessage{{ $course->id }}" class="alert subject-message" role="alert"></div>

                    <form class="add-subject-form" data-course-id="{{ $course->id }}" method="POST" action="{{ url('admin/course/'.$course->id.'/subject') }}" novalidate>
                        @csrf
                        <input type="hidden" name="response_format" value="json">
                        <label for="subjectName{{ $course->id }}" class="premium-label">Add subject</label>
                        <div class="subject-add-row">
                            <input type="text" class="form-control premium-control" id="subjectName{{ $course->id }}" name="name" placeholder="Enter subject name" maxlength="255" autocomplete="off">
                            <button type="submit" class="btn-modal-primary"><i class='bx bx-plus'></i> Add subject</button>
                        </div>
                    </form>

                    <div class="subject-list" id="subjectsList{{ $course->id }}" data-course-id="{{ $course->id }}">
                        @forelse($course->subjects as $subject)
                            <div class="subject-row" data-subject-id="{{ $subject->id }}">
                                <div class="subject-view">
                                    <div class="subject-name-wrap">
                                        <span class="subject-icon"><i class='bx bx-book'></i></span>
                                        <span class="subject-name">{{ $subject->name }}</span>
                                    </div>
                                    <div class="subject-actions">
                                        <button type="button" class="course-action-btn btn-edit-gold js-edit-subject"><i class='bx bx-edit-alt'></i> Edit</button>
                                        <form class="delete-subject-form" data-course-id="{{ $course->id }}" method="POST" action="{{ url('admin/course/'.$course->id.'/subject/'.$subject->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="response_format" value="json">
                                            <button type="submit" class="course-action-btn btn-delete-soft"><i class='bx bx-trash'></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <form class="subject-edit-form d-none" data-course-id="{{ $course->id }}" method="POST" action="{{ url('admin/course/'.$course->id.'/subject/'.$subject->id) }}" novalidate>
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="response_format" value="json">
                                    <input type="text" class="form-control premium-control" name="name" value="{{ $subject->name }}" maxlength="255" autocomplete="off">
                                    <button type="submit" class="btn-modal-primary">Save</button>
                                    <button type="button" class="btn-modal-secondary js-cancel-subject">Cancel</button>
                                </form>
                            </div>
                        @empty
                            <div class="subject-empty"><i class='bx bx-book-add'></i>No subjects added yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@section('scripts')
<script src="{{ url('vertical/assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script>
$(function () {
    const csrfToken = @json(csrf_token());
    const courseBaseUrl = @json(route('admin.course.index', [], false));

    const validationOptions = {
        errorElement: 'span',
        errorClass: 'validation-error',
        highlight: function (element) { $(element).addClass('validation-invalid'); },
        unhighlight: function (element) { $(element).removeClass('validation-invalid'); },
        rules: {
            name: {
                required: true,
                minlength: 2,
                maxlength: 255,
                normalizer: function (value) { return $.trim(value); }
            }
        },
        messages: {
            name: {
                required: 'Please enter a name.',
                minlength: 'Please enter at least 2 characters.',
                maxlength: 'The name cannot exceed 255 characters.'
            }
        }
    };

    $('#addCourseForm').validate(validationOptions);
    $('.edit-course-form').each(function () { $(this).validate(validationOptions); });

    const courseTable = $('#coursesTable').DataTable({
        dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>',
        pageLength: 10,
        lengthChange: true,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        order: [[1, 'asc']],
        autoWidth: false,
        language: {
            search: '',
            searchPlaceholder: 'Search courses...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: 'No courses have been added yet.'
        },
        columnDefs: [
            { targets: 0, orderable: false, searchable: false },
            { targets: 2, orderable: false, searchable: false }
        ],
        drawCallback: function () {
            const api = this.api();
            const info = api.page.info();
            api.column(0, { page: 'current' }).nodes().each(function (cell, index) {
                $(cell).find('.serial-badge').text(info.start + index + 1);
            });
        }
    });

    function subjectUrl(courseId, subjectId) {
        return courseBaseUrl + '/' + courseId + '/subject' + (subjectId ? '/' + subjectId : '');
    }

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function subjectRow(courseId, subject) {
        const name = escapeHtml(subject.name);
        return `
            <div class="subject-row" data-subject-id="${subject.id}">
                <div class="subject-view">
                    <div class="subject-name-wrap">
                        <span class="subject-icon"><i class='bx bx-book'></i></span>
                        <span class="subject-name">${name}</span>
                    </div>
                    <div class="subject-actions">
                        <button type="button" class="course-action-btn btn-edit-gold js-edit-subject"><i class='bx bx-edit-alt'></i> Edit</button>
                        <form class="delete-subject-form" data-course-id="${courseId}" method="POST" action="${subjectUrl(courseId, subject.id)}">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="response_format" value="json">
                            <button type="submit" class="course-action-btn btn-delete-soft"><i class='bx bx-trash'></i> Delete</button>
                        </form>
                    </div>
                </div>
                <form class="subject-edit-form d-none" data-course-id="${courseId}" method="POST" action="${subjectUrl(courseId, subject.id)}" novalidate>
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="response_format" value="json">
                    <input type="text" class="form-control premium-control" name="name" value="${name}" maxlength="255" autocomplete="off">
                    <button type="submit" class="btn-modal-primary">Save</button>
                    <button type="button" class="btn-modal-secondary js-cancel-subject">Cancel</button>
                </form>
            </div>`;
    }

    function initializeSubjectEditValidation(scope) {
        $(scope).find('.subject-edit-form').each(function () {
            const form = $(this);
            if (form.data('validator')) return;
            form.validate($.extend(true, {}, validationOptions, {
                submitHandler: function (formElement) {
                    submitSubjectForm($(formElement), 'Subject updated successfully.');
                }
            }));
        });
    }

    function renderSubjects(courseId, subjects) {
        const list = $('#subjectsList' + courseId);
        if (!subjects.length) {
            list.html("<div class='subject-empty'><i class='bx bx-book-add'></i>No subjects added yet.</div>");
            return;
        }
        list.html(subjects.map(function (subject) { return subjectRow(courseId, subject); }).join(''));
        initializeSubjectEditValidation(list);
    }

    function showSubjectMessage(courseId, message, isError) {
        const alert = $('#subjectMessage' + courseId);
        alert.stop(true, true)
            .removeClass('alert-success alert-danger')
            .addClass(isError ? 'alert-danger' : 'alert-success')
            .text(message)
            .fadeIn(150);
        if (!isError) setTimeout(function () { alert.fadeOut(200); }, 3000);
    }

    function closeSubjectModal(courseId) {
        const modalElement = document.getElementById('Subjects' + courseId);
        if (!modalElement || typeof bootstrap === 'undefined') return;
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.hide();
    }

    function ajaxErrorMessage(xhr) {
        return xhr.responseJSON?.errors?.name?.[0] || xhr.responseJSON?.message || 'Something went wrong. Please try again.';
    }

    function updateSubjectRowFromForm(form) {
        if (!form.hasClass('subject-edit-form')) return;

        const row = form.closest('.subject-row');
        const name = $.trim(form.find('input[name="name"]').val());
        row.find('.subject-name').text(name);
        row.find('.subject-edit-form').addClass('d-none');
        row.find('.subject-view').removeClass('d-none');
    }

    function removeSubjectRow(form, courseId) {
        const row = form.closest('.subject-row');
        if (!row.length) return;

        row.fadeOut(160, function () {
            row.remove();
            const list = $('#subjectsList' + courseId);
            if (!list.find('.subject-row').length) {
                list.html("<div class='subject-empty'><i class='bx bx-book-add'></i>No subjects added yet.</div>");
            }
        });
    }

    function refreshSubjectsAfterSave(courseId, onComplete) {
        $.ajax({
            url: courseBaseUrl + 's/subjects',
            method: 'GET',
            data: { course: courseId },
            headers: { 'Accept': 'application/json' }
        }).done(function (response) {
            response = normaliseSubjectResponse(response);
            if (response && Array.isArray(response.subjects)) renderSubjects(courseId, response.subjects);
        }).always(onComplete);
    }

    function normaliseSubjectResponse(response) {
        if (typeof response !== 'string') return response || {};
        try { return JSON.parse(response); } catch (error) { return {}; }
    }

    function showSubjectSuccess(courseId, message) {
        showSubjectMessage(courseId, message, false);
        if (typeof Swal !== 'undefined') {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 2200, timerProgressBar: true });
        }
    }

    function submitSubjectForm(form, successMessage) {
        const courseId = form.data('course-id');
        const submitButton = form.find('[type="submit"]');
        submitButton.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).done(function (response) {
            response = normaliseSubjectResponse(response);
            if (response && Array.isArray(response.subjects)) {
                renderSubjects(courseId, response.subjects);
            } else {
                updateSubjectRowFromForm(form);
            }
            showSubjectSuccess(courseId, response.message || successMessage);
            if (form.hasClass('add-subject-form')) {
                form[0].reset();
                // Reload after a confirmed add so the course count and all subject
                // views are guaranteed to use the database's latest state.
                setTimeout(function () { window.location.reload(); }, 700);
            }
        }).fail(function (xhr) {
            // The local server can occasionally fail after committing the database
            // transaction. Re-read the authoritative subject list in that case.
            if (xhr.status !== 419 && xhr.status !== 422 && xhr.status !== 0) {
                refreshSubjectsAfterSave(courseId, function () {
                    if (form.hasClass('subject-edit-form')) updateSubjectRowFromForm(form);
                    if (form.hasClass('add-subject-form')) {
                        form[0].reset();
                        setTimeout(function () { window.location.reload(); }, 700);
                    }
                    showSubjectSuccess(courseId, successMessage);
                });
                return;
            }
            showSubjectMessage(courseId, ajaxErrorMessage(xhr), true);
        }).always(function () {
            submitButton.prop('disabled', false);
        });
    }

    $('.add-subject-form').each(function () {
        const form = $(this);
        form.validate($.extend(true, {}, validationOptions, {
            errorPlacement: function (error, element) { error.insertAfter(element.closest('.subject-add-row')); },
            submitHandler: function (formElement) {
                submitSubjectForm($(formElement), 'Subject added successfully.');
            }
        }));
    });

    initializeSubjectEditValidation(document);

    $(document).on('click', '.js-edit-subject', function () {
        const row = $(this).closest('.subject-row');
        row.find('.subject-view').addClass('d-none');
        row.find('.subject-edit-form').removeClass('d-none').find('input[name="name"]').trigger('focus').select();
    });

    $(document).on('click', '.js-cancel-subject', function () {
        const row = $(this).closest('.subject-row');
        row.find('.subject-edit-form').addClass('d-none');
        row.find('.subject-view').removeClass('d-none');
    });

    $(document).on('submit', '.delete-subject-form', function (event) {
        event.preventDefault();
        const form = $(this);
        const courseId = form.data('course-id');
        Swal.fire({
            title: 'Delete subject?',
            text: 'This subject will be removed from the course.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#b44955',
            cancelButtonColor: '#748096'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).done(function (response) {
                response = normaliseSubjectResponse(response);
                removeSubjectRow(form, courseId);
                if (response && Array.isArray(response.subjects)) renderSubjects(courseId, response.subjects);
                showSubjectSuccess(courseId, response.message || 'Subject deleted successfully.');
            }).fail(function (xhr) {
                if (xhr.status !== 419 && xhr.status !== 422 && xhr.status !== 0) {
                    removeSubjectRow(form, courseId);
                    refreshSubjectsAfterSave(courseId, function () {
                        showSubjectSuccess(courseId, 'Subject deleted successfully.');
                    });
                    return;
                }
                showSubjectMessage(courseId, ajaxErrorMessage(xhr), true);
            });
        });
    });

    $('.course-delete-form').on('submit', function (event) {
        event.preventDefault();
        const form = $(this);
        Swal.fire({
            title: 'Delete course?',
            text: 'This course and all of its subjects will be permanently removed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#b44955',
            cancelButtonColor: '#748096'
        }).then(function (result) {
            if (result.isConfirmed) form[0].submit();
        });
    });
});
</script>
@endsection
