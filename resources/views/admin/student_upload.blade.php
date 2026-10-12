@extends('layouts.appadmin')

@section('content')
<div class="container mt-4">

    <h3>Upload Students CSV</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.students.upload.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Course select -->
        <div class="mb-3">
            <label class="form-label">Select Course</label>
            <select name="course_id" class="form-control" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- CSV Upload -->
        <div class="mb-3">
            <label class="form-label">Upload CSV File</label>
            <input type="file" name="csv_file" accept=".csv" class="form-control" required>
        </div>

        <hr>

        <h5>Add Session + Semester</h5>

        <div id="sessionRows">
            <div class="row mb-2 session-row">
                <div class="col-md-5">
                    <select name="session_id[]" class="form-control" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}">{{ $s->session_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <select name="semester[]" class="form-control" required>
                        <option value="">Select Semester</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3">Semester 3</option>
                        <option value="4">Semester 4</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-danger removeRow">X</button>
                </div>
            </div>
        </div>

        <button type="button" id="addRow" class="btn btn-secondary mt-2">+ Add More</button>

        <button class="btn btn-primary mt-4">Upload</button>

    </form>
</div>

<script>
// ADD NEW ROW
document.getElementById('addRow').addEventListener('click', function () {

    let html = `
    <div class="row mb-2 session-row">
        <div class="col-md-5">
            <select name="session_id[]" class="form-control" required>
                <option value="">Select Session</option>
                @foreach($sessions as $s)
                    <option value="{{ $s->id }}">{{ $s->session_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5">
            <select name="semester[]" class="form-control" required>
                <option value="">Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
                <option value="3">Semester 3</option>
                <option value="4">Semester 4</option>
            </select>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger removeRow">X</button>
        </div>
    </div>`;

    document.getElementById('sessionRows').insertAdjacentHTML('beforeend', html);
});

// REMOVE ROW
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.session-row').remove();
    }
});
</script>
@endsection
