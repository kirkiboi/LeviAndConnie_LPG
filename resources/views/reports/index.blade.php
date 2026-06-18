@extends('layouts.app')
@section('title', 'Sales Reports')
@section('header-title', 'Sales Reports')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Business Reports</div>
        <div class="page-subtitle">
            Showing: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
            @if($dateFrom !== $dateTo)
                — {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            @endif
        </div>
    </div>
    <button class="btn btn-ghost" onclick="window.print()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print Report
    </button>
</div>

{{-- Period Selector --}}
<form method="GET" action="{{ route('reports.index') }}" id="report-form">
    <div style="display:flex; align-items:flex-end; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
        <div>
            <label class="form-label">Period</label>
            <div class="period-selector">
                @foreach(['daily','weekly','monthly','yearly'] as $p)
                    <button type="button" class="period-btn {{ $period === $p ? 'active' : '' }}"
                        onclick="setPeriod('{{ $p }}')">
                        {{ ucfirst($p) }}
                    </button>
                @endforeach
            </div>
        </div>
        <input type="hidden" name="period" id="period-input" value="{{ $period }}">
        <div class="form-group mb-0">
            <label class="form-label">Date</label>
            <input type="date" name="date" id="date-input" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Generate
        </button>
    </div>
</form>

{{-- ── KPI Cards ── --}}
<div class="stat-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">₱{{ number_format($totalSales, 2) }}</div>
            <div class="stat-label">Total Sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">₱{{ number_format($totalExpenses, 2) }}</div>
            <div class="stat-label">Total Expenses</div>
        </div>
    </div>
    <div class="stat-card" style="border-color: {{ $netProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}; box-shadow: 0 0 0 1px {{ $netProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}20;">
        <div class="stat-icon {{ $netProfit >= 0 ? 'green' : 'red' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                @if($netProfit >= 0)
                    <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>
                @else
                    <polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/>
                @endif
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value {{ $netProfit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                {{ $netProfit >= 0 ? '' : '-' }}₱{{ number_format(abs($netProfit), 2) }}
            </div>
            <div class="stat-label">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 320px; gap:20px; margin-bottom:20px;">
    {{-- Expense breakdown --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg>
                Expense Breakdown
            </div>
        </div>
        <div class="card-body">
            @php
                $expenseItems = [
                    [
                        'label' => 'Employee Wages',
                        'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:6px; color:var(--info);"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                        'amount' => $wageExpenses,
                        'class' => 'badge-info'
                    ],
                    [
                        'label' => 'Stock Purchases',
                        'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:6px; color:var(--warning);"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
                        'amount' => $stockExpenses,
                        'class' => 'badge-warning'
                    ],
                    [
                        'label' => 'Other Expenses',
                        'icon' => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:6px; color:var(--text-muted);"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
                        'amount' => $otherExpenses,
                        'class' => 'badge-muted'
                    ],
                ];
            @endphp
            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach($expenseItems as $exp)
                @php $pct = $totalExpenses > 0 ? ($exp['amount'] / $totalExpenses) * 100 : 0; @endphp
                <div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:13.5px;">
                        <span>{!! $exp['icon'] !!}{{ $exp['label'] }}</span>
                        <span class="font-semibold">₱{{ number_format($exp['amount'], 2) }} <span class="text-muted">({{ number_format($pct, 0) }}%)</span></span>
                    </div>
                    <div style="height:8px; background:var(--border); border-radius:99px; overflow:hidden;">
                        <div style="height:100%; width:{{ $pct }}%; background:var(--accent); border-radius:99px; transition:width 0.6s ease;"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="divider"></div>

            <div style="display:flex; justify-content:space-between; font-size:15px; font-weight:700; color:var(--text-primary);">
                <span>Total Expenses</span>
                <span class="text-danger">₱{{ number_format($totalExpenses, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Top products --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Top Products
            </div>
        </div>
        <div class="card-body" style="padding:12px;">
            @forelse($topProducts as $i => $tp)
            <div style="display:flex; align-items:center; gap:10px; padding:8px 6px; {{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                <div style="width:26px; height:26px; background:{{ ['var(--accent)','var(--info)','var(--success)','var(--warning)','var(--text-muted)'][$i] }}22; border-radius:50%; display:flex;align-items:center;justify-content:center; font-size:12px; font-weight:800; color:{{ ['var(--accent)','var(--info)','var(--success)','var(--warning)','var(--text-muted)'][$i] }}; flex-shrink:0;">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $tp->product?->name ?? 'Deleted' }}
                    </div>
                    <div style="font-size:11px; color:var(--text-muted);">{{ $tp->total_qty }} sold</div>
                </div>
                <span class="text-accent font-semibold" style="font-size:13px; white-space:nowrap;">₱{{ number_format($tp->total_revenue, 0) }}</span>
            </div>
            @empty
            <div class="text-center td-muted" style="padding:24px; font-size:13px;">No sales data for this period.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Daily breakdown table --}}
@if(count($dailyBreakdown) > 1)
<div class="card mb-6">
    <div class="card-header">
        <div class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Daily Breakdown
        </div>
        <span class="text-muted text-sm">{{ count($dailyBreakdown) }} days</span>
    </div>
    <div class="table-wrapper" style="border:none; border-radius:0;">
        <table class="breakdown-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th style="text-align:right;">Sales</th>
                    <th style="text-align:right;">Expenses</th>
                    <th style="text-align:right;">Net Profit/Loss</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSalesSum = 0; $totalExpSum = 0; $totalProfitSum = 0; @endphp
                @foreach($dailyBreakdown as $row)
                @php
                    $totalSalesSum  += $row['sales'];
                    $totalExpSum    += $row['expenses'];
                    $totalProfitSum += $row['profit'];
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('M d, Y') }}</td>
                    <td class="td-muted">{{ \Carbon\Carbon::parse($row['date'])->format('l') }}</td>
                    <td style="text-align:right;" class="font-semibold">₱{{ number_format($row['sales'], 2) }}</td>
                    <td style="text-align:right;" class="text-danger">₱{{ number_format($row['expenses'], 2) }}</td>
                    <td style="text-align:right;" class="profit-cell {{ $row['profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ $row['profit'] >= 0 ? '' : '-' }}₱{{ number_format(abs($row['profit']), 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:var(--bg-hover); font-weight:700; border-top:2px solid var(--border);">
                    <td colspan="2" style="padding:11px 16px; color:var(--text-primary);">TOTAL</td>
                    <td style="text-align:right; padding:11px 16px;">₱{{ number_format($totalSalesSum, 2) }}</td>
                    <td style="text-align:right; padding:11px 16px; color:var(--danger);">₱{{ number_format($totalExpSum, 2) }}</td>
                    <td style="text-align:right; padding:11px 16px;" class="{{ $totalProfitSum >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ $totalProfitSum >= 0 ? '' : '-' }}₱{{ number_format(abs($totalProfitSum), 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif

{{-- Recent orders in period --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Transactions in Period
        </div>
        <span class="text-muted text-sm">{{ $recentOrders->count() }} records shown</span>
    </div>
    <div class="table-wrapper" style="border:none; border-radius:0;">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date / Time</th>
                    <th>Cashier</th>
                    <th>Items</th>
                    <th>Cash</th>
                    <th>GCash</th>
                    <th>Total</th>
                    <th>Change</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td><span class="badge badge-muted">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                    <td>
                        <div style="font-size:13px;">{{ $order->created_at->format('M d, Y') }}</div>
                        <div style="font-size:11.5px; color:var(--text-muted);">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td>{{ $order->employee?->short_name ?? '—' }}</td>
                    <td class="td-muted">{{ $order->items->count() }}</td>
                    <td class="td-muted">₱{{ number_format($order->cash_amount, 2) }}</td>
                    <td>
                        @if($order->gcash_amount > 0)
                            <span class="text-accent font-semibold">₱{{ number_format($order->gcash_amount, 2) }}</span>
                            @if($order->gcash_reference)
                                <div style="font-size:10.5px; color:var(--text-muted);">{{ $order->gcash_reference }}</div>
                            @endif
                        @else
                            <span class="td-muted">—</span>
                        @endif
                    </td>
                    <td class="font-semibold text-accent">₱{{ number_format($order->total_amount, 2) }}</td>
                    <td class="td-muted">₱{{ number_format($order->change_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center td-muted" style="padding:32px;">No transactions for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setPeriod(p) {
    document.getElementById('period-input').value = p;
    document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('report-form').submit();
}
</script>
@endpush
