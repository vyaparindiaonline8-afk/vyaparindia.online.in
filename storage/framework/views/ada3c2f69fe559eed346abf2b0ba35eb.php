<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Express Checkout</h1>
        <p class="text-xs text-gray-500 mt-1">Direct order from <?php echo e($sellerPage->page_title); ?></p>
    </div>

    <!-- Empty Cart Fallback -->
    <div id="checkout-empty-state" class="hidden bg-white rounded-3xl p-16 text-center border border-gray-200">
        <i class="fa-solid fa-cart-shopping text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-bold text-gray-900">Your bag is empty</h3>
        <p class="text-xs text-gray-500 mt-1">Please add items to your cart before proceeding to checkout.</p>
        <a href="<?php echo e(route('minisite.products', $sellerPage->slug)); ?>" class="mt-4 inline-block px-6 py-2.5 rounded-xl bg-gray-900 text-white font-semibold text-xs hover:bg-gray-800">
            Browse Store Products
        </a>
    </div>

    <!-- Checkout Grid -->
    <div id="checkout-container" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Customer & Delivery Form (Left 7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form id="checkout-form" onsubmit="submitCheckout(event)" class="space-y-6">
                <?php echo csrf_field(); ?>
                <!-- Contact & Shipping Details -->
                <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-4">
                    <div class="flex items-center gap-2 text-gray-900 font-bold text-base pb-3 border-b border-gray-100">
                        <i class="fa-solid fa-truck-fast text-brand-custom"></i>
                        <span>1. Shipping & Contact Information</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="customer_name" required placeholder="e.g. Rahul Sharma" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Mobile / WhatsApp Number <span class="text-red-500">*</span></label>
                            <input type="tel" id="customer_phone" required placeholder="10-digit mobile number" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Email Address (Optional)</label>
                            <input type="email" id="customer_email" placeholder="rahul@example.com" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Full Delivery Address <span class="text-red-500">*</span></label>
                            <textarea id="shipping_address" required rows="2" placeholder="House/Flat No., Street, Landmark" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                            <input type="text" id="city" required placeholder="e.g. Mumbai / Jaipur" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">PIN Code <span class="text-red-500">*</span></label>
                            <input type="text" id="pincode" required placeholder="6-digit PIN code" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Payment Selection -->
                <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-4">
                    <div class="flex items-center gap-2 text-gray-900 font-bold text-base pb-3 border-b border-gray-100">
                        <i class="fa-solid fa-credit-card text-brand-custom"></i>
                        <span>2. Choose Payment Mode</span>
                    </div>

                    <div class="space-y-3">
                        <?php if($sellerPage->enable_cod): ?>
                            <label class="flex items-center justify-between p-4 rounded-2xl border-2 border-brand-custom bg-brand-light/10 cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="cod" checked class="h-4 w-4 text-brand focus:ring-brand">
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">Cash on Delivery (COD)</div>
                                        <div class="text-[11px] text-gray-500">Pay cash/UPI when order reaches your doorstep</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-hand-holding-dollar text-emerald-600 text-lg"></i>
                            </label>
                        <?php endif; ?>

                        <label class="flex items-center justify-between p-4 rounded-2xl border border-gray-200 hover:border-gray-300 bg-white cursor-pointer">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="online" <?php echo e(!$sellerPage->enable_cod ? 'checked' : ''); ?> class="h-4 w-4 text-brand focus:ring-brand">
                                <div>
                                    <div class="text-xs font-bold text-gray-900">Pay Online (UPI / Card / NetBanking)</div>
                                    <div class="text-[11px] text-gray-500">Fast & 100% Secure Instant Payment</div>
                                </div>
                            </div>
                            <i class="fa-solid fa-qrcode text-blue-600 text-lg"></i>
                        </label>
                    </div>

                    <div class="pt-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Order Notes (Optional)</label>
                        <input type="text" id="order_notes" placeholder="Any special instructions for the seller" class="w-full px-4 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn" class="w-full py-4 px-6 rounded-2xl bg-brand-custom text-white font-extrabold text-base shadow-lg hover:opacity-95 active:scale-98 transition-all flex items-center justify-center gap-3">
                    <span id="btn-text">Confirm & Place Order</span>
                    <i class="fa-solid fa-shield-check"></i>
                </button>
            </form>
        </div>

        <!-- Order Summary (Right 5 Cols) -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 sticky top-24 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <h2 class="font-extrabold text-base text-gray-900">Order Summary</h2>
                    <span id="summary-items-count" class="text-xs font-semibold bg-gray-100 px-2.5 py-1 rounded-full text-gray-700">0 Items</span>
                </div>

                <!-- Summary Items List -->
                <div id="summary-items-list" class="divide-y divide-gray-100 max-h-72 overflow-y-auto pr-1">
                    <!-- Populated by JS -->
                </div>

                <!-- Cost Breakdown -->
                <div class="space-y-2 pt-4 border-t border-gray-100 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Items Subtotal:</span>
                        <span id="summary-subtotal" class="font-bold text-gray-800">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Delivery Fee:</span>
                        <span class="font-bold text-emerald-600">FREE</span>
                    </div>
                    <div class="flex justify-between text-base font-extrabold text-gray-900 pt-3 border-t border-gray-100">
                        <span>Grand Total:</span>
                        <span id="summary-total" class="text-brand-custom text-lg">₹0.00</span>
                    </div>
                </div>

                <!-- Verified Guarantee -->
                <div class="p-3.5 rounded-2xl bg-gray-50 border border-gray-100 text-[11px] text-gray-500 space-y-1">
                    <div class="flex items-center gap-1.5 font-bold text-gray-800">
                        <i class="fa-solid fa-lock text-emerald-600"></i>
                        <span>Secure Direct Checkout</span>
                    </div>
                    <p>Your order is directly transmitted to <strong><?php echo e($sellerPage->page_title); ?></strong> for immediate packing and dispatch.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function renderCheckoutSummary() {
        const cart = getCart();
        const emptyState = document.getElementById('checkout-empty-state');
        const checkoutGrid = document.getElementById('checkout-container');
        const listContainer = document.getElementById('summary-items-list');
        const countBadge = document.getElementById('summary-items-count');
        const subtotalEl = document.getElementById('summary-subtotal');
        const totalEl = document.getElementById('summary-total');

        if (cart.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            if (checkoutGrid) checkoutGrid.classList.add('hidden');
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');
        if (checkoutGrid) checkoutGrid.classList.remove('hidden');

        let totalQty = 0;
        let subtotal = 0;
        let html = '';

        cart.forEach(item => {
            let itemTotal = item.price * item.quantity;
            totalQty += item.quantity;
            subtotal += itemTotal;
            html += `
                <div class="py-3 flex items-center gap-3">
                    <img src="${item.image}" alt="${item.name}" class="h-12 w-12 object-cover rounded-lg border border-gray-100 shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-xs text-gray-900 truncate">${item.name}</div>
                        <div class="text-[11px] text-gray-500">Qty: ${item.quantity} × ₹${item.price.toFixed(2)}</div>
                    </div>
                    <div class="text-xs font-bold text-gray-900">₹${itemTotal.toFixed(2)}</div>
                </div>
            `;
        });

        if (listContainer) listContainer.innerHTML = html;
        if (countBadge) countBadge.innerText = totalQty + (totalQty === 1 ? ' Item' : ' Items');
        if (subtotalEl) subtotalEl.innerText = '₹' + subtotal.toFixed(2);
        if (totalEl) totalEl.innerText = '₹' + subtotal.toFixed(2);
    }

    async function submitCheckout(e) {
        e.preventDefault();
        const cart = getCart();
        if (cart.length === 0) {
            alert('Your cart is empty.');
            return;
        }

        const btn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        btn.disabled = true;
        btnText.innerText = 'Placing Order...';

        const paymentMethodEl = document.querySelector('input[name="payment_method"]:checked');

        const payload = {
            _token: '<?php echo e(csrf_token()); ?>',
            customer_name: document.getElementById('customer_name').value,
            customer_phone: document.getElementById('customer_phone').value,
            customer_email: document.getElementById('customer_email').value,
            shipping_address: document.getElementById('shipping_address').value,
            city: document.getElementById('city').value,
            pincode: document.getElementById('pincode').value,
            payment_method: paymentMethodEl ? paymentMethodEl.value : 'cod',
            notes: document.getElementById('order_notes').value,
            cart: cart.map(item => ({ id: item.id, quantity: item.quantity }))
        };

        try {
            const response = await fetch("<?php echo e(route('minisite.placeOrder', $sellerPage->slug)); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (response.ok && data.success) {
                // Clear local cart
                saveCart([]);
                window.location.href = data.redirect_url;
            } else {
                alert('Error placing order: ' + (data.message || 'Please check all required fields.'));
                btn.disabled = false;
                btnText.innerText = 'Confirm & Place Order';
            }
        } catch (error) {
            console.error(error);
            alert('Something went wrong. Please check your connection and try again.');
            btn.disabled = false;
            btnText.innerText = 'Confirm & Place Order';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCheckoutSummary();
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller-site.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller-site/checkout.blade.php ENDPATH**/ ?>