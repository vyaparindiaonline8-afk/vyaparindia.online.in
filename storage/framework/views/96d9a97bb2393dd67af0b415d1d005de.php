<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 mb-6">
        <a href="<?php echo e(route('minisite.show', $sellerPage->slug)); ?>" class="hover:text-gray-900">Home</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <a href="<?php echo e(route('minisite.products', $sellerPage->slug)); ?>" class="hover:text-gray-900">Products</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="text-gray-900 font-semibold truncate"><?php echo e($product->name); ?></span>
    </nav>

    <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Image Left -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-100 relative">
                    <img id="main-product-image" src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover">
                    <?php if($product->category): ?>
                        <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-xs text-gray-800 text-xs font-bold px-3 py-1 rounded-lg shadow-xs">
                            <?php echo e($product->category->name); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Details Right -->
            <div class="flex flex-col">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight leading-snug">
                    <?php echo e($product->name); ?>

                </h1>

                <!-- Price Box -->
                <div class="mt-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-baseline gap-3">
                    <span class="text-3xl font-black text-gray-900">₹<?php echo e(number_format($product->price, 2)); ?></span>
                    <span class="text-sm text-gray-400 line-through">₹<?php echo e(number_format($product->price * 1.35, 2)); ?></span>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-md">SAVE 35%</span>
                </div>

                <!-- Short Highlights -->
                <div class="mt-6 grid grid-cols-2 gap-3 text-xs text-gray-600">
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <i class="fa-solid fa-shield-check text-emerald-600 text-base"></i>
                        <span>100% Quality Checked</span>
                    </div>
                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <i class="fa-solid fa-truck-fast text-blue-600 text-base"></i>
                        <span>Pan-India Dispatch</span>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="mt-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Quantity</label>
                    <div class="inline-flex items-center border-2 border-gray-200 rounded-xl bg-white overflow-hidden">
                        <button type="button" onclick="changeDetailQty(-1)" class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors">-</button>
                        <input type="number" id="detail-qty" value="1" min="1" class="w-12 text-center text-sm font-bold text-gray-900 border-none focus:outline-none" readonly>
                        <button type="button" onclick="changeDetailQty(1)" class="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 transition-colors">+</button>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="mt-8 space-y-3">
                    <!-- WhatsApp Direct Buy Button -->
                    <?php if($sellerPage->whatsapp_number): ?>
                        <button onclick="orderThisOnWhatsapp()" class="w-full py-3.5 px-6 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            <span>Order on WhatsApp (Fastest)</span>
                        </button>
                    <?php endif; ?>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Add to Bag -->
                        <button onclick="addDetailToBag()" class="py-3.5 px-4 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Add to Bag</span>
                        </button>

                        <!-- Instant Checkout -->
                        <button onclick="buyNowDirect()" class="py-3.5 px-4 rounded-2xl bg-brand-custom text-white font-bold text-xs sm:text-sm shadow-md hover:opacity-95 transition-all flex items-center justify-center gap-2">
                            <span>Buy Now</span>
                            <i class="fa-solid fa-bolt text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Product Description -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900 mb-2">Description & Details</h3>
                    <div class="text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                        <?php echo e($product->description ?: 'No detailed description provided for this item.'); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(isset($relatedProducts) && $relatedProducts->isNotEmpty()): ?>
        <div class="mt-12">
            <h2 class="text-xl font-extrabold text-gray-900 mb-6">You May Also Like</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-2xl border border-gray-200 hover:shadow-md transition-all p-3 flex flex-col">
                        <a href="<?php echo e(route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $rel->slug])); ?>" class="aspect-square bg-gray-100 rounded-xl overflow-hidden block mb-2">
                            <img src="<?php echo e($rel->image_url); ?>" alt="<?php echo e($rel->name); ?>" class="w-full h-full object-cover">
                        </a>
                        <a href="<?php echo e(route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $rel->slug])); ?>" class="text-xs font-bold text-gray-900 truncate hover:text-brand-custom">
                            <?php echo e($rel->name); ?>

                        </a>
                        <div class="mt-1 text-xs font-extrabold text-gray-900">₹<?php echo e(number_format($rel->price, 2)); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    const currentProduct = <?php echo json_encode($product, 15, 512) ?>;

    function changeDetailQty(delta) {
        let input = document.getElementById('detail-qty');
        let val = parseInt(input.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        input.value = val;
    }

    function addDetailToBag() {
        let qty = parseInt(document.getElementById('detail-qty').value) || 1;
        addToCart(currentProduct, qty, true);
    }

    function buyNowDirect() {
        let qty = parseInt(document.getElementById('detail-qty').value) || 1;
        addToCart(currentProduct, qty, false);
        window.location.href = "<?php echo e(route('minisite.checkout', $sellerPage->slug)); ?>";
    }

    function orderThisOnWhatsapp() {
        if (!WHATSAPP_NUM) {
            alert('WhatsApp number not set.');
            return;
        }
        let qty = parseInt(document.getElementById('detail-qty').value) || 1;
        let total = currentProduct.price * qty;
        let text = `*New Order Inquiry from ${STORE_NAME}*\n\n` +
                   `🛍️ *Product:* ${currentProduct.name}\n` +
                   `🔢 *Quantity:* ${qty}\n` +
                   `💰 *Unit Price:* ₹${currentProduct.price}\n` +
                   `💵 *Total Amount:* ₹${total.toFixed(2)}\n` +
                   `🔗 *Link:* ${window.location.href}\n\n` +
                   `Please share payment and dispatch information.`;

        const url = `https://wa.me/${WHATSAPP_NUM}?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
    }
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller-site.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller-site/product_detail.blade.php ENDPATH**/ ?>