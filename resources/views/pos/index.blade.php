@extends('layouts.app')
@section('title', 'Point of Sale')
@section('header-title', 'Point of Sale')

@section('content')
<div class="pos-layout">

    {{-- ── LEFT: Product Grid ── --}}
    <div class="pos-products">
        {{-- Search + Controls --}}
        <div class="pos-search-bar">
            <input type="text" id="product-search" class="form-control" placeholder="Search products..." style="flex:1;">
            <span class="badge badge-orange" style="padding:8px 14px; font-size:13px;" id="cart-count-badge">Cart: 0</span>
        </div>

        {{-- Category tabs --}}
        <div class="category-tabs">
            <button class="category-tab active" data-cat="all">All</button>
            @foreach($categories as $cat)
                <button class="category-tab" data-cat="{{ $cat }}">{{ $cat }}</button>
            @endforeach
        </div>

        {{-- Product grid --}}
        <div class="product-grid" id="product-grid">
            @foreach($products as $product)
            <div class="product-item {{ $product->stock == 0 ? 'out-of-stock' : '' }}"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-stock="{{ $product->stock }}"
                 data-unit="{{ $product->unit }}"
                 data-cat="{{ $product->category }}"
                 onclick="addToCart(this)">
                <div class="product-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <ellipse cx="12" cy="5" rx="9" ry="3"/>
                        <path d="M3 5v14a9 3 0 0018 0V5"/>
                        <path d="M3 12a9 3 0 0018 0"/>
                    </svg>
                </div>
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                <div class="product-stock {{ $product->isLowStock() && $product->stock > 0 ? 'low' : '' }}">
                    @if($product->stock == 0)
                        Out of Stock
                    @else
                        Stock: {{ $product->stock }} {{ $product->unit }}
                    @endif
                </div>
            </div>
            @endforeach

            @if($products->isEmpty())
            <div style="grid-column:1/-1; text-align:center; padding:48px 0; color:var(--text-muted);">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                <div>No active products. <a href="{{ route('products.index') }}">Add products</a> first.</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── RIGHT: Cart Panel ── --}}
    <div class="cart-panel">
        <div class="cart-header">
            <div class="cart-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                Order Cart
            </div>
            <button onclick="clearCart()" class="btn btn-ghost btn-sm" id="clear-cart-btn" style="display:none;">Clear</button>
        </div>

        {{-- Cart items --}}
        <div class="cart-items" id="cart-items">
            <div class="cart-empty" id="cart-empty">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                <div style="font-size:13px; font-weight:500;">Cart is empty</div>
                <div style="font-size:12px;">Click a product to add it</div>
            </div>
        </div>

        {{-- Cart totals + checkout --}}
        <div class="cart-footer">
            <div class="cart-totals" id="cart-totals" style="display:none;">
                <div class="cart-row">
                    <span class="text-muted">Subtotal</span>
                    <span id="cart-subtotal">₱0.00</span>
                </div>
                <div class="cart-row total">
                    <span>Total</span>
                    <span class="text-accent" id="cart-total">₱0.00</span>
                </div>
            </div>
            <button class="btn btn-primary btn-block" id="checkout-btn" onclick="openPaymentModal()" disabled>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Proceed to Payment
            </button>
        </div>
    </div>
</div>

{{-- ── Payment Modal ── --}}
<div class="modal-overlay" id="payment-modal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <div class="modal-title">Payment</div>
            <button class="modal-close" onclick="closePaymentModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="modal-body">
            {{-- Order summary --}}
            <div class="card mb-4" style="background:var(--bg-primary);">
                <div class="card-header" style="font-size:13px; font-weight:600;">Order Summary</div>
                <div id="payment-order-summary" style="padding:12px 16px; max-height:180px; overflow-y:auto;"></div>
                <div style="padding:10px 16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:700; font-size:15px;">Total Due</span>
                    <span class="text-accent" style="font-size:20px; font-weight:800;" id="payment-total-display">₱0.00</span>
                </div>
            </div>

            {{-- Payment method toggle --}}
            <div class="form-group">
                <label class="form-label">Payment Method</label>
                <div class="payment-method-toggle">
                    <div class="payment-option active" id="opt-cash" onclick="selectPaymentMethod('cash')">
                        <div class="payment-option-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-banknote" style="margin-bottom: 4px;"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                        </div>
                        <div class="payment-option-label">Cash Only</div>
                    </div>
                    <div class="payment-option" id="opt-gcash" onclick="selectPaymentMethod('gcash')">
                        <div class="payment-option-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-smartphone" style="margin-bottom: 4px;"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                        </div>
                        <div class="payment-option-label">GCash Only</div>
                    </div>
                    <div class="payment-option" id="opt-split" onclick="selectPaymentMethod('split')" style="grid-column:1/-1;">
                        <div class="payment-option-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shuffle" style="margin-bottom: 4px;"><path d="M2 18h1.4c1.3 0 2.5-.6 3.3-1.7l6.1-8.6c.7-1.1 2-1.7 3.3-1.7H22"/><path d="m18 2 4 4-4 4"/><path d="M2 6h1.9c1.2 0 2.3.6 3 1.7l1.1 1.6"/><path d="m15.4 12.8 1.2 1.7c.8 1.1 2 1.7 3.2 1.7H22"/><path d="m18 14 4 4-4 4"/></svg>
                        </div>
                        <div class="payment-option-label">Split (Cash + GCash)</div>
                    </div>
                </div>
            </div>

            {{-- Cash amount --}}
            <div id="cash-section">
                <div class="form-group">
                    <label class="form-label">Cash Amount (₱)</label>
                    <input type="number" id="cash-input" class="form-control" placeholder="0.00" min="0" step="0.01" oninput="recalcChange()">
                </div>
            </div>

            {{-- GCash section --}}
            <div id="gcash-section" style="display:none;">
                <div class="form-group">
                    <label class="form-label">GCash Amount (₱)</label>
                    <input type="number" id="gcash-input" class="form-control" placeholder="0.00" min="0" step="0.01" oninput="recalcChange()">
                </div>
                <div class="form-group">
                    <label class="form-label">GCash Reference Number</label>
                    <input type="text" id="gcash-ref" class="form-control" placeholder="e.g. GC1234567890">
                </div>
            </div>

            {{-- Notes --}}
            <div class="form-group">
                <label class="form-label">Notes (optional)</label>
                <input type="text" id="order-notes" class="form-control" placeholder="Any notes for this order…">
            </div>

            {{-- Change display --}}
            <div class="change-display">
                <div class="change-label">Change</div>
                <div class="change-value" id="change-display">₱0.00</div>
                <div style="font-size:11px; color:var(--text-muted); margin-top:4px;" id="change-hint">Enter payment amount above</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closePaymentModal()">Cancel</button>
            <button class="btn btn-success btn-lg" id="confirm-payment-btn" onclick="confirmPayment()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Confirm Payment
            </button>
        </div>
    </div>
</div>

{{-- ── Receipt Modal ── --}}
<div class="modal-overlay" id="receipt-modal">
    <div class="modal" style="max-width:400px; text-align:center;">
        <div class="modal-body" style="padding:36px;">
            <div style="width:64px;height:64px;background:var(--success-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="font-size:20px; font-weight:800; color:var(--text-primary); margin-bottom:6px;">Payment Successful!</div>
            <div style="font-size:13px; color:var(--text-secondary); margin-bottom:20px;">Order <span id="receipt-order-id" class="text-accent"></span> completed.</div>

            <div style="background:var(--bg-input); border:1px solid var(--border); border-radius:var(--radius-sm); padding:16px; margin-bottom:20px;">
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Change</div>
                <div style="font-size:36px; font-weight:800; color:var(--success);" id="receipt-change">₱0.00</div>
            </div>

            <button class="btn btn-primary btn-block btn-lg" onclick="newOrder()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Order
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cart = [];
let currentPaymentMethod = 'cash';

document.getElementById('product-search').addEventListener('input', function() {
    filterProducts();
});

document.querySelectorAll('.category-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        filterProducts();
    });
});

function filterProducts() {
    const search = document.getElementById('product-search').value.toLowerCase();
    const activeCat = document.querySelector('.category-tab.active')?.dataset.cat || 'all';

    document.querySelectorAll('.product-item').forEach(item => {
        const nameMatch = item.dataset.name.toLowerCase().includes(search);
        const catMatch  = activeCat === 'all' || item.dataset.cat === activeCat;
        item.style.display = (nameMatch && catMatch) ? '' : 'none';
    });
}

function addToCart(el) {
    if (el.classList.contains('out-of-stock')) return;

    const id    = el.dataset.id;
    const name  = el.dataset.name;
    const price = parseFloat(el.dataset.price);
    const stock = parseInt(el.dataset.stock);
    const unit  = el.dataset.unit;

    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.qty >= stock) {
            showToast('Max stock reached for ' + name, 'warning');
            return;
        }
        existing.qty++;
    } else {
        cart.push({ id, name, price, stock, unit, qty: 1 });
    }
    renderCart();
}

function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.id !== id);
    } else if (item.qty > item.stock) {
        item.qty = item.stock;
        showToast('Max stock reached for ' + item.name, 'warning');
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

function renderCart() {
    const container  = document.getElementById('cart-items');
    const emptyEl    = document.getElementById('cart-empty');
    const totalsEl   = document.getElementById('cart-totals');
    const checkoutBtn = document.getElementById('checkout-btn');
    const clearBtn   = document.getElementById('clear-cart-btn');
    const countBadge = document.getElementById('cart-count-badge');

    container.querySelectorAll('.cart-item').forEach(el => el.remove());

    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const count = cart.reduce((s, i) => s + i.qty, 0);

    countBadge.textContent = 'Cart: ' + count;

    if (cart.length === 0) {
        emptyEl.style.display = 'flex';
        totalsEl.style.display = 'none';
        checkoutBtn.disabled  = true;
        clearBtn.style.display = 'none';
        return;
    }

    emptyEl.style.display  = 'none';
    totalsEl.style.display = 'block';
    checkoutBtn.disabled   = false;
    clearBtn.style.display = '';

    cart.forEach(item => {
        const el = document.createElement('div');
        el.className = 'cart-item';
        el.innerHTML = `
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">₱${item.price.toFixed(2)} × ${item.qty} = <strong>₱${(item.price * item.qty).toFixed(2)}</strong></div>
            </div>
            <div class="cart-qty">
                <button class="qty-btn" onclick="updateQty('${item.id}', -1)">−</button>
                <span class="qty-value">${item.qty}</span>
                <button class="qty-btn" onclick="updateQty('${item.id}', 1)">+</button>
            </div>
            <button class="cart-item-remove" onclick="removeFromCart('${item.id}')" title="Remove">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>`;
        container.appendChild(el);
    });

    document.getElementById('cart-subtotal').textContent = '₱' + total.toFixed(2);
    document.getElementById('cart-total').textContent    = '₱' + total.toFixed(2);
}

function openPaymentModal() {
    if (cart.length === 0) return;
    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);

    let summaryHtml = '';
    cart.forEach(item => {
        summaryHtml += `<div style="display:flex;justify-content:space-between;padding:5px 0;font-size:13px;border-bottom:1px solid var(--border);">
            <span>${item.name} <span style="color:var(--text-muted)">× ${item.qty}</span></span>
            <span>₱${(item.price * item.qty).toFixed(2)}</span>
        </div>`;
    });
    document.getElementById('payment-order-summary').innerHTML = summaryHtml;
    document.getElementById('payment-total-display').textContent = '₱' + total.toFixed(2);

    selectPaymentMethod('cash');
    document.getElementById('cash-input').value    = total.toFixed(2);
    document.getElementById('gcash-input').value   = '';
    document.getElementById('gcash-ref').value     = '';
    document.getElementById('order-notes').value   = '';
    recalcChange();

    document.getElementById('payment-modal').classList.add('open');
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.remove('open');
}

function selectPaymentMethod(method) {
    currentPaymentMethod = method;
    ['cash','gcash','split'].forEach(m => {
        document.getElementById('opt-' + m)?.classList.remove('active');
    });
    document.getElementById('opt-' + method).classList.add('active');

    const total = cart.reduce((s, i) => s + i.price * i.qty, 0);

    if (method === 'cash') {
        document.getElementById('cash-section').style.display = '';
        document.getElementById('gcash-section').style.display = 'none';
        document.getElementById('cash-input').value = total.toFixed(2);
        document.getElementById('gcash-input').value = '0';
    } else if (method === 'gcash') {
        document.getElementById('cash-section').style.display = 'none';
        document.getElementById('gcash-section').style.display = '';
        document.getElementById('gcash-input').value = total.toFixed(2);
        document.getElementById('cash-input').value = '0';
    } else {
        document.getElementById('cash-section').style.display = '';
        document.getElementById('gcash-section').style.display = '';
        document.getElementById('cash-input').value = '';
        document.getElementById('gcash-input').value = '';
    }
    recalcChange();
}

function recalcChange() {
    const total    = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const cash     = parseFloat(document.getElementById('cash-input').value)  || 0;
    const gcash    = parseFloat(document.getElementById('gcash-input').value) || 0;
    const paid     = cash + gcash;
    const change   = paid - total;
    const changeEl = document.getElementById('change-display');
    const hintEl   = document.getElementById('change-hint');

    changeEl.textContent = '₱' + Math.abs(change).toFixed(2);
    changeEl.className   = 'change-value';

    if (paid === 0) {
        hintEl.textContent = 'Enter payment amount above';
    } else if (change < -0.005) {
        changeEl.classList.add('underpaid');
        hintEl.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:4px;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> Short by ₱' + Math.abs(change).toFixed(2);
    } else {
        changeEl.classList.remove('underpaid');
        hintEl.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg> Payment OK';
    }
}

async function confirmPayment() {
    const total    = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const cash     = parseFloat(document.getElementById('cash-input').value)  || 0;
    const gcash    = parseFloat(document.getElementById('gcash-input').value) || 0;
    const gcashRef = document.getElementById('gcash-ref').value.trim();
    const notes    = document.getElementById('order-notes').value.trim();

    if (cash + gcash < total - 0.005) {
        showToast('Payment amount is insufficient!', 'danger');
        return;
    }

    if (gcash > 0 && !gcashRef) {
        showToast('Please enter the GCash reference number.', 'warning');
        return;
    }

    const btn = document.getElementById('confirm-payment-btn');
    btn.disabled  = true;
    btn.innerHTML = '<span class="spinner"></span> Processing…';

    const payload = {
        items: cart.map(i => ({ product_id: i.id, quantity: i.qty })),
        cash_amount:  cash,
        gcash_amount: gcash,
        gcash_reference: gcashRef,
        notes: notes,
        _token: document.querySelector('meta[name="csrf-token"]').content,
    };

    try {
        const res  = await fetch('{{ route("pos.checkout") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': payload._token },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        btn.disabled  = false;
        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Confirm Payment';

        if (data.success) {
            closePaymentModal();
            document.getElementById('receipt-order-id').textContent = '#' + String(data.order_id).padStart(4, '0');
            document.getElementById('receipt-change').textContent   = '₱' + parseFloat(data.change).toFixed(2);
            document.getElementById('receipt-modal').classList.add('open');
            reloadProducts();
        } else {
            showToast(data.message || 'Checkout failed.', 'danger');
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = '✓ Confirm Payment';
        showToast('Network error. Please try again.', 'danger');
    }
}

function newOrder() {
    cart = [];
    renderCart();
    document.getElementById('receipt-modal').classList.remove('open');
    reloadProducts();
}

function reloadProducts() {
    window.location.reload();
}

function showToast(message, type = 'info') {
    const colors = { danger:'var(--danger)', warning:'var(--warning)', info:'var(--info)', success:'var(--success)' };
    const toast  = document.createElement('div');
    toast.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;background:var(--bg-card);border:1px solid ${colors[type]};color:${colors[type]};padding:12px 20px;border-radius:var(--radius-sm);font-size:13.5px;font-weight:500;box-shadow:var(--shadow-lg);animation:slideUp 0.2s ease;max-width:360px;`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity 0.4s'; setTimeout(() => toast.remove(), 400); }, 3000);
}
</script>
@endpush