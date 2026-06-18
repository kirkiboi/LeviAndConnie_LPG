<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products   = Product::where('isActive', true)->orderBy('category')->orderBy('name')->get();
        $categories = Product::where('isActive', true)->whereNotNull('category')->distinct()->pluck('category');
        return view('pos.index', compact('products', 'categories'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'cash_amount'     => 'required|numeric|min:0',
            'gcash_amount'    => 'required|numeric|min:0',
            'gcash_reference' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $employeeId    = session('employee_id');
            $subtotal      = 0;
            $itemsToProcess = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                if (!$product || !$product->isActive) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Product not found or inactive.']);
                }
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$product->name}. Available: {$product->stock} {$product->unit}."
                    ]);
                }
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal    += $itemSubtotal;
                $itemsToProcess[] = [
                    'product'   => $product,
                    'quantity'  => $item['quantity'],
                    'unitPrice' => $product->price,
                    'subtotal'  => $itemSubtotal,
                ];
            }

            $cashAmount  = (float) $request->cash_amount;
            $gcashAmount = (float) $request->gcash_amount;
            $totalPaid   = $cashAmount + $gcashAmount;

            if (round($totalPaid, 2) < round($subtotal, 2)) {
                return response()->json(['success' => false, 'message' => 'Total payment is less than the order total.']);
            }

            $changeAmount = max(0, $totalPaid - $subtotal);

            $order = Order::create([
                'employee_id'     => $employeeId,
                'subtotal'        => $subtotal,
                'cash_amount'     => $cashAmount,
                'gcash_amount'    => $gcashAmount,
                'gcash_reference' => $request->gcash_reference,
                'total_amount'    => $subtotal,
                'change_amount'   => $changeAmount,
                'status'          => 'completed',
                'notes'           => $request->notes,
            ]);

            foreach ($itemsToProcess as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unitPrice'],
                    'subtotal'   => $item['subtotal'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);

                StockMovement::create([
                    'product_id'   => $item['product']->id,
                    'employee_id'  => $employeeId,
                    'type'         => 'sale',
                    'quantity'     => -$item['quantity'],
                    'notes'        => "Sale - Order #" . $order->id,
                    'reference_id' => $order->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $order->id,
                'change'   => $changeAmount,
                'message'  => 'Order completed successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}