@extends('layouts.appadmin')

@section('content')
{{--
    Variables from controller:
    $student, $course, $sem, $feeRecords, $payments,
    $specialCategories, $feeStructures,
    $total_fees, $paid, $paymentMethods
--}}

<div class="premium-page premium-page--compact fee-view-page">
<div class="row justify-content-center">
<div class="col-lg-11">

    {{-- ── Alerts ──────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Student Header Card ──────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fa fa-money-bill-wave me-2"></i>
                Fee Details — {{ $student->first_name }} {{ $student->last_name }}
            </h4>
            <div>
                <span class="badge bg-light text-dark fs-6 me-1">{{ $course->name }}</span>
                <span class="badge bg-warning text-dark fs-6">Sem {{ $sem }}</span>
            </div>
        </div>
        <div class="card-body bg-light">
            <div class="row align-items-center">
                @php
                    $photoSrc = asset('images/default-avatar.svg');
                    if (!empty($student->photo)) {
                        if (str_starts_with($student->photo, 'http')) {
                            $photoSrc = $student->photo;
                        } elseif (file_exists(public_path($student->photo))) {
                            $photoSrc = url($student->photo);
                        } elseif (file_exists(public_path('uploads/' . basename($student->photo)))) {
                            $photoSrc = url('uploads/' . basename($student->photo));
                        }
                    }
                @endphp
                <div class="col-md-2 text-center">
                    <img src="{{ $photoSrc }}"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';"
                         alt="Student" style="max-width:110px;max-height:140px;border-radius:8px;object-fit:cover;background:#f1f5f9;border:1px solid #e2e8f0;">
                    <div class="fw-bold mt-1">{{ $student->first_name }} {{ $student->last_name }}</div>
                </div>
                <div class="col-md-5">
                    <table class="table table-sm table-borderless mb-0" style="font-size:0.9rem;">
                        <tr><td class="fw-semibold" style="width:45%">Roll No</td><td>{{ $student->roll_no }}</td></tr>
                        <tr><td class="fw-semibold">Student ID</td><td>{{ $student->student_id }}</td></tr>
                        <tr><td class="fw-semibold">Father Name</td><td>{{ $student->father_name }}</td></tr>
                        <tr><td class="fw-semibold">Gender</td><td>{{ $student->gender }}</td></tr>
                        <tr><td class="fw-semibold">Date of Birth</td><td>{{ $student->date_of_birth->format('d-m-Y') }}</td></tr>
                    </table>
                </div>
                <div class="col-md-5">
                    <table class="table table-sm table-borderless mb-0" style="font-size:0.9rem;">
                        <tr><td class="fw-semibold" style="width:45%">Mobile</td><td>{{ $student->phone_number }}</td></tr>
                        <tr><td class="fw-semibold">Email</td><td>{{ $student->email }}</td></tr>
                        <tr><td class="fw-semibold">Course</td><td>{{ $course->name }}</td></tr>
                        <tr><td class="fw-semibold">Batch</td><td>{{ $student->batch }}</td></tr>
                        <tr><td class="fw-semibold">Semester</td>
                            <td><span class="badge bg-warning text-dark">{{ $sem }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Fee Summary Cards ────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-warning text-dark rounded shadow-sm h-100">
                <div class="small fw-semibold">Semester</div>
                <div class="fs-3 fw-bold">{{ $sem }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-primary text-white rounded shadow-sm h-100">
                <div class="small fw-semibold">Total Fees</div>
                <div class="fs-3 fw-bold">₹{{ number_format($total_fees, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-success text-white rounded shadow-sm h-100">
                <div class="small fw-semibold">Paid</div>
                <div class="fs-3 fw-bold">₹{{ number_format($paid, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 {{ $balance > 0 ? 'bg-danger' : 'bg-secondary' }} text-white rounded shadow-sm h-100">
                <div class="small fw-semibold">Due Amount</div>
                <div class="fs-3 fw-bold">₹{{ number_format($balance, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- ── Payments History ─────────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-credit-card me-2"></i>Payment History</h5>
            <a href="{{ route('fee_management.receipt', ['student_id' => $student->id, 'course_id' => $course->id]) }}"
               class="btn btn-sm btn-light">
                <i class="fa fa-print me-1"></i>Print All
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Fee Head</th>
                            <th>Amount</th>
                            <th>Student Category</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Txn ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->notes ?? '-' }}</td>
                            <td class="fw-semibold">₹{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $payment->student_type ?? 'Regular College' }}
                                    @if(($payment->student_type ?? '') === 'Other College' && $payment->other_college_name)
                                        <small class="text-primary d-block font-monospace">({{ $payment->other_college_name }})</small>
                                    @endif
                                </span>
                            </td>
                            <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                            <td>
                                <span class="badge
                                    @if($payment->payment_method === 'Cash') bg-success
                                    @elseif($payment->payment_method === 'UPI') bg-info text-dark
                                    @elseif($payment->payment_method === 'Cheque') bg-warning text-dark
                                    @else bg-secondary @endif">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $payment->transaction_id ?? '-' }}</td>
                            <td>
                                <a href="{{ route('fee_management.receipt_single', [
                                       'student_id' => $student->id,
                                       'course_id'  => $course->id,
                                       'receipt_id' => $payment->id
                                   ]) }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-print"></i> Print
                                </a>
                                <a href="{{ route('fee_management.delete_receipt', [
                                       'student_id' => $student->id,
                                       'course_id'  => $course->id,
                                       'receipt_id' => $payment->id
                                   ]) }}"
                                   class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No payments recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Record Payment Form ──────────────────────────────────────────── --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-money-check-alt me-2"></i>Record Payment</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('fee_management.pay_multi') }}" id="payMultiForm">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <input type="hidden" name="course_id"  value="{{ $course->id }}">
                {{-- Hidden inputs injected by JS before submit --}}
                <div id="feeHeadsHidden"></div>

                <div class="row g-3">
                    {{-- Semester --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                        <select class="form-select" id="semesterSelect" name="semester" required>
                            @for($s = 1; $s <= 8; $s++)
                                <option value="{{ $s }}" {{ $sem == $s ? 'selected' : '' }}>Sem {{ $s }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Payment Date --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control premium-datepicker" name="payment_date"
                               value="{{ old('payment_date', now()->format('d-m-Y')) }}" autocomplete="off" required>
                    </div>

                    {{-- Payment Method --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_method" id="paymentMethodSelect" required>
                            <option value="" disabled selected>Select Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Transaction ID --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Transaction ID <span class="text-muted">(optional)</span></label>
                        <input type="text" class="form-control" name="transaction_id" placeholder="UPI/Cheque/Ref no.">
                    </div>
                </div>

                {{-- Fee Heads (loaded via AJAX) --}}
                <div class="mt-4">
                    <label class="form-label fw-semibold">
                        Select Fee Heads <span class="text-danger">*</span>
                        <small class="text-muted ms-2">(tick the ones being paid — amounts are editable)</small>
                    </label>
                    <div id="feeHeadsContainer" class="border rounded p-3 bg-light min-vh-5">
                        <p class="text-muted mb-0" id="feeHeadsMsg">
                            <i class="fa fa-spinner fa-spin me-1"></i>Loading fee heads…
                        </p>
                    </div>
                </div>

                <div class="row g-3 align-items-center mt-2 p-3 bg-light rounded border">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold mb-1" for="studentTypeSelect">Student Category / Quota <span class="text-danger">*</span></label>
                        <select class="form-select" id="studentTypeSelect" name="student_type">
                            <option value="Regular College" selected>Regular College</option>
                            <option value="Ward">Ward</option>
                            <option value="Other College">Other College</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="otherCollegeNameContainer" style="display: none;">
                        <label class="form-label fw-semibold mb-1" for="otherCollegeName">Other College / School Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="otherCollegeName" name="other_college_name" placeholder="e.g. BCD College">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" id="discountEligible" name="discount_eligible" value="1">
                            <label class="form-check-label fw-semibold" for="discountEligible">Apply Discount</label>
                        </div>
                        <small class="text-muted d-block" style="font-size:0.8rem;">Default 10% or enter custom amount.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold mb-1" for="discountAmount">Discount Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control" id="discountAmount" name="discount_amount" min="0" step="0.01" placeholder="0.00" disabled>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold" for="paymentNotes">Payment notes <span class="text-muted">(optional)</span></label>
                    <textarea class="form-control" id="paymentNotes" name="notes" rows="2" maxlength="1000" placeholder="Add an optional payment note"></textarea>
                </div>

                {{-- Auto Total --}}
                <div class="mt-3 p-3 bg-success text-white rounded d-flex justify-content-between align-items-center">
                    <span class="fw-semibold fs-5">Total Amount:</span>
                    <span class="fw-bold fs-4" id="totalAmount">₹0.00</span>
                </div>

                <button type="submit" class="btn btn-primary mt-4 px-4 shadow-sm" id="submitPayBtn" disabled>
                    <i class="fa fa-save me-2"></i>Record Payment &amp; Print Receipt
                </button>
            </form>
        </div>
    </div>

</div>
</div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    var FEE_URL  = '/admin/fee/management/fee-heads';
    var COURSE_ID = "{{ $course->id }}";

    // ── Student Type change ───────────────────────────────────────────────
    $('#studentTypeSelect').on('change', function () {
        var type = $(this).val();
        if (type === 'Other College') {
            $('#otherCollegeNameContainer').show();
            $('#otherCollegeName').prop('required', true);
        } else {
            $('#otherCollegeNameContainer').hide();
            $('#otherCollegeName').prop('required', false).val('');
        }
        if (type === 'Ward' || type === 'Other College') {
            if (!$('#discountEligible').is(':checked')) {
                $('#discountEligible').prop('checked', true).trigger('change');
            }
        }
    });

    // ── Load fee heads via AJAX ────────────────────────────────────────────
    function loadFeeHeads(sem) {
        $('#feeHeadsContainer').html('<p class="text-muted mb-0"><i class="fa fa-spinner fa-spin me-1"></i>Loading…</p>');
        $('#totalAmount').text('₹0.00');
        $('#submitPayBtn').prop('disabled', true);
        $('#feeHeadsHidden').empty();

        $.getJSON(FEE_URL, { course_id: COURSE_ID, semester: sem })
            .done(function (data) {
                if (!data || data.length === 0) {
                    $('#feeHeadsContainer').html('<p class="text-warning mb-0">No fee heads found for this semester.</p>');
                    return;
                }
                var html = '<div class="row">';
                $.each(data, function (i, fh) {
                    var amt     = parseFloat(fh.amount) || 0;
                    var checked = amt > 0 ? 'checked' : '';
                    html += '<div class="col-md-4 mb-2">';
                    html += '<div class="form-check d-flex align-items-center gap-2">';
                    html += '<input class="form-check-input fee-checkbox" type="checkbox" ' + checked + ' ';
                    html += 'data-name="' + fh.name.replace(/"/g, '&quot;') + '" id="fh_' + fh.id + '">';
                    html += '<label class="form-check-label fw-semibold" for="fh_' + fh.id + '" style="flex:1">' + fh.name + '</label>';
                    html += '<input type="number" step="0.01" min="0" class="form-control form-control-sm fee-amt-input" ';
                    html += 'style="width:95px;" data-id="' + fh.id + '" value="' + amt.toFixed(2) + '">';
                    html += '</div></div>';
                });
                html += '</div>';
                $('#feeHeadsContainer').html(html);
                recalcTotal();
            })
            .fail(function () {
                $('#feeHeadsContainer').html('<p class="text-danger mb-0">Failed to load. Please try again.</p>');
            });
    }

    // ── Recalculate the total and optional concession ──────────────────────
    function recalcTotal() {
        var gross = 0;
        $('.fee-checkbox:checked').each(function () {
            var id  = $(this).attr('id').replace('fh_', '');
            var amt = parseFloat($('.fee-amt-input[data-id="' + id + '"]').val()) || 0;
            gross  += amt;
        });

        var eligible = $('#discountEligible').is(':checked');
        var $discountAmount = $('#discountAmount');
        $discountAmount.prop('disabled', !eligible);

        var discount = 0;
        if (eligible) {
            var enteredAmount = $.trim($discountAmount.val());
            if (enteredAmount === '') {
                discount = gross * 0.10;
                $discountAmount.attr('placeholder', discount.toFixed(2));
            } else {
                discount = parseFloat(enteredAmount) || 0;
            }
            discount = Math.max(0, Math.min(discount, gross));
        } else {
            $discountAmount.val('');
            $discountAmount.attr('placeholder', '0.00');
        }

        $('#totalAmount').text('₹' + Math.max(0, gross - discount).toFixed(2));
        $('#submitPayBtn').prop('disabled', gross <= 0);
    }

    // ── Events ────────────────────────────────────────────────────────────
    $('#semesterSelect').on('change', function () { loadFeeHeads($(this).val()); });

    $(document).on('change', '.fee-checkbox', recalcTotal);

    $(document).on('input', '.fee-amt-input', function () {
        var id = $(this).data('id');
        if (parseFloat($(this).val()) > 0) $('#fh_' + id).prop('checked', true);
        recalcTotal();
    });

    $('#discountEligible').on('change', recalcTotal);
    $('#discountAmount').on('input', recalcTotal);

    // ── Build hidden inputs on submit ─────────────────────────────────────
    $('#payMultiForm').on('submit', function (e) {
        $('#feeHeadsHidden').empty();
        var idx   = 0;
        var valid = false;

        $('.fee-checkbox:checked').each(function () {
            var name   = $(this).data('name');
            var id     = $(this).attr('id').replace('fh_', '');
            var amount = parseFloat($('.fee-amt-input[data-id="' + id + '"]').val()) || 0;
            if (amount > 0) {
                $('<input>').attr({ type: 'hidden', name: 'fee_heads[' + idx + '][name]',   value: name   }).appendTo('#feeHeadsHidden');
                $('<input>').attr({ type: 'hidden', name: 'fee_heads[' + idx + '][amount]', value: amount }).appendTo('#feeHeadsHidden');
                idx++;
                valid = true;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Please select at least one fee head with amount > 0.');
        }
    });

    // ── Init ──────────────────────────────────────────────────────────────
    loadFeeHeads($('#semesterSelect').val());
});
</script>
@endsection
