@extends('layouts.appadmin')

@section('css')
<link rel="stylesheet" href="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="container-fluid premium-page staff-form-page">
    <div class="premium-page-header">
        <div><span class="premium-page-eyebrow">People operations</span><h1 class="premium-page-title">Add staff member</h1><p class="premium-page-subtitle">Create a staff profile with employment details and supporting documents.</p></div>
        <div class="premium-header-actions"><a href="{{ route('admin.staff.index') }}" class="premium-btn premium-btn-outline"><i class="bx bx-arrow-back"></i> Staff directory</a></div>
    </div>
    <div class="premium-card"><div class="premium-card-header"><div><h2>Staff profile</h2><p>Fields marked with an asterisk are required.</p></div></div><div class="premium-card-body">
        @include('admin.staff._form', ['staff' => null])
    </div></div>
</div>
@endsection

@section('scripts')
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>
<script src="{{ url('vertical/assets/js/staff-form.js') }}"></script>
@endsection
