@extends('layouts.app')
@section('title', 'Inventory')
@section('header-title', 'Inventory & Stock Management')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Stock Overview</div>
        <div class="page-subtitle">Monitor and manage product stock levels</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('inventory.movements') }}" class="btn btn-ghost">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
            Movement Log
        </a>
        <button class="btn btn-primary" onclick="openRestockModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Restock Product
        </button>
    </div>
</div>

{{-- Low stock alert banner --}}
@if($lowStockCount > 0)
<div class="alert alert-warning mb-4">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <strong>{{ $lowStockCount }} product(s)</strong> are at or below their low stock threshold. Consider restocking soon.
</div>
@endif

{{-- Filter --}}
<div class="filter-row">
    <div class="form-group">
        <label class="form-label">Search</label>
        <input type="text" id="stock-search" class="form-control" placeholder="Search products…" oninput="filterStock()">
    </div>
    <div class="form-group" style="min-width:160px; max-width:200px;">
        <label class="form-label">Category</label>
        <select id="stock-cat-filter" class="form-control" onchange="filterStock()">
            <option value="">All Categories</option>
            @foreach($products->pluck('category')->filter()->unique()->sort() as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group" style="min-width:160px; max-width:200px;">
        <label class="form-label">Stock Status</label>
        <select id="stock-status-filter" class="form-control" onchange="filterStock()">
            <option value="">All</option>
            <option value="low">Low Stock</option>
            <option value="ok">OK</option>
            <option value="out">Out of Stock</option>
        </select>
    </div>
</div>

<div class="table-wrapper">
    <table id="stock-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock Level</th>
                <th>Threshold</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            @php
                $pct = $product->stock > 0 ? min(100, ($product->stock / max($product->low_stock_threshold * 3, 1)) * 100) : 0;
                $barClass = $product->stock == 0 ? 'low' : ($product->isLowStock() ? 'medium' : 'high');
            @endphp
            <tr data-name="{{ strtolower($product->name) }}"
                data-cat="{{ strtolower($product->category) }}"
                data-stock="{{ $product->stock }}"
                data-threshold="{{ $product->low_stock_threshold }}">
                <td class="td-muted">{{ $product->id }}</td>
                <td>
                    <div style="font-weight:600; color:var(--text-primary);">{{ $product->name }}</div>
                    @if($product->description)
                    <div style="font-size:11.5px; color:var(--text-muted);">{{ Str::limit($product->description, 50) }}</div>
                    @endif
                </td>
                <td>
                    @if($product->category)
                        <span class="badge badge-muted">{{ $product->category }}</span>
                    @else
                        <span class="td-muted">—</span>
                    @endif
                </td>
                <td class="text-accent font-semibold">₱{{ number_format($product->price, 2) }}</td>
                <td style="min-width:160px;">
                    <div class="stock-indicator">
                        <div class="stock-bar-bg">
                            <div class="stock-bar-fill {{ $barClass }}" style="width:{{ $pct }}%;"></div>
                        </div>
                        <span class="stock-count {{ $product->stock == 0 || $product->isLowStock() ? 'low' : '' }}">
                            {{ $product->stock }} <span style="font-size:10px; color:var(--text-muted);">{{ $product->unit }}</span>
                        </span>
                    </div>
                </td>
                <td class="td-muted">{{ $product->low_stock_threshold }} {{ $product->unit }}</td>
                <td>
                    @if($product->stock == 0)
                        <span class="badge badge-danger">Out of Stock</span>
                    @elseif($product->isLowStock())
                        <span class="badge badge-warning">Low Stock</span>
                    @else
                        <span class="badge badge-success">In Stock</span>
                    @endif
                    @if(!$product->isActive)
                        <span class="badge badge-muted">Inactive</span>
                    @endif
                </td>
                <td>
                    <button class="btn btn-primary btn-sm"
                        onclick="openRestockForProduct({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->unit }}', {{ $product->cost_price }})">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Restock
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($products->isEmpty())
    <div class="text-center" style="padding:48px; color:var(--text-muted);">
        No products found. <a href="{{ route('products.index') }}">Add products</a> to get started.
    </div>
    @endif
</div>

{{-- ── Restock Modal ── --}}
<div class="modal-overlay" id="restock-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Restock Product</div>
            <button class="modal-close" onclick="closeRestockModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="{{ route('inventory.restock') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product</label>
                    <select name="product_id" id="restock-product-select" class="form-control" required>
                        <option value="">— Select Product —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-unit="{{ $p->unit }}" data-cost="{{ $p->cost_price }}">
                                {{ $p->name }} ({{ $p->stock }} {{ $p->unit }} in stock)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Quantity to Add</label>
                        <div class="input-group">
                            <input type="number" name="quantity" id="restock-qty" class="form-control" min="1" placeholder="0" required>
                            <span class="input-addon" id="restock-unit-label">pcs</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cost per Unit (₱) <span class="text-muted">(optional)</span></label>
                        <input type="number" name="cost_per_unit" id="restock-cost" class="form-control" step="0.01" min="0" placeholder="Use default">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="e.g. Supplier delivery, PO #123…">
                </div>
                <div style="background:var(--bg-input); border:1px solid var(--border); border-radius:var(--radius-sm); padding:12px; font-size:13px; color:var(--text-secondary);">
                    Providing a cost per unit will automatically log this as a stock purchase expense.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeRestockModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                    Confirm Restock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterStock() {
    const search  = document.getElementById('stock-search').value.toLowerCase();
    const cat     = document.getElementById('stock-cat-filter').value.toLowerCase();
    const status  = document.getElementById('stock-status-filter').value;

    document.querySelectorAll('#stock-table tbody tr').forEach(row => {
        const nm  = row.dataset.name  || '';
        const ct  = row.dataset.cat   || '';
        const stk = parseInt(row.dataset.stock);
        const thr = parseInt(row.dataset.threshold);

        const nameOk   = nm.includes(search);
        const catOk    = !cat || ct.includes(cat);
        let   statusOk = true;
        if (status === 'out')  statusOk = stk === 0;
        if (status === 'low')  statusOk = stk > 0 && stk <= thr;
        if (status === 'ok')   statusOk = stk > thr;

        row.style.display = (nameOk && catOk && statusOk) ? '' : 'none';
    });
}

function openRestockModal() {
    document.getElementById('restock-modal').classList.add('open');
}

function closeRestockModal() {
    document.getElementById('restock-modal').classList.remove('open');
}

function openRestockForProduct(id, name, unit, cost) {
    document.getElementById('restock-product-select').value = id;
    document.getElementById('restock-unit-label').textContent = unit;
    document.getElementById('restock-cost').value = cost || '';
    openRestockModal();
}

document.getElementById('restock-product-select')?.addEventListener('change', function() {
    const opt  = this.options[this.selectedIndex];
    document.getElementById('restock-unit-label').textContent = opt.dataset.unit || 'pcs';
    document.getElementById('restock-cost').value = opt.dataset.cost || '';
});
</script>
@endpush
