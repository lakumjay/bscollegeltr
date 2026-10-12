@extends('layouts.appadmin')

@section('content')
<div class="premium-page inventory-page">
    <section class="premium-page-header">
        <div>
            <span class="premium-eyebrow">Stock &amp; Inventory</span>
            <h1>Stock Management</h1>
            <p>Maintain inventory items, unit pricing, stock availability, bill &amp; party records, and track real-time stock valuation.</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <button type="button" class="premium-btn premium-btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                <i class="fa fa-plus"></i> Add Stock
            </button>
            <a href="{{ route('admin.inventory.export_csv', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="premium-btn premium-btn-gold">
                <i class="fa fa-download"></i> Export CSV
            </a>
        </div>
    </section>

    @if(session('success')) 
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    @endif
    @if($errors->any()) 
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    @endif

    {{-- Summary Statistics Cards --}}
    <section class="inventory-item-overview mb-4">
        <div>
            <span>Total Unique Items</span>
            <strong>{{ number_format($totalItemsCount) }}</strong>
        </div>
        <div>
            <span>Total Stock Units</span>
            <strong class="text-success">{{ number_format($totalStockQty) }} <small style="font-size:0.8rem;font-weight:normal;color:#6c757d;">Qty</small></strong>
        </div>
        <div>
            <span>Total Inventory Value</span>
            <strong class="text-primary">₹ {{ number_format($totalInventoryValue, 2) }}</strong>
        </div>
    </section>

    {{-- Datewise Filter Bar --}}
    <section class="premium-content-card mb-4" style="padding: 1.25rem 1.5rem;">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label for="filter_from_date" class="form-label mb-1" style="font-size: 0.85rem; font-weight: 700; color: #17355b;">
                    <i class="fa fa-calendar text-primary me-1"></i> From Date
                </label>
                <input type="text" class="form-control premium-datepicker" name="from_date" id="filter_from_date" value="{{ $fromDate }}" placeholder="DD-MM-YYYY" autocomplete="off">
            </div>
            <div class="col-md-4 col-sm-6">
                <label for="filter_to_date" class="form-label mb-1" style="font-size: 0.85rem; font-weight: 700; color: #17355b;">
                    <i class="fa fa-calendar text-primary me-1"></i> To Date
                </label>
                <input type="text" class="form-control premium-datepicker" name="to_date" id="filter_to_date" value="{{ $toDate }}" placeholder="DD-MM-YYYY" autocomplete="off">
            </div>
            <div class="col-md-4 col-sm-12 d-flex gap-2">
                <button type="submit" class="premium-btn premium-btn-primary flex-grow-1">
                    <i class="fa fa-filter"></i> Apply Filter
                </button>
                @if($fromDate || $toDate)
                    <a href="{{ route('admin.inventory.index') }}" class="premium-btn premium-btn-outline" title="Clear Filters">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </section>

    {{-- DataTable Section --}}
    <section class="premium-content-card inventory-table-card">
        <div class="premium-card-heading">
            <div>
                <span class="premium-eyebrow">Stock Catalog</span>
                <h2>Stock List <span class="premium-count-badge">{{ $inventoryItems->count() }}</span></h2>
            </div>
        </div>
        <div class="premium-table-wrap">
            <table class="premium-table js-premium-datatable" id="inventory_table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Item Details</th>
                        <th>Bill No. &amp; Date</th>
                        <th>Party / Supplier</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-center">Stock / Qty</th>
                        <th class="text-end">Total Amount</th>
                        <th>Payment Mode</th>
                        <th>Created By</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventoryItems as $index => $item)
                    @php
                        $stockQty = (int) $item->stocks->sum('quantity');
                        $totalAmount = (float) $item->unit_price * $stockQty;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong class="d-block text-dark">{{ $item->name }}</strong>
                            <small class="text-muted d-block" style="font-size:0.75rem;">SKU: {{ $item->sku }}</small>
                            @if($item->description)
                                <small class="text-muted d-block" style="font-size:0.72rem;max-width:220px;white-space:normal;">{{ Str::limit($item->description, 60) }}</small>
                            @endif
                        </td>
                        <td>
                            @if($item->bill_no)
                                <span class="badge bg-light text-dark border d-block mb-1" style="font-size:0.75rem;width:fit-content;"><i class="fa fa-file-text-o"></i> {{ $item->bill_no }}</span>
                            @else
                                <span class="text-muted d-block mb-1">—</span>
                            @endif
                            <small class="text-muted"><i class="fa fa-calendar-o"></i> {{ $item->purchase_date ? $item->purchase_date->format('d-m-Y') : '-' }}</small>
                        </td>
                        <td>
                            @if($item->party_name)
                                <strong class="d-block text-primary" style="font-size:0.8rem;">{{ $item->party_name }}</strong>
                            @endif
                            @if($item->supplier_name)
                                <span class="d-block" style="font-size:0.78rem;"><i class="fa fa-user-o"></i> {{ $item->supplier_name }}</span>
                            @endif
                            @if($item->supplier_address)
                                <small class="text-muted d-block" style="font-size:0.72rem;max-width:200px;white-space:normal;"><i class="fa fa-map-marker"></i> {{ $item->supplier_address }}</small>
                            @endif
                            @if(!$item->party_name && !$item->supplier_name && !$item->supplier_address)
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end finance-amount">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">
                            <span class="inventory-stock-badge">{{ $stockQty }}</span>
                        </td>
                        <td class="text-end">
                            <strong class="text-success" style="font-size: 0.95rem;">₹{{ number_format($totalAmount, 2) }}</strong>
                        </td>
                        <td>
                            @if($item->payment_method)
                                <span class="daybook-type-badge daybook-type-badge--{{ strtolower($item->payment_method) }}">{{ $item->payment_method }}</span>
                                @if($item->payment_method === 'Cheque' && $item->cheque_no)
                                    <small class="text-muted d-block mt-1" style="font-size:0.72rem;">Chq: {{ $item->cheque_no }}</small>
                                @elseif($item->payment_method === 'Online' && $item->transaction_id)
                                    <small class="text-muted d-block mt-1" style="font-size:0.72rem;">Txn: {{ $item->transaction_id }}</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td class="text-center">
                            <div class="inventory-actions justify-content-center">
                                <a href="{{ route('admin.inventory.edit', $item) }}" class="premium-btn premium-btn-outline premium-btn-sm" style="padding: 0.35rem 0.65rem;" title="Edit Item"><i class="fa fa-pencil"></i></a>
                                <form action="{{ route('admin.inventory.destroy', $item) }}" method="POST" class="d-inline delete-inventory-form" data-name="{{ $item->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="premium-btn premium-btn-danger premium-btn-sm js-delete-inventory-btn" style="padding: 0.35rem 0.65rem;" title="Delete Item"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted">No stock items found for the selected criteria.</td></tr>
                    @endforelse
                </tbody>
                @if($inventoryItems->count() > 0)
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1;">
                        <td colspan="4" class="text-end text-uppercase" style="font-size: 0.8rem; color: #475569;">Total:</td>
                        <td class="text-end">—</td>
                        <td class="text-center">
                            <span class="inventory-stock-badge" style="background:#e0e7ff;color:#3730a3;">{{ number_format($totalStockQty) }}</span>
                        </td>
                        <td class="text-end text-success" style="font-size: 1rem;">
                            ₹{{ number_format($totalInventoryValue, 2) }}
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </section>
</div>

{{-- Add Stock Bootstrap Modal --}}
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #17355b 0%, #1a56db 100%); color: #fff; padding: 1.25rem 1.75rem;">
                <div>
                    <span class="d-block text-uppercase" style="font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em; opacity: 0.85;">Stock Entry</span>
                    <h5 class="modal-title mb-0" id="addStockModalLabel" style="font-weight: 700; color: #fff;">Add New Stock Item</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.store') }}" id="addStockForm">
                @csrf
                <div class="modal-body" style="padding: 1.5rem 1.75rem; max-height: calc(85vh - 120px); overflow-y: auto;">
                    
                    {{-- Section 1: Item & Price Details --}}
                    <div class="inventory-section-title mb-3"><span>1</span> Item Details &amp; Pricing</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7 col-sm-12">
                            <label for="modal_item_name" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="modal_item_name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Printer / Desk / Paper A4" required>
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <label for="modal_sku" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">SKU / Item Code <small class="text-muted" style="font-weight: normal;">(Auto if empty)</small></label>
                            <input type="text" name="sku" id="modal_sku" class="form-control" value="{{ old('sku') }}" placeholder="e.g. PRN-001">
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="modal_unit_price" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Unit Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="unit_price" id="modal_unit_price" class="form-control" min="0" step="0.01" value="{{ old('unit_price') }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label for="modal_total_stock" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Total Stock (Qty) <span class="text-danger">*</span></label>
                            <input type="number" name="total_stock" id="modal_total_stock" class="form-control" min="0" step="1" value="{{ old('total_stock', 1) }}" placeholder="1" required>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Total Amount</label>
                            <div class="p-2 border rounded bg-light text-center" style="border-color: #cbd5e1 !important; min-height: 38px; display: flex; align-items: center; justify-content: center;">
                                <strong id="modal_total_amount_display" class="text-success" style="font-size: 1.05rem;">₹ 0.00</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Bill & Purchase Details --}}
                    <div class="inventory-section-title mb-3"><span>2</span> Bill &amp; Purchase Information</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-sm-12">
                            <label for="modal_bill_no" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Bill / Invoice No.</label>
                            <input type="text" name="bill_no" id="modal_bill_no" class="form-control" value="{{ old('bill_no') }}" placeholder="e.g. INV-2026-001">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label for="modal_purchase_date" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control premium-datepicker" name="purchase_date" id="modal_purchase_date" value="{{ old('purchase_date', now()->format('d-m-Y')) }}" autocomplete="off" required>
                        </div>
                    </div>

                    {{-- Section 3: Party & Supplier Information --}}
                    <div class="inventory-section-title mb-3"><span>3</span> Party &amp; Supplier Details</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 col-sm-12">
                            <label for="modal_party_name" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Party Name</label>
                            <input type="text" name="party_name" id="modal_party_name" class="form-control" value="{{ old('party_name') }}" placeholder="e.g. ABC Enterprises">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label for="modal_supplier_name" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Supplier / Vendor Name</label>
                            <input type="text" name="supplier_name" id="modal_supplier_name" class="form-control" value="{{ old('supplier_name') }}" placeholder="e.g. Shri Krishna Stationery">
                        </div>
                        <div class="col-12">
                            <label for="modal_supplier_address" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Supplier / Vendor Address</label>
                            <input type="text" name="supplier_address" id="modal_supplier_address" class="form-control" value="{{ old('supplier_address') }}" placeholder="Supplier street address, city, contact info">
                        </div>
                    </div>

                    {{-- Section 4: Payment Details --}}
                    <div class="inventory-section-title mb-3"><span>4</span> Payment Details</div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4 col-sm-12">
                            <label for="modal_payment_method" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Payment Method</label>
                            <select name="payment_method" id="modal_payment_method" class="form-select">
                                <option value="">Select payment method</option>
                                <option value="Cash" @selected(old('payment_method') === 'Cash')>Cash</option>
                                <option value="Cheque" @selected(old('payment_method') === 'Cheque')>Cheque (Check)</option>
                                <option value="Online" @selected(old('payment_method') === 'Online')>Online (UPI / NEFT / Bank)</option>
                            </select>
                        </div>
                        <div class="col-md-8 col-sm-12" id="modal_cheque_no_container" style="display: {{ old('payment_method') === 'Cheque' ? 'block' : 'none' }};">
                            <label for="modal_cheque_no" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Cheque Number</label>
                            <input type="text" name="cheque_no" id="modal_cheque_no" class="form-control" value="{{ old('cheque_no') }}" placeholder="e.g. CHQ-987654">
                        </div>
                        <div class="col-md-8 col-sm-12" id="modal_transaction_id_container" style="display: {{ old('payment_method') === 'Online' ? 'block' : 'none' }};">
                            <label for="modal_transaction_id" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Online Transaction ID / Reference</label>
                            <input type="text" name="transaction_id" id="modal_transaction_id" class="form-control" value="{{ old('transaction_id') }}" placeholder="e.g. UPI / TXN12345678">
                        </div>
                    </div>

                    {{-- Section 5: Description --}}
                    <div class="inventory-section-title mb-3"><span>5</span> Remarks &amp; Notes</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="modal_description" class="form-label" style="font-size: 0.82rem; font-weight: 700; color: #17355b;">Description / Notes</label>
                            <textarea name="description" id="modal_description" class="form-control" rows="2" placeholder="Optional notes or details about this item">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1rem 1.75rem;">
                    <button type="button" class="premium-btn premium-btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="premium-btn premium-btn-primary">
                        <i class="fa fa-plus"></i> Save Stock Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Live Total Calculation
    const unitPriceInput = document.getElementById('modal_unit_price');
    const totalStockInput = document.getElementById('modal_total_stock');
    const totalAmountDisplay = document.getElementById('modal_total_amount_display');

    function calculateTotal() {
        const unitPrice = parseFloat(unitPriceInput ? unitPriceInput.value : 0) || 0;
        const totalStock = parseFloat(totalStockInput ? totalStockInput.value : 0) || 0;
        const total = unitPrice * totalStock;
        
        if (totalAmountDisplay) {
            totalAmountDisplay.innerText = '₹ ' + total.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    if (unitPriceInput) {
        unitPriceInput.addEventListener('input', calculateTotal);
        unitPriceInput.addEventListener('change', calculateTotal);
    }
    if (totalStockInput) {
        totalStockInput.addEventListener('input', calculateTotal);
        totalStockInput.addEventListener('change', calculateTotal);
    }
    calculateTotal();

    // 2. Payment Method Switcher in Modal
    const methodSelect = document.getElementById('modal_payment_method');
    const chequeContainer = document.getElementById('modal_cheque_no_container');
    const txnContainer = document.getElementById('modal_transaction_id_container');

    function togglePaymentFields() {
        if (!methodSelect) return;
        const val = methodSelect.value;
        if (chequeContainer) {
            chequeContainer.style.display = (val === 'Cheque') ? 'block' : 'none';
        }
        if (txnContainer) {
            txnContainer.style.display = (val === 'Online') ? 'block' : 'none';
        }
    }

    if (methodSelect) {
        methodSelect.addEventListener('change', togglePaymentFields);
        methodSelect.addEventListener('input', togglePaymentFields);
        togglePaymentFields();
    }

    const modalElement = document.getElementById('addStockModal');
    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function () {
            togglePaymentFields();
            calculateTotal();
        });
    }

    // 3. SweetAlert2 Confirmation on Delete
    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.js-delete-inventory-btn');
        if (!deleteBtn) return;
        e.preventDefault();

        const form = deleteBtn.closest('form');
        if (!form) return;

        const itemName = form.dataset.name || 'this item';

        if (typeof Swal === 'undefined') {
            if (window.confirm(`Are you sure you want to delete "${itemName}"? This action cannot be undone.`)) {
                form.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Delete Stock Item?',
            text: `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#e02424',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // 4. Re-open Modal if There are Validation Errors
    @if($errors->any())
        var addStockModalEl = document.getElementById('addStockModal');
        if (addStockModalEl) {
            var modal = new bootstrap.Modal(addStockModalEl);
            modal.show();
        }
    @endif
});
</script>
@endsection
