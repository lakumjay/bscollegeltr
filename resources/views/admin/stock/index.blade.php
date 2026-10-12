@extends('layouts.appadmin')

@section('content')
<div class="premium-page stock-page">
    <section class="premium-page-header"><div><span class="premium-eyebrow">Inventory management</span><h1>Stock ledger</h1><p>Add inventory quantities and review every stock update.</p></div></section>

    <section class="premium-content-card stock-create-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Stock update</span><h2>Add stock for an item</h2></div><i class="fa fa-cubes"></i></div>
        <form method="post" action="{{ route('admin.stock.store') }}" class="stock-create-form">
            @csrf
            <div class="finance-field"><label for="inventory_item_id">Item <span>*</span></label><select name="inventory_item_id" id="inventory_item_id" required><option value="">Select item</option>@foreach($inventoryItems as $item)<option value="{{ $item->id }}" @selected(old('inventory_item_id') == $item->id)>{{ $item->name }} (SKU: {{ $item->sku }})</option>@endforeach</select>@error('inventory_item_id')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="finance-field"><label for="quantity">Quantity <span>*</span></label><input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" required>@error('quantity')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <div class="finance-field"><label for="location">Location</label><input type="text" name="location" id="location" value="{{ old('location') }}">@error('location')<small class="text-danger">{{ $message }}</small>@enderror</div>
            <button type="submit" class="premium-btn premium-btn-primary"><i class="fa fa-plus"></i> Add stock</button>
        </form>
    </section>

    <section class="premium-content-card stock-table-card">
        <div class="premium-card-heading"><div><span class="premium-eyebrow">Stock history</span><h2>Stock details <span class="premium-count-badge">{{ $stocks->count() }}</span></h2></div></div>
        <div class="premium-table-wrap"><table class="premium-table js-premium-datatable" id="stock_table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Location</th>
                                <th>Updated By</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $stock)
                            <tr>
                                <td><strong>{{ $stock->inventoryItem?->name ?? '-' }}</strong></td>
                                <td>{{ $stock->inventoryItem?->sku ?? 'N/A' }}</td>
                                <td><span class="inventory-stock-badge">{{ $stock->quantity }}</span></td>
                                <td>{{ $stock->location ?? 'N/A' }}</td>
                                <td>{{ $stock->user?->name ?? '-' }}</td>
                                <td>{{ $stock->updated_at->format('d-m-Y H:i') }}</td>
                            </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">No stock records found.</td></tr>
                            @endforelse
                        </tbody>
        </table></div>
    </section>
</div>
@endsection
