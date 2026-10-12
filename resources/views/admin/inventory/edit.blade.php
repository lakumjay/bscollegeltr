@extends('layouts.appadmin')

@section('content')
<div class="premium-page inventory-page">
    <section class="premium-page-header">
        <div><span class="premium-eyebrow">Inventory management</span><h1>Edit inventory item</h1><p>Update item pricing, bill, party, supplier, and payment details.</p></div>
        <a href="{{ route('admin.inventory.index') }}" class="premium-btn premium-btn-outline"><i class="fa fa-arrow-left"></i> Back to inventory</a>
    </section>

    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="inventory-item-overview">
        <div><span>Item SKU</span><strong>{{ $inventoryItem->sku }}</strong></div>
        <div><span>Current unit price</span><strong>₹{{ number_format($inventoryItem->unit_price, 2) }}</strong></div>
        <div><span>Available stock</span><strong>{{ $inventoryItem->stocks->sum('quantity') }}</strong></div>
    </section>

    <section class="premium-card inventory-edit-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Item details</span><h2>{{ $inventoryItem->name }}</h2></div><span class="inventory-stock-badge"><i class="fa fa-cubes"></i> {{ $inventoryItem->stocks->sum('quantity') }} in stock</span></div>
        <form method="POST" action="{{ route('admin.inventory.update', $inventoryItem) }}" class="inventory-form-grid">
            @csrf @method('PUT')
            <input type="hidden" name="sku" value="{{ $inventoryItem->sku }}">

            {{-- Section 1: Item & Billing details --}}
            <div class="inventory-section-title"><span>1</span> Item &amp; Billing Details</div>
            <div class="inventory-fields-row">
                <div class="finance-field">
                    <label for="item_name">Item name <span>*</span></label>
                    <input type="text" name="name" id="item_name" value="{{ old('name', $inventoryItem->name) }}" required>
                </div>
                <div class="finance-field">
                    <label for="unit_price">Unit price (₹) <span>*</span></label>
                    <input type="number" name="unit_price" id="unit_price" min="0" step="0.01" value="{{ old('unit_price', $inventoryItem->unit_price) }}" required>
                </div>
                <div class="finance-field">
                    <label for="total_stock">Total stock <span>*</span></label>
                    <input type="number" name="total_stock" id="total_stock" min="0" value="{{ old('total_stock', $inventoryItem->stocks->sum('quantity')) }}" required>
                </div>
                <div class="finance-field">
                    <label for="bill_no">Bill / Invoice No.</label>
                    <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no', $inventoryItem->bill_no) }}" placeholder="e.g. BILL-2026-001">
                </div>
                <div class="finance-field">
                    <label for="purchase_date">Date <span>*</span></label>
                    <input type="text" class="premium-datepicker" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', optional($inventoryItem->purchase_date)->format('d-m-Y') ?? now()->format('d-m-Y')) }}" autocomplete="off" required>
                </div>
            </div>

            {{-- Section 2: Party & Supplier details --}}
            <div class="inventory-section-title"><span>2</span> Party &amp; Supplier Information</div>
            <div class="inventory-fields-row">
                <div class="finance-field">
                    <label for="party_name">Party name</label>
                    <input type="text" name="party_name" id="party_name" value="{{ old('party_name', $inventoryItem->party_name) }}" placeholder="e.g. ABC Enterprises">
                </div>
                <div class="finance-field">
                    <label for="supplier_name">Supplier / Vendor name</label>
                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name', $inventoryItem->supplier_name) }}" placeholder="e.g. Shri Krishna Stationery">
                </div>
                <div class="finance-field" style="grid-column: span 2;">
                    <label for="supplier_address">Supplier / Vendor address</label>
                    <input type="text" name="supplier_address" id="supplier_address" value="{{ old('supplier_address', $inventoryItem->supplier_address) }}" placeholder="Supplier street address, city, etc.">
                </div>
            </div>

            {{-- Section 3: Payment details --}}
            <div class="inventory-section-title"><span>3</span> Payment Details</div>
            <div class="inventory-fields-row">
                <div class="finance-field">
                    <label for="payment_method">Payment method</label>
                    <select name="payment_method" id="payment_method" class="form-select">
                        <option value="">Select payment method</option>
                        <option value="Cash" @selected(old('payment_method', $inventoryItem->payment_method) === 'Cash')>Cash</option>
                        <option value="Cheque" @selected(old('payment_method', $inventoryItem->payment_method) === 'Cheque')>Cheque (Check)</option>
                        <option value="Online" @selected(old('payment_method', $inventoryItem->payment_method) === 'Online')>Online</option>
                    </select>
                </div>
                <div class="finance-field" id="cheque_no_container" style="display: {{ old('payment_method', $inventoryItem->payment_method) === 'Cheque' ? 'block' : 'none' }};">
                    <label for="cheque_no">Cheque number</label>
                    <input type="text" name="cheque_no" id="cheque_no" value="{{ old('cheque_no', $inventoryItem->cheque_no) }}" placeholder="e.g. CHQ-987654">
                </div>
                <div class="finance-field" id="transaction_id_container" style="display: {{ old('payment_method', $inventoryItem->payment_method) === 'Online' ? 'block' : 'none' }};">
                    <label for="transaction_id">Online transaction ID</label>
                    <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id', $inventoryItem->transaction_id) }}" placeholder="e.g. UPI / TXN12345678">
                </div>
            </div>

            {{-- Section 4: Description --}}
            <div class="finance-field inventory-description-field">
                <label for="description">Description / Remarks</label>
                <textarea name="description" id="description" rows="3" placeholder="Add a useful description for this inventory item">{{ old('description', $inventoryItem->description) }}</textarea>
            </div>

            <div class="inventory-form-actions">
                <button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-save"></i> Update item</button>
                <a href="{{ route('admin.inventory.index') }}" class="premium-btn premium-btn-outline">Cancel</a>
            </div>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const methodSelect = document.getElementById('payment_method');
    const chequeContainer = document.getElementById('cheque_no_container');
    const txnContainer = document.getElementById('transaction_id_container');

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
        togglePaymentFields();
    }
});
</script>
@endsection
