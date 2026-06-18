<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Expense;
use App\Models\EmployeeSession;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        $date   = $request->get('date', now()->toDateString());

        [$dateFrom, $dateTo] = $this->getDateRange($period, $date);

        $totalSales  = Order::where('status', 'completed')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->sum('total_amount');

        $totalOrders = Order::where('status', 'completed')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->count();

        $stockExpenses = Expense::where('type', 'stock_purchase')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount');

        $wageExpenses  = $this->calculateWages($dateFrom, $dateTo);
        $otherExpenses = Expense::where('type', 'other')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount');

        $totalExpenses = $stockExpenses + $wageExpenses + $otherExpenses;
        $netProfit     = $totalSales - $totalExpenses;

        $dailyBreakdown = $this->getDailyBreakdown($dateFrom, $dateTo);

        $topProducts = OrderItem::with('product')
            ->whereHas('order', function ($q) use ($dateFrom, $dateTo) {
                $q->where('status', 'completed')
                  ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo]);
            })
            ->select('product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        $recentOrders = Order::with(['employee', 'items.product'])
            ->where('status', 'completed')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->latest()
            ->take(10)
            ->get();

        return view('reports.index', compact(
            'totalSales', 'totalOrders', 'totalExpenses', 'netProfit',
            'stockExpenses', 'wageExpenses', 'otherExpenses',
            'dailyBreakdown', 'topProducts', 'recentOrders',
            'period', 'date', 'dateFrom', 'dateTo'
        ));
    }

    private function getDateRange(string $period, string $date): array
    {
        $carbon = Carbon::parse($date);
        return match ($period) {
            'weekly'  => [$carbon->startOfWeek()->toDateString(), $carbon->copy()->endOfWeek()->toDateString()],
            'monthly' => [$carbon->startOfMonth()->toDateString(), $carbon->copy()->endOfMonth()->toDateString()],
            'yearly'  => [$carbon->startOfYear()->toDateString(), $carbon->copy()->endOfYear()->toDateString()],
            default   => [$date, $date],
        };
    }

    private function calculateWages(string $dateFrom, string $dateTo): float
    {
        $sessions = EmployeeSession::with('employee')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();

        $wageMap = [];
        foreach ($sessions as $session) {
            $key = $session->employee_id . '-' . $session->date;
            if (!isset($wageMap[$key])) {
                $wageMap[$key] = $session->employee->daily_salary ?? 0;
            }
        }
        return array_sum($wageMap);
    }

    private function getDailyBreakdown(string $dateFrom, string $dateTo): array
    {
        $salesData = Order::where('status', 'completed')
            ->whereBetween(DB::raw('DATE(created_at)'), [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $expensesData = Expense::whereBetween('date', [$dateFrom, $dateTo])
            ->selectRaw('date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $sessions = EmployeeSession::with('employee')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get();
            
        $wagesData = [];
        foreach ($sessions as $session) {
            $date = $session->date;
            $empId = $session->employee_id;
            if (!isset($wagesData[$date])) {
                $wagesData[$date] = [];
            }
            $wagesData[$date][$empId] = $session->employee->daily_salary ?? 0;
        }

        $dailyWages = [];
        foreach ($wagesData as $date => $employees) {
            $dailyWages[$date] = array_sum($employees);
        }

        $start     = Carbon::parse($dateFrom);
        $end       = Carbon::parse($dateTo);
        $breakdown = [];

        while ($start->lte($end)) {
            $day      = $start->toDateString();
            $sales    = $salesData->get($day, 0);
            $expenses = $expensesData->get($day, 0);
            $wages    = $dailyWages[$day] ?? 0;

            $breakdown[] = [
                'date'     => $day,
                'sales'    => (float) $sales,
                'expenses' => (float) $expenses + $wages,
                'profit'   => (float) $sales - $expenses - $wages,
            ];
            $start->addDay();
        }
        return $breakdown;
    }
}