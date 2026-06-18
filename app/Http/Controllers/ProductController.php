<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products   = Product::orderBy('category')->orderBy('name')->get();
        $categories = Product::whereNotNull('category')->distinct()->pluck('category');
        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:200',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'unit'               => 'required|string|max:50',
            'category'           => 'nullable|string|max:100',
            'stock'              => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
        ]);

        Product::create(array_merge(
            $request->only(['name', 'description', 'price', 'cost_price', 'unit', 'category', 'stock', 'low_stock_threshold']),
            ['isActive' => true]
        ));

        return back()->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'               => 'required|string|max:200',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'unit'               => 'required|string|max:50',
            'category'           => 'nullable|string|max:100',
            'low_stock_threshold'=> 'required|integer|min:0',
        ]);

        $product->update($request->only([
            'name', 'description', 'price', 'cost_price', 'unit', 'category', 'low_stock_threshold'
        ]));

        return back()->with('success', 'Product updated successfully.');
    }

    public function toggleActive(Product $product)
    {
        $product->update(['isActive' => !$product->isActive]);
        $status = $product->isActive ? 'activated' : 'deactivated';
        return back()->with('success', "Product {$status}.");
    }
}