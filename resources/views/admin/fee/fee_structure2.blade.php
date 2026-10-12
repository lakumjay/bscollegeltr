@extends('layouts.appadmin')

@section('content')
<div class="premium-page premium-page--compact fee-structure-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Fee management</span><h1>Fee structure</h1><p>Set the semester-wise amount for each fee head and course.</p></div>
    </section>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <section class="premium-content-card fee-structure-picker-card">
        <form method="GET" action="{{ route('fee_structure.index') }}" class="fee-structure-picker" id="feeStructurePicker">
            <div class="premium-field">
                <label for="feeStructureCourse">Course</label>
                <select id="feeStructureCourse" name="course_id" required>
                    <option value="">Select a course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (int) $selectedCourseId === (int) $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </section>

    @if($selectedCourse)
        <form method="POST" action="{{ route('fee_structure.store') }}" class="fee-structure-form">
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
            <section class="premium-content-card fee-structure-card">
                <div class="premium-card-heading">
                    <div><span class="premium-eyebrow">Course structure</span><h2>{{ $selectedCourse->name }}</h2></div>
                    <span class="premium-count-badge">{{ $fees->count() }} fee heads</span>
                </div>
                <div class="fee-structure-card-body">
                    <div class="premium-table-wrap">
                        <table class="premium-table fee-structure-table">
                            <thead><tr><th class="fee-structure-head-column">Fee head</th>@for($i = 1; $i <= 8; $i++)<th>Sem {{ $i }}</th>@endfor</tr></thead>
                            <tbody>
                                @foreach($fees as $index => $fee)
                                    <tr>
                                        <td>{{ $fee['head_name'] }}<input type="hidden" name="fees[{{ $index }}][head_name]" value="{{ $fee['head_name'] }}"></td>
                                        @for($i = 1; $i <= 8; $i++)
                                            <td><input type="number" min="0" step="0.01" class="form-control" name="fees[{{ $index }}][sem{{ $i }}]" value="{{ old('fees.' . $index . '.sem' . $i, $fee['sem' . $i] ?? 0) }}"></td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <div class="premium-action-row fee-structure-actions"><button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-save"></i> Save selected course</button></div>
        </form>
    @else
        <section class="premium-content-card premium-empty-state"><i class="fa fa-graduation-cap"></i><h2>Select a course to begin</h2><p>Fee amounts are loaded and saved only for the course selected above.</p></section>
    @endif

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var courseSelector = document.getElementById('feeStructureCourse');

        if (courseSelector) {
            courseSelector.addEventListener('change', function () {
                if (this.value) {
                    document.getElementById('feeStructurePicker').submit();
                }
            });
        }
    });
</script>
@endsection
