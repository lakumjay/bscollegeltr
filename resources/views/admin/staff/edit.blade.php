@extends('layouts.appadmin')

@section('css')
<link rel="stylesheet" href="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="container-fluid premium-page staff-form-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">People operations</span><h1 class="premium-page-title">Edit {{ $staff->first_name }} {{ $staff->last_name }}</h1><p class="premium-page-subtitle">Update staff details while keeping existing documents available for review.</p></div>
        <div class="premium-header-actions"><a href="{{ route('admin.staff.show', $staff) }}" class="premium-btn premium-btn-outline"><i class="bx bx-show"></i> View profile</a><a href="{{ route('admin.staff.index') }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Staff directory</a></div>
    </div>
    <div class="premium-card"><div class="premium-card-header"><div><h2>Staff profile</h2><p>Replace a document only when an updated version is required.</p></div></div><div class="premium-card-body">
        @include('admin.staff._form', ['staff' => $staff])
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>
<script src="{{ url('vertical/assets/js/staff-form.js') }}"></script>
@endsection
