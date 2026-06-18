@extends('layouts.app')
@section('title', 'Stock Movements')
@section('header-title', 'Stock Movement Log')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Movement History</div>
        <div class="page-subtitle">Complete audit log of all stock changes</div>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-ghost">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Inventory
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('inventory.movements') }}">
    <div class="filter-row">
        <div class="form-group">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-control">
                <option value="">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="max-width:160px;">
            <label class="form-label">Type</label>
            <select name="type" class="form-control">
                <option value="">All Types</option>
                <option value="sale"       {{ request('type') == 'sale'       ? 'selected':'' }}>Sale</option>
                <option value="restock"    {{ request('type') == 'restock'    ? 'selected':'' }}>Restock</option>
                <option value="return"     {{ request('type') == 'return'     ? 'selected':'' }}>Return</option>
                <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected':'' }}>Adjustment</option>
            </select>
        </div>
        <div class="form-group" style="max-width:160px;">
            <label class="form-label">Date From</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="form-group" style="max-width:160px;">
            <label class="form-label">Date To</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="form-group" style="display:flex; align-items:flex-end; gap:8px;">
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
            <a href="{{ route('inventory.movements') }}" class="btn btn-ghost">Reset</a>
        </div>
    </div>
</form>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date & Time</th>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Processed By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $mov)
            <tr>
                <td class="td-muted">{{ $mov->id }}</td>
                <td>
                    <div style="font-size:13px; color:var(--text-primary);">{{ $mov->created_at->format('M d, Y') }}</div>
                    <div style="font-size:11.5px; color:var(--text-muted);">{{ $mov->created_at->format('h:i:s A') }}</div>
                </td>
                <td>
                    <span style="font-weight:600; color:var(--text-primary);">{{ $mov->product?->name ?? 'Deleted Product' }}</span>
                </td>
                <td>
                    @php
                        $typeMap = [
                            'sale'       => ['badge-danger',  '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px;"><line x1="7" y1="7" x2="17" y2="17"/><polyline points="17 7 17 17 7 17"/></svg>Sale'],
                            'restock'    => ['badge-success', '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px;"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>Restock'],
                            'return'     => ['badge-info',    '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px;"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>Return'],
                            'adjustment' => ['badge-warning', '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:4px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Adjustment'],
                        ];
                        [$badgeClass, $label] = $typeMap[$mov->type] ?? ['badge-muted', ucfirst($mov->type)];
                    @endphp
                    <span class="badge {{ $badgeClass }}">{!! $label !!}</span>
                </td>
                <td>
                    <span style="font-size:15px; font-weight:700; color:{{ $mov->quantity > 0 ? 'var(--success)' : 'var(--danger)' }}">
                        {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                        <span style="font-size:11px; font-weight:400; color:var(--text-muted)">{{ $mov->product?->unit }}</span>
                    </span>
                </td>
                <td>{{ $mov->employee?->short_name ?? '—' }}</td>
                <td class="td-muted" style="max-width:250px; font-size:12.5px;">{{ $mov->notes ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center td-muted" style="padding:48px;">
                    No stock movements found for the selected filters.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($movements->hasPages())
<div class="pagination">
    {{ $movements->links() }}
</div>
@endif
@endsection