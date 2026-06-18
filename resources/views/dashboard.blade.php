@extends('layouts.app')
@section('title', 'Dashboard')
@section('header-title', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">₱{{ number_format($todaySales, 2) }}</div>
            <div class="stat-label">Today's Sales</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $todayOrders }}</div>
            <div class="stat-label">Orders Today</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $lowStockCount > 0 ? 'red' : 'green' }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $lowStockCount }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $activeEmployeesToday }}</div>
            <div class="stat-label">Staff Today</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 320px; gap: 20px;">
    {{-- Recent Orders --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                Recent Transactions
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="table-wrapper" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Items</th>
                        <th>Cashier</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td><span class="badge badge-muted">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                        <td>{{ $order->items->count() }} item(s)</td>
                        <td>{{ $order->employee?->short_name ?? '—' }}</td>
                        <td>
                            @if($order->cash_amount > 0 && $order->gcash_amount > 0)
                                <span class="badge badge-info">Split</span>
                            @elseif($order->gcash_amount > 0)
                                <span class="badge badge-orange">GCash</span>
                            @else
                                <span class="badge badge-muted">Cash</span>
                            @endif
                        </td>
                        <td class="font-semibold text-accent">₱{{ number_format($order->total_amount, 2) }}</td>
                        <td class="td-muted">{{ $order->created_at->format('h:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center td-muted" style="padding:32px;">No transactions yet today</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Low Stock Alerts --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Low Stock Alerts
            </div>
            <a href="{{ route('inventory.index') }}" class="btn btn-ghost btn-sm">View</a>
        </div>
        <div class="card-body">
            @forelse($lowStockItems as $item)
            <div style="padding: 10px 0; border-bottom: 1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $item->name }}</div>
                    <div style="font-size:11px; color:var(--text-muted);">{{ $item->category ?? 'Uncategorized' }}</div>
                </div>
                <span class="badge {{ $item->stock == 0 ? 'badge-danger' : 'badge-warning' }}">
                    {{ $item->stock }} {{ $item->unit }}
                </span>
            </div>
            @empty
            <div class="text-center td-muted" style="padding: 24px 0;">
                All stock levels are healthy!
            </div>
            @endforelse
        </div>

        <div class="card-header" style="margin-top:4px;">
            <div class="card-title">Quick Actions</div>
        </div>
        <div class="card-body" style="display:flex; flex-direction:column; gap:8px;">
            <a href="{{ route('pos.index') }}" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                New Sale (POS)
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Restock Items
            </a>
        </div>
    </div>
</div>
@endsection