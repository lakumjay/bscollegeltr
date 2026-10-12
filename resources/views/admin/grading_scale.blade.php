@extends('layouts.appadmin')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa fa-ruler me-2"></i>Manage Grading Scales</h4>
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

                    <form method="post" action="{{ route('exam_management.save_grading_scale') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Scale Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div id="scale-container">
                            <div class="scale-row row g-3 mb-2">
                                <div class="col-md-4">
                                    <label class="form-label">Min Score *</label>
                                    <input type="number" class="form-control" name="scale[0][min]" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Max Score *</label>
                                    <input type="number" class="form-control" name="scale[0][max]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Grade *</label>
                                    <input type="text" class="form-control" name="scale[0][grade]" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-scale"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" id="add-scale"><i class="fa fa-plus me-2"></i>Add Scale</button>
                        <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-save me-2"></i>Save Scale</button>
                    </form>

                    <h4 class="mt-4">Existing Grading Scales</h4>
                    @if($gradingScales->isEmpty())
                        <div class="alert alert-info">No grading scales found.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Scale</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradingScales as $scale)
                                        <tr>
                                            <td>{{ $scale->name }}</td>
                                            <td>
                                                @foreach($scale->scale as $s)
                                                    {{ $s['min'] }}-{{ $s['max'] }}: {{ $s['grade'] }}<br>
                                                @endforeach
                                            </td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let scaleCount = 1;

    $('#add-scale').click(function() {
        let html = `
            <div class="scale-row row g-3 mb-2">
                <div class="col-md-4">
                    <label class="form-label">Min Score *</label>
                    <input type="number" class="form-control" name="scale[${scaleCount}][min]" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Max Score *</label>
                    <input type="number" class="form-control" name="scale[${scaleCount}][max]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Grade *</label>
                    <input type="text" class="form-control" name="scale[${scaleCount}][grade]" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-scale"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        `;
        $('#scale-container').append(html);
        scaleCount++;
    });

    $(document).on('click', '.remove-scale', function() {
        if ($('.scale-row').length > 1) {
            $(this).closest('.scale-row').remove();
        }
    });
});
</script>

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