@extends('layouts.app')
@section('title', 'Menu & Pricing')
@section('header-title', 'Menu & Pricing Configuration')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Products / Menu</div>
        <div class="page-subtitle">Manage product catalog, pricing, and availability</div>
    </div>
    <button class="btn btn-primary" onclick="openAddModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Product
    </button>
</div>

{{-- Filter row --}}
<div class="filter-row">
    <div class="form-group">
        <label class="form-label">Search</label>
        <input type="text" id="prod-search" class="form-control" placeholder="Search products…" oninput="filterProducts()">
    </div>
    <div class="form-group" style="max-width:200px;">
        <label class="form-label">Category</label>
        <select id="prod-cat" class="form-control" onchange="filterProducts()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group" style="max-width:160px;">
        <label class="form-label">Status</label>
        <select id="prod-status" class="form-control" onchange="filterProducts()">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
</div>

<div class="table-wrapper">
    <table id="products-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Unit</th>
                <th>Cost Price</th>
                <th>Selling Price</th>
                <th>Stock</th>
                <th>Low Stock</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr data-name="{{ strtolower($product->name) }}"
                data-cat="{{ strtolower($product->category) }}"
                data-status="{{ $product->isActive ? 'active' : 'inactive' }}">
                <td class="td-muted">{{ $product->id }}</td>
                <td>
                    <div style="font-weight:600; color:var(--text-primary);">{{ $product->name }}</div>
                    @if($product->description)
                        <div style="font-size:11.5px; color:var(--text-muted);">{{ Str::limit($product->description, 55) }}</div>
                    @endif
                </td>
                <td>
                    @if($product->category)
                        <span class="badge badge-muted">{{ $product->category }}</span>
                    @else
                        <span class="td-muted">—</span>
                    @endif
                </td>
                <td class="td-muted">{{ $product->unit }}</td>
                <td class="td-muted">₱{{ number_format($product->cost_price, 2) }}</td>
                <td>
                    <span class="text-accent font-semibold">₱{{ number_format($product->price, 2) }}</span>
                    @php $margin = $product->cost_price > 0 ? (($product->price - $product->cost_price) / $product->cost_price) * 100 : null; @endphp
                    @if($margin !== null)
                        <div style="font-size:11px; color:var(--success);">+{{ number_format($margin, 0) }}% margin</div>
                    @endif
                </td>
                <td>
                    <span style="font-weight:600; color:{{ $product->stock == 0 ? 'var(--danger)' : ($product->isLowStock() ? 'var(--warning)' : 'var(--success)') }}">
                        {{ $product->stock }} {{ $product->unit }}
                    </span>
                </td>
                <td class="td-muted">{{ $product->low_stock_threshold }} {{ $product->unit }}</td>
                <td>
                    <span class="badge {{ $product->isActive ? 'badge-success' : 'badge-muted' }}">
                        {{ $product->isActive ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td style="text-align:right;">
                    <div class="flex gap-2" style="justify-content:flex-end;">
                        <button class="btn btn-ghost btn-sm" onclick='openEditModal(@json($product))'>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit
                        </button>
                        <form action="{{ route('products.toggle', $product) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $product->isActive ? 'btn-ghost' : 'btn-secondary' }}"
                                onclick="return confirm('{{ $product->isActive ? 'Deactivate' : 'Activate' }} this product?')">
                                {{ $product->isActive ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center td-muted" style="padding:48px;">
                    No products yet. Click "Add Product" to get started.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── Add Product Modal ── --}}
<div class="modal-overlay" id="add-modal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div class="modal-title">Add New Product</div>
            <button class="modal-close" onclick="document.getElementById('add-modal').classList.remove('open')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. LPG 11kg Cylinder" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Optional product description…"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" placeholder="e.g. LPG Cylinder, Accessories" list="cat-list">
                        <datalist id="cat-list">
                            @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit *</label>
                        <input type="text" name="unit" class="form-control" value="pcs" placeholder="e.g. pcs, kg, cylinder" required list="unit-list">
                        <datalist id="unit-list">
                            <option value="pcs"><option value="kg"><option value="cylinder"><option value="tank"><option value="liter">
                        </datalist>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cost Price (₱) *</label>
                        <input type="number" name="cost_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selling Price (₱) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Initial Stock *</label>
                        <input type="number" name="stock" class="form-control" min="0" value="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Low Stock Alert Threshold *</label>
                        <input type="number" name="low_stock_threshold" class="form-control" min="0" value="5" required>
                        <div class="form-hint">Alert when stock reaches this level</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('add-modal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Edit Product Modal ── --}}
<div class="modal-overlay" id="edit-modal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div class="modal-title">Edit Product</div>
            <button class="modal-close" onclick="document.getElementById('edit-modal').classList.remove('open')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="edit-form" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="edit-description" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" id="edit-category" class="form-control" list="cat-list">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit *</label>
                        <input type="text" name="unit" id="edit-unit" class="form-control" required list="unit-list">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Cost Price (₱)</label>
                        <input type="number" name="cost_price" id="edit-cost-price" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selling Price (₱) *</label>
                        <input type="number" name="price" id="edit-price" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" id="edit-threshold" class="form-control" min="0" required>
                    <div class="form-hint">Note: Use Inventory → Restock to change stock quantity.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('edit-modal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterProducts() {
    const search = document.getElementById('prod-search').value.toLowerCase();
    const cat    = document.getElementById('prod-cat').value.toLowerCase();
    const status = document.getElementById('prod-status').value;

    document.querySelectorAll('#products-table tbody tr').forEach(row => {
        const nm = row.dataset.name || '';
        const ct = row.dataset.cat  || '';
        const st = row.dataset.status || '';
        const ok = nm.includes(search) && (!cat || ct.includes(cat)) && (!status || st === status);
        row.style.display = ok ? '' : 'none';
    });
}

function openAddModal() {
    document.getElementById('add-modal').classList.add('open');
}

function openEditModal(product) {
    document.getElementById('edit-form').action = `/products/${product.id}`;
    document.getElementById('edit-name').value        = product.name;
    document.getElementById('edit-description').value = product.description || '';
    document.getElementById('edit-category').value    = product.category || '';
    document.getElementById('edit-unit').value        = product.unit;
    document.getElementById('edit-cost-price').value  = product.cost_price;
    document.getElementById('edit-price').value       = product.price;
    document.getElementById('edit-threshold').value   = product.low_stock_threshold;
    document.getElementById('edit-modal').classList.add('open');
}
</script>
@endpush
