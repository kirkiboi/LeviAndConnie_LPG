<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Employee;
use App\Models\EmployeeSession;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $todaySales   = Order::where('status', 'completed')->whereDate('created_at', $today)->sum('total_amount');
        $todayOrders  = Order::where('status', 'completed')->whereDate('created_at', $today)->count();
        $lowStockCount = Product::whereRaw('stock <= low_stock_threshold')->where('isActive', true)->count();
        $activeEmployeesToday = EmployeeSession::where('date', $today)->distinct('employee_id')->count('employee_id');

        $recentOrders = Order::with(['employee', 'items.product'])
            ->latest()
            ->take(8)
            ->get();

        $lowStockItems = Product::whereRaw('stock <= low_stock_threshold')
            ->where('isActive', true)
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales', 'todayOrders', 'lowStockCount',
            'activeEmployeesToday', 'recentOrders', 'lowStockItems'
        ));
    }
}