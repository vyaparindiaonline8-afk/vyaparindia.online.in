<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Contact & Store Policies</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Get in touch directly with <?php echo e($sellerPage->page_title); ?></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Store Contact Details -->
        <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-store text-brand-custom"></i>
                <span>Store Information</span>
            </h2>

            <div class="space-y-4 text-xs sm:text-sm text-gray-700">
                <?php if($sellerPage->address): ?>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Registered Address</div>
                            <div class="text-gray-500 mt-0.5"><?php echo e($sellerPage->address); ?></div>
                            <div class="text-gray-500"><?php echo e($sellerPage->city); ?><?php echo e($sellerPage->pincode ? ' - ' . $sellerPage->pincode : ''); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($sellerPage->whatsapp_number): ?>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">WhatsApp Chat</div>
                            <a href="https://wa.me/<?php echo e($sellerPage->clean_whatsapp_number); ?>" target="_blank" class="text-emerald-600 font-semibold hover:underline mt-0.5 block">
                                +<?php echo e($sellerPage->clean_whatsapp_number); ?> (Direct Message)
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($sellerPage->support_phone): ?>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Support Phone</div>
                            <a href="tel:<?php echo e($sellerPage->support_phone); ?>" class="text-gray-600 hover:text-gray-900 mt-0.5 block">
                                <?php echo e($sellerPage->support_phone); ?>

                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($sellerPage->support_email): ?>
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">Official Email</div>
                            <a href="mailto:<?php echo e($sellerPage->support_email); ?>" class="text-gray-600 hover:text-gray-900 mt-0.5 block">
                                <?php echo e($sellerPage->support_email); ?>

                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- WhatsApp Big Action -->
            <?php if($sellerPage->whatsapp_number): ?>
                <div class="pt-4 border-t border-gray-100">
                    <a href="https://wa.me/<?php echo e($sellerPage->clean_whatsapp_number); ?>?text=Hello%20<?php echo e(urlencode($sellerPage->page_title)); ?>%2C%20I%20have%20an%20inquiry." target="_blank" class="w-full py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-base"></i>
                        <span>Start Instant WhatsApp Chat</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Store Policies (Shipping, Return, COD) -->
        <div id="policies" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-file-shield text-brand-custom"></i>
                <span>Store Policies & Guarantees</span>
            </h2>

            <div class="space-y-4 text-xs text-gray-600 leading-relaxed">
                <?php if($sellerPage->policies): ?>
                    <div class="whitespace-pre-line bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <?php echo e($sellerPage->policies); ?>

                    </div>
                <?php else: ?>
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3">
                        <div>
                            <h4 class="font-bold text-gray-900">📦 Shipping & Delivery Policy</h4>
                            <p class="text-gray-500 mt-1">Orders are packed and dispatched within 24-48 business hours. Pan-India shipping with real-time tracking.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">🔄 Return & Replacement</h4>
                            <p class="text-gray-500 mt-1">In case of damaged or defective products, please contact the seller on WhatsApp within 48 hours of delivery with unboxing proof.</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">💵 Payment Options</h4>
                            <p class="text-gray-500 mt-1">We accept Cash on Delivery (COD) and Online Payments (UPI, Cards, NetBanking).</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller-site.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller-site/contact.blade.php ENDPATH**/ ?>