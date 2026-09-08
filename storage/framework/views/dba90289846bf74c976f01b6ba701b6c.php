<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropship Fulfillment Orders - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dashboard')); ?>" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Dropship Order Fulfillment</h1>
                        <p class="text-xs text-gray-500">Track supplier fulfillment, approve pricing adjustments, & track courier AWB</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dropship.wallet')); ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-wallet"></i>
                        <span>My Profit Wallet</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <?php if(session('success')): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark text-red-600 text-base"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($dropshipOrders->isEmpty()): ?>
            <div class="bg-white rounded-3xl p-16 text-center border border-gray-200">
                <i class="fa-solid fa-truck-ramp-box text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-base font-bold text-gray-800">No dropship orders received yet</h3>
                <p class="text-xs text-gray-400 mt-1">When buyers order imported products from your mini-site, fulfillment orders will appear here.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $dropshipOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-black text-sm text-gray-900">#<?php echo e($dso->ds_order_number); ?></span>
                                <span class="text-xs text-gray-400">Parent Order: #<?php echo e($dso->order->order_number ?? $dso->order_id); ?></span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?php echo e($dso->fulfillment_status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : ($dso->fulfillment_status === 'dispatched' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700')); ?>">
                                    <?php echo e(str_replace('_', ' ', $dso->fulfillment_status)); ?>

                                </span>
                            </div>

                            <div class="text-xs text-gray-500">
                                <span>Wholesaler:</span>
                                <strong class="text-gray-900"><?php echo e($dso->wholesaler->name); ?></strong>
                            </div>
                        </div>

                        <!-- Financials & Breakdown -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 text-xs">
                            <div>
                                <span class="text-gray-500">Customer Retail Paid:</span>
                                <div class="font-black text-gray-900 text-sm mt-0.5">₹<?php echo e(number_format($dso->customer_retail_total, 2)); ?></div>
                            </div>
                            <div>
                                <span class="text-gray-500">Wholesale Base Cost:</span>
                                <div class="font-bold text-gray-800 text-sm mt-0.5">₹<?php echo e(number_format($dso->supplier_base_cost, 2)); ?></div>
                            </div>
                            <div>
                                <span class="text-gray-500">Shipping Cost:</span>
                                <div class="font-bold text-gray-800 text-sm mt-0.5">₹<?php echo e(number_format($dso->shipping_cost, 2)); ?></div>
                            </div>
                            <div>
                                <span class="text-gray-500">Your Net Profit:</span>
                                <div class="font-black text-emerald-600 text-base mt-0.5">+₹<?php echo e(number_format($dso->dropshipper_profit, 2)); ?></div>
                            </div>
                        </div>

                        <!-- Price Adjustment Alert for Dropshipper -->
                        <?php if($dso->price_adjusted_by_wholesaler && $dso->dropshipper_approval_status === 'pending_approval'): ?>
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 space-y-3">
                                <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                    <span>Wholesaler has updated Pricing / Added Shipping Fee</span>
                                </div>
                                <p class="text-xs text-amber-800">
                                    <strong>Wholesaler Note:</strong> <?php echo e($dso->wholesaler_adjustment_note ?: 'Pricing adjusted for order volume & courier charge.'); ?>

                                </p>
                                <div class="flex items-center gap-3 pt-1">
                                    <form action="<?php echo e(route('seller.dropship.approvePrice', $dso->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors">
                                            ✓ Approve Updated Cost & Proceed
                                        </button>
                                    </form>
                                    <form action="<?php echo e(route('seller.dropship.rejectPrice', $dso->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-colors">
                                            ✕ Reject & Cancel Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Courier Tracking & COD Section -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 text-xs">
                            <!-- Tracking -->
                            <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white border border-gray-200">
                                <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-location-crosshairs text-base"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">
                                        <?php echo e($dso->courier_partner ? $dso->courier_partner . ' Tracking' : 'Awaiting Courier Dispatch'); ?>

                                    </div>
                                    <?php if($dso->awb_number): ?>
                                        <div class="text-gray-500 font-mono mt-0.5">AWB: <?php echo e($dso->awb_number); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php if($dso->tracking_url): ?>
                                    <a href="<?php echo e($dso->tracking_url); ?>" target="_blank" class="ml-auto px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold text-[11px] hover:bg-blue-700">
                                        Live Track &rarr;
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- COD Status -->
                            <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white border border-gray-200">
                                <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-hand-holding-dollar text-base"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">Payment Mode: <?php echo e(strtoupper($dso->payment_collection_mode)); ?></div>
                                    <div class="text-gray-500 mt-0.5">
                                        Status: <strong class="text-gray-800"><?php echo e(ucfirst(str_replace('_', ' ', $dso->cod_remittance_status))); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div>
                <?php echo e($dropshipOrders->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/dropship/orders.blade.php ENDPATH**/ ?>