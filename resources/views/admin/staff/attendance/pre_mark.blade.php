@extends('layouts.appadmin')

@section('css')
<link rel="stylesheet" href="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.min.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="container-fluid premium-page attendance-date-page"><div class="premium-page-header"><div><span class="premium-page-eyebrow">Staff attendance</span><h1 class="premium-page-title">Select attendance date</h1><p class="premium-page-subtitle">Choose a working date before opening the staff attendance register.</p></div></div><section class="premium-card attendance-date-card"><div class="premium-card-header"><div><h2>Attendance register date</h2><p>You can return and update this date at any time.</p></div></div><div class="premium-card-body"><form method="get" action="{{ route('admin.staff.attendance.mark') }}" class="premium-form-grid"><div class="premium-field"><label for="date" class="premium-label">Date <span class="text-danger">*</span></label><div class="premium-date-control"><input type="text" name="date" id="date" class="form-control attendance-datepicker" value="{{ $date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : now()->format('d-m-Y') }}" autocomplete="off" required><i class="bx bx-calendar"></i></div>@error('date')<span class="text-danger small">{{ $message }}</span>@enderror</div><div class="premium-field d-flex align-items-end"><button type="submit" class="premium-btn premium-btn-gold"><i class="bx bx-right-arrow-alt"></i> Open register</button></div></form></div></section></div>
@endsection

@section('scripts')
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/moment.min.js') }}"></script>
<script src="{{ url('vertical/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.min.js') }}"></script>
<script>$('.attendance-datepicker').bootstrapMaterialDatePicker({ time:false, format:'DD-MM-YYYY', clearButton:true, cancelText:'Cancel', okText:'Select' });</script>
@endsection
