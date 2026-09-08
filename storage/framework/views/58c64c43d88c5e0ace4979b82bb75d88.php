<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-8 sm:p-12 text-center">
        <!-- Success Icon -->
        <div class="h-20 w-20 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto text-4xl shadow-inner mb-6 animate-bounce">
            <i class="fa-solid fa-check"></i>
        </div>

        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
            Order Confirmed
        </span>

        <h1 class="text-3xl font-black text-gray-900 mt-3">Thank You for Your Order!</h1>
        <p class="text-sm text-gray-500 mt-2">
            Your order <strong class="text-gray-900 font-mono">#<?php echo e($order->order_number); ?></strong> has been placed with <strong><?php echo e($sellerPage->page_title); ?></strong>.
        </p>

        <!-- Order Snapshot Box -->
        <div class="mt-8 bg-gray-50 rounded-2xl p-6 text-left border border-gray-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Order Number:</span>
                <span class="font-mono font-bold text-gray-900"><?php echo e($order->order_number); ?></span>
            </div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Customer Name:</span>
                <span class="font-bold text-gray-900"><?php echo e($order->customer_name); ?></span>
            </div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Delivery Address:</span>
                <span class="font-semibold text-gray-800 text-right max-w-xs"><?php echo e($order->shipping_address); ?>, <?php echo e($order->city); ?> - <?php echo e($order->pincode); ?></span>
            </div>
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs">
                <span class="text-gray-500">Payment Mode:</span>
                <span class="font-bold text-gray-900 uppercase"><?php echo e($order->payment_method); ?> (<?php echo e($order->payment_status); ?>)</span>
            </div>
            <div class="flex items-center justify-between pt-1 text-sm font-extrabold text-gray-900">
                <span>Total Amount:</span>
                <span class="text-brand-custom text-lg">₹<?php echo e(number_format($order->total_price, 2)); ?></span>
            </div>
        </div>

        <!-- WhatsApp Confirmation Trigger -->
        <?php if($sellerPage->whatsapp_number): ?>
            <div class="mt-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center space-y-3">
                <p class="text-xs font-semibold text-emerald-900">
                    📲 Want faster updates? Send your order number to the seller directly on WhatsApp!
                </p>
                <a href="https://wa.me/<?php echo e($sellerPage->clean_whatsapp_number); ?>?text=Hi%20<?php echo e(urlencode($sellerPage->page_title)); ?>%2C%20I%20just%20placed%20order%20%23<?php echo e($order->order_number); ?>%20worth%20%E2%82%B9<?php echo e($order->total_price); ?>%20on%20your%20store.%20Please%20confirm%20and%20share%20tracking%20details." target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                    <i class="fa-brands fa-whatsapp text-base"></i>
                    <span>Confirm Order on WhatsApp</span>
                </a>
            </div>
        <?php endif; ?>

        <div class="mt-8 flex justify-center gap-4">
            <a href="<?php echo e(route('minisite.show', $sellerPage->slug)); ?>" class="px-6 py-3 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs transition-colors">
                Back to Storefront
            </a>
            <a href="<?php echo e(route('minisite.products', $sellerPage->slug)); ?>" class="px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller-site.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller-site/order_success.blade.php ENDPATH**/ ?>