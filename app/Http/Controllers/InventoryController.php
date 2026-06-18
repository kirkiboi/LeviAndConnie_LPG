<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $products      = Product::orderBy('category')->orderBy('name')->get();
        $lowStockCount = Product::whereRaw('stock <= low_stock_threshold')->where('isActive', true)->count();
        return view('inventory.index', compact('products', 'lowStockCount'));
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'employee'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(20)->withQueryString();
        $products  = Product::orderBy('name')->get();
        return view('inventory.movements', compact('movements', 'products'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id',
            'quantity'      => 'required|integer|min:1',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        $product      = Product::find($request->product_id);
        $quantity     = (int) $request->quantity;
        $costPerUnit  = $request->cost_per_unit ?? $product->cost_price;
        $totalCost    = $costPerUnit * $quantity;

        DB::transaction(function () use ($product, $quantity, $request, $totalCost) {
            $product->increment('stock', $quantity);

            StockMovement::create([
                'product_id'  => $product->id,
                'employee_id' => session('employee_id'),
                'type'        => 'restock',
                'quantity'    => $quantity,
                'notes'       => $request->notes ?? "Restock: +{$quantity} {$product->unit}",
            ]);

            if ($totalCost > 0) {
                Expense::create([
                    'date'        => now()->toDateString(),
                    'description' => "Stock purchase: {$product->name} x{$quantity}",
                    'amount'      => $totalCost,
                    'type'        => 'stock_purchase',
                ]);
            }
        });

        return back()->with('success', "Successfully restocked {$quantity} {$product->unit} of {$product->name}.");
    }
}