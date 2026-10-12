<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with(['stocks', 'user']);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate) {
            try {
                $parsedFrom = Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay();
            } catch (\Exception $e) {
                $parsedFrom = Carbon::parse($fromDate)->startOfDay();
            }
            $query->where(function ($q) use ($parsedFrom) {
                $q->whereDate('purchase_date', '>=', $parsedFrom->toDateString())
                  ->orWhere(function ($sq) use ($parsedFrom) {
                      $sq->whereNull('purchase_date')->whereDate('created_at', '>=', $parsedFrom->toDateString());
                  });
            });
        }

        if ($toDate) {
            try {
                $parsedTo = Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay();
            } catch (\Exception $e) {
                $parsedTo = Carbon::parse($toDate)->endOfDay();
            }
            $query->where(function ($q) use ($parsedTo) {
                $q->whereDate('purchase_date', '<=', $parsedTo->toDateString())
                  ->orWhere(function ($sq) use ($parsedTo) {
                      $sq->whereNull('purchase_date')->whereDate('created_at', '<=', $parsedTo->toDateString());
                  });
            });
        }

        $inventoryItems = $query->latest()->get();

        $totalItemsCount = $inventoryItems->count();
        $totalStockQty = $inventoryItems->sum(function ($item) {
            return (int) $item->stocks->sum('quantity');
        });
        $totalInventoryValue = $inventoryItems->sum(function ($item) {
            return (float) $item->unit_price * (int) $item->stocks->sum('quantity');
        });

        return view('admin.inventory.index', compact('inventoryItems', 'fromDate', 'toDate', 'totalItemsCount', 'totalStockQty', 'totalInventoryValue'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:inventory_items,sku',
            'unit_price' => 'required|numeric|min:0',
            'total_stock' => 'nullable|integer|min:0',
            'bill_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|string',
            'party_name' => 'nullable|string|max:255',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_address' => 'nullable|string',
            'payment_method' => 'nullable|string|in:Cash,Cheque,Online',
            'cheque_no' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $sku = $request->filled('sku') ? $request->sku : 'INV-' . strtoupper(Str::random(8));
        while (InventoryItem::where('sku', $sku)->exists()) {
            $sku = 'INV-' . strtoupper(Str::random(8));
        }

        $purchaseDate = null;
        if ($request->filled('purchase_date')) {
            try {
                $purchaseDate = Carbon::createFromFormat('d-m-Y', $request->purchase_date)->toDateString();
            } catch (\Exception $e) {
                $purchaseDate = Carbon::parse($request->purchase_date)->toDateString();
            }
        } else {
            $purchaseDate = now()->toDateString();
        }

        $item = InventoryItem::create([
            'name' => $request->name,
            'sku' => $sku,
            'bill_no' => $request->bill_no,
            'purchase_date' => $purchaseDate,
            'party_name' => $request->party_name,
            'supplier_name' => $request->supplier_name,
            'supplier_address' => $request->supplier_address,
            'payment_method' => $request->payment_method,
            'cheque_no' => $request->payment_method === 'Cheque' ? $request->cheque_no : null,
            'transaction_id' => $request->payment_method === 'Online' ? $request->transaction_id : null,
            'unit_price' => $request->unit_price,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        if ($request->filled('total_stock') && (int) $request->total_stock > 0) {
            Stock::create([
                'inventory_item_id' => $item->id,
                'quantity' => (int) $request->total_stock,
                'location' => 'Initial Stock',
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Inventory item added successfully.');
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $inventoryItem->load('stocks');
        return view('admin.inventory.edit', compact('inventoryItem'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory_items,sku,' . $inventoryItem->id,
            'unit_price' => 'required|numeric|min:0',
            'total_stock' => 'nullable|integer|min:0',
            'bill_no' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|string',
            'party_name' => 'nullable|string|max:255',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_address' => 'nullable|string',
            'payment_method' => 'nullable|string|in:Cash,Cheque,Online',
            'cheque_no' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $purchaseDate = $inventoryItem->purchase_date;
        if ($request->filled('purchase_date')) {
            try {
                $purchaseDate = Carbon::createFromFormat('d-m-Y', $request->purchase_date)->toDateString();
            } catch (\Exception $e) {
                $purchaseDate = Carbon::parse($request->purchase_date)->toDateString();
            }
        }

        $inventoryItem->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'bill_no' => $request->bill_no,
            'purchase_date' => $purchaseDate,
            'party_name' => $request->party_name,
            'supplier_name' => $request->supplier_name,
            'supplier_address' => $request->supplier_address,
            'payment_method' => $request->payment_method,
            'cheque_no' => $request->payment_method === 'Cheque' ? $request->cheque_no : null,
            'transaction_id' => $request->payment_method === 'Online' ? $request->transaction_id : null,
            'unit_price' => $request->unit_price,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        if ($request->has('total_stock')) {
            $newStock = (int) $request->input('total_stock');
            $currentStock = (int) $inventoryItem->stocks()->sum('quantity');

            if ($newStock !== $currentStock) {
                if ($inventoryItem->stocks()->count() === 1) {
                    $inventoryItem->stocks()->first()->update([
                        'quantity' => $newStock,
                        'user_id' => Auth::id(),
                    ]);
                } elseif ($inventoryItem->stocks()->count() === 0) {
                    Stock::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'quantity' => $newStock,
                        'location' => 'Default',
                        'user_id' => Auth::id(),
                    ]);
                } else {
                    $diff = $newStock - $currentStock;
                    Stock::create([
                        'inventory_item_id' => $inventoryItem->id,
                        'quantity' => $diff,
                        'location' => 'Stock Adjustment',
                        'user_id' => Auth::id(),
                    ]);
                }
            }
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return redirect()->back()->with('success', 'Inventory item deleted successfully.');
    }

    public function exportCsv(Request $request)
    {
        $query = InventoryItem::with(['stocks', 'user']);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate) {
            try {
                $parsedFrom = Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay();
            } catch (\Exception $e) {
                $parsedFrom = Carbon::parse($fromDate)->startOfDay();
            }
            $query->where(function ($q) use ($parsedFrom) {
                $q->whereDate('purchase_date', '>=', $parsedFrom->toDateString())
                  ->orWhere(function ($sq) use ($parsedFrom) {
                      $sq->whereNull('purchase_date')->whereDate('created_at', '>=', $parsedFrom->toDateString());
                  });
            });
        }

        if ($toDate) {
            try {
                $parsedTo = Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay();
            } catch (\Exception $e) {
                $parsedTo = Carbon::parse($toDate)->endOfDay();
            }
            $query->where(function ($q) use ($parsedTo) {
                $q->whereDate('purchase_date', '<=', $parsedTo->toDateString())
                  ->orWhere(function ($sq) use ($parsedTo) {
                      $sq->whereNull('purchase_date')->whereDate('created_at', '<=', $parsedTo->toDateString());
                  });
            });
        }

        $items = $query->latest()->get();
        $filename = 'inventory_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Item Name',
                'SKU',
                'Bill No',
                'Date',
                'Party Name',
                'Supplier Name',
                'Supplier Address',
                'Unit Price (INR)',
                'Total Stock',
                'Total Amount (INR)',
                'Payment Method',
                'Cheque No',
                'Transaction ID',
                'Description',
                'Created By',
            ]);

            foreach ($items as $item) {
                $stockQty = (int) $item->stocks->sum('quantity');
                $totalAmt = (float) $item->unit_price * $stockQty;
                fputcsv($handle, [
                    $item->name,
                    $item->sku,
                    $item->bill_no ?? '-',
                    $item->purchase_date ? Carbon::parse($item->purchase_date)->format('d-m-Y') : '-',
                    $item->party_name ?? '-',
                    $item->supplier_name ?? '-',
                    $item->supplier_address ?? '-',
                    number_format((float) $item->unit_price, 2, '.', ''),
                    $stockQty,
                    number_format($totalAmt, 2, '.', ''),
                    $item->payment_method ?? '-',
                    $item->cheque_no ?? '-',
                    $item->transaction_id ?? '-',
                    $item->description ?? '-',
                    $item->user->name ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
    public function stock_index()
    {
        $inventoryItems = InventoryItem::all();
        $stocks = Stock::with(['inventoryItem', 'user'])->get();
        return view('admin.stock.index', compact('inventoryItems', 'stocks'));
    }

    public function stock_store(Request $request)
    {
        $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        Stock::create([
            'inventory_item_id' => $request->inventory_item_id,
            'quantity' => $request->quantity,
            'location' => $request->location,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Stock added successfully.');
    }
}
