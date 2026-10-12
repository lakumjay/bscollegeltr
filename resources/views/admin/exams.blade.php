@extends('layouts.appadmin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa fa-book me-2"></i>Manage Exams</h4>
                </div>
                <div class="card-body bg-light">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="post" action="{{ route('exam_management.save_exam') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="course_id" class="form-label">Course *</label>
                                <select class="form-control" id="course_id" name="course_id" required>
                                    <option value="">Select a Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="name" class="form-label">Exam Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-3">
                                <label for="grading_scale_id" class="form-label">Grading Scale *</label>
                                <select class="form-control" id="grading_scale_id" name="grading_scale_id" required>
                                    <option value="">Select a Scale</option>
                                    @foreach($gradingScales as $scale)
                                        <option value="{{ $scale->id }}">{{ $scale->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="max_marks" class="form-label">Max Marks per Subject *</label>
                                <input type="number" step="0.01" class="form-control" id="max_marks" name="max_marks" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-save me-2"></i>Create Exam</button>
                    </form>

                    <h4 class="mt-4">Existing Exams</h4>
                    @if($exams->isEmpty())
                        <div class="alert alert-info">No exams found.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Course</th>
                                        <th>Exam Name</th>
                                        <th>Grading Scale</th>
                                        <th>Max Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exams as $exam)
                                        <tr>
                                            <td>{{ $exam->course->name }}</td>
                                            <td>{{ $exam->name }}</td>
                                            <td>{{ $exam->gradingScale->name }}</td>
                                            <td>{{ $exam->max_marks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0;
    padding: 1rem 1.5rem;
}
.table th, .table td {
    vertical-align: middle;
}
.btn {
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-2px);
}
.alert {
    border-radius: 8px;
}
.form-control {
    border-radius: 6px;
}
</style>
@endsection