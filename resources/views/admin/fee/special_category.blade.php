@extends('layouts.appadmin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Special Discount Categories</div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="get" action="{{ route('special_category.index') }}">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Select Class/Course *</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                <option value="">Select a Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>

                    @if($selectedCourseId)
                        @if($feeHeads->isEmpty())
                            <div class="alert alert-warning mt-3">
                                No fee heads defined for this course. Please create fee heads in the <a href="{{ route('fee_structure.index', ['course_id' => $selectedCourseId]) }}">Fee Structure</a> section first.
                            </div>
                        @else
                            <form method="post" action="{{ route('special_category.store') }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                                <div id="categories">
                                    @foreach($specialCategories as $catIndex => $category)
                                        <div class="category mb-4 border p-3" data-id="{{ $catIndex }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Category Name *</label>
                                                    <input type="text" class="form-control" name="categories[{{ $catIndex }}][category_name]" value="{{ $category->category_name }}" required>
                                                    <input type="hidden" name="categories[{{ $catIndex }}][id]" value="{{ $category->id }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-danger remove-category mt-4">Remove Category</button>
                                                </div>
                                            </div>
                                            <div class="fee-heads mt-3">
                                                @foreach($category->feeHeads as $feeIndex => $feeHead)
                                                    <div class="fee-head row mb-2" data-id="{{ $feeIndex }}">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Fee Head *</label>
                                                            <select class="form-control" name="categories[{{ $catIndex }}][fee_heads][{{ $feeIndex }}][fee_head_id]" required>
                                                                <option value="">Select Fee Head</option>
                                                                @foreach($feeHeads as $fh)
                                                                    <option value="{{ $fh->fee_head_id }}" {{ $feeHead->fee_head_id == $fh->fee_head_id ? 'selected' : '' }}>{{ $fh->fee_head_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="categories[{{ $catIndex }}][fee_heads][{{ $feeIndex }}][id]" value="{{ $feeHead->id }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Discount Type *</label>
                                                            <select class="form-control" name="categories[{{ $catIndex }}][fee_heads][{{ $feeIndex }}][discount_type]" required>
                                                                <option value="Flat" {{ $feeHead->discount_type === 'Flat' ? 'selected' : '' }}>Flat</option>
                                                                <option value="Percentage" {{ $feeHead->discount_type === 'Percentage' ? 'selected' : '' }}>Percentage</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Discount *</label>
                                                            <input type="number" step="0.01" class="form-control" name="categories[{{ $catIndex }}][fee_heads][{{ $feeIndex }}][discount]" value="{{ $feeHead->discount }}" required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger remove-fee-head mt-4">X</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-secondary add-fee-head mt-2">+ Add Fee Head</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" id="addCategory" class="btn btn-secondary mt-3">+ Add Category</button>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">Save Categories</button>
                                    <a href="{{ route('special_category.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let categoryCount = {{ $specialCategories->count() }};
    let feeHeadCounts = {};

    // Initialize fee head counts for each category
    @foreach($specialCategories as $catIndex => $category)
        feeHeadCounts[{{ $catIndex }}] = {{ $category->feeHeads->count() }};
    @endforeach

    // Add new category
    $('#addCategory').click(function() {
        feeHeadCounts[categoryCount] = 0;
        let html = `
            <div class="category mb-4 border p-3" data-id="${categoryCount}">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="categories[${categoryCount}][category_name]" required>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger remove-category mt-4">Remove Category</button>
                    </div>
                </div>
                <div class="fee-heads mt-3"></div>
                <button type="button" class="btn btn-secondary add-fee-head mt-2">+ Add Fee Head</button>
            </div>
        `;
        $('#categories').append(html);
        categoryCount++;
    });

    // Add fee head to a category
    $(document).on('click', '.add-fee-head', function() {
        let category = $(this).closest('.category');
        let catId = category.data('id');
        let feeHeadCount = feeHeadCounts[catId] || 0;
        let html = `
            <div class="fee-head row mb-2" data-id="${feeHeadCount}">
                <div class="col-md-4">
                    <label class="form-label">Fee Head *</label>
                    <select class="form-control" name="categories[${catId}][fee_heads][${feeHeadCount}][fee_head_id]" required>
                        <option value="">Select Fee Head</option>
                        @foreach($feeHeads as $fh)
                            <option value="{{ $fh->fee_head_id }}">{{ $fh->fee_head_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Discount Type *</label>
                    <select class="form-control" name="categories[${catId}][fee_heads][${feeHeadCount}][discount_type]" required>
                        <option value="Flat">Flat</option>
                        <option value="Percentage">Percentage</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Discount *</label>
                    <input type="number" step="0.01" class="form-control" name="categories[${catId}][fee_heads][${feeHeadCount}][discount]" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-fee-head mt-4">X</button>
                </div>
            </div>
        `;
        category.find('.fee-heads').append(html);
        feeHeadCounts[catId] = feeHeadCount + 1;
    });

    // Remove category
    $(document).on('click', '.remove-category', function() {
        $(this).closest('.category').remove();
    });

    // Remove fee head
    $(document).on('click', '.remove-fee-head', function() {
        $(this).closest('.fee-head').remove();
    });
});
</script>
@endsection