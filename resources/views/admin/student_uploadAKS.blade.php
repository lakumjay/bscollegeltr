@extends('layouts.appadmin')

@section('content')
<div class="container mt-4">

    <h3>Upload Students CSV</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.students.uploadAKS.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        
        <!-- CSV Upload -->
        <div class="mb-3">
            <label class="form-label">Upload CSV File</label>
            <input type="file" name="csv_file" accept=".csv" class="form-control" required>
        </div>
 
        <button class="btn btn-primary mt-4">Upload</button>

    </form>
</div>
 
@endsection
