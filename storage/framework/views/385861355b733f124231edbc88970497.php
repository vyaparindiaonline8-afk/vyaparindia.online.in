<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesaler Supplier Fulfillment - VyaparIndia</title>
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
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Wholesaler Fulfillment Console</h1>
                        <p class="text-xs text-gray-500">Manage dropshipper orders, adjust shipping & rates, dispatch couriers, & reconcile COD</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dashboard')); ?>" class="px-3.5 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-xs">
                        Dashboard
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
                <i class="fa-solid fa-inbox text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-base font-bold text-gray-800">No dropship fulfillment requests</h3>
                <p class="text-xs text-gray-400 mt-1">When dropshippers receive sales for your products, packing requests will show up here.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php $__currentLoopData = $dropshipOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
                        <!-- Top Order Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100">
                            <div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-black text-base text-gray-900">#<?php echo e($dso->ds_order_number); ?></span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?php echo e($dso->fulfillment_status === 'delivered' ? 'bg-emerald-50 text-emerald-700' : ($dso->fulfillment_status === 'dispatched' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700')); ?>">
                                        <?php echo e(str_replace('_', ' ', $dso->fulfillment_status)); ?>

                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Dropshipper Store: <strong><?php echo e($dso->dropshipper->sellerPage->page_title ?? $dso->dropshipper->name); ?></strong>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('seller.wholesaler.invoice', $dso->id)); ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-print"></i>
                                    <span>White-label Packing Slip</span>
                                </a>
                            </div>
                        </div>

                        <!-- 3 Column Info: Customer Address, Items, Financials -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                            <!-- Customer Address -->
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-1.5">
                                <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-tag text-blue-600"></i>
                                    <span>End Customer Shipping Details</span>
                                </div>
                                <div class="font-semibold text-gray-800"><?php echo e($dso->order->customer_name); ?></div>
                                <div class="text-gray-500"><?php echo e($dso->order->customer_phone); ?></div>
                                <div class="text-gray-600 leading-relaxed"><?php echo e($dso->order->shipping_address); ?>, <?php echo e($dso->order->city); ?> - <?php echo e($dso->order->pincode); ?></div>
                            </div>

                            <!-- Ordered Items -->
                            <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                                <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-box text-purple-600"></i>
                                    <span>Order Items</span>
                                </div>
                                <?php $__currentLoopData = $dso->order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex justify-between items-center text-gray-700">
                                        <span class="truncate max-w-[140px]"><?php echo e($p->name); ?></span>
                                        <span class="font-bold">x<?php echo e($p->pivot->quantity); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div class="pt-2 border-t border-gray-200 flex justify-between font-bold text-gray-900">
                                    <span>Customer Retail Total:</span>
                                    <span>₹<?php echo e(number_format($dso->customer_retail_total, 2)); ?></span>
                                </div>
                            </div>

                            <!-- Supplier Financials & Pricing -->
                            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
                                <div class="font-bold text-emerald-900 text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-calculator text-emerald-600"></i>
                                    <span>Wholesaler Cost & Payout</span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Base Wholesale Cost:</span>
                                    <span class="font-bold">₹<?php echo e(number_format($dso->supplier_base_cost, 2)); ?></span>
                                </div>
                                <div class="flex justify-between text-gray-700">
                                    <span>Shipping Charge Added:</span>
                                    <span class="font-bold">₹<?php echo e(number_format($dso->shipping_cost, 2)); ?></span>
                                </div>
                                <div class="pt-1.5 border-t border-emerald-200 flex justify-between font-black text-gray-900 text-sm">
                                    <span>Total Payable to You:</span>
                                    <span class="text-emerald-700">₹<?php echo e(number_format($dso->total_supplier_payable, 2)); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Wholesaler Actions Section -->
                        <div class="pt-4 border-t border-gray-100 space-y-4">
                            <!-- 1. Price Adjustment Form (if order not yet dispatched) -->
                            <?php if(in_array($dso->fulfillment_status, ['pending_wholesaler_review', 'awaiting_dropshipper_approval', 'ready_to_pack'])): ?>
                                <details class="bg-gray-50 rounded-2xl p-4 border border-gray-200 group">
                                    <summary class="font-bold text-xs text-gray-800 cursor-pointer flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="fa-solid fa-sliders text-blue-600"></i>
                                            <span>Adjust Rate (Low Volume) / Add Shipping Fee</span>
                                        </span>
                                        <i class="fa-solid fa-chevron-down text-gray-400 group-open:rotate-180 transition-transform"></i>
                                    </summary>

                                    <form action="<?php echo e(route('seller.wholesaler.adjustPricing', $dso->id)); ?>" method="POST" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                        <?php echo csrf_field(); ?>
                                        <div>
                                            <label class="block font-bold text-gray-700 mb-1">Base Product Cost (₹)</label>
                                            <input type="number" step="0.01" name="supplier_base_cost" value="<?php echo e($dso->supplier_base_cost); ?>" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-gray-700 mb-1">Actual Shipping Fee (₹)</label>
                                            <input type="number" step="0.01" name="shipping_cost" value="<?php echo e($dso->shipping_cost); ?>" required class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl font-bold">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block font-bold text-gray-700 mb-1">Adjustment Reason / Note for Dropshipper</label>
                                            <input type="text" name="wholesaler_adjustment_note" value="<?php echo e($dso->wholesaler_adjustment_note); ?>" placeholder="e.g. Volume is 1 piece, adjusted base rate to ₹320 + ₹70 courier charge." class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl">
                                        </div>
                                        <div class="sm:col-span-3">
                                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors">
                                                Send Updated Quote to Dropshipper
                                            </button>
                                        </div>
                                    </form>
                                </details>
                            <?php endif; ?>

                            <!-- 2. Dispatch / Tracking Inputs -->
                            <div class="flex flex-wrap items-center gap-3">
                                <?php if($dso->fulfillment_status === 'pending_wholesaler_review' || $dso->fulfillment_status === 'ready_to_pack'): ?>
                                    <button onclick="openDispatchModal(<?php echo e($dso->id); ?>, '<?php echo e($dso->ds_order_number); ?>')" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                                        <i class="fa-solid fa-truck-fast"></i>
                                        <span>Dispatch Order & Add Courier AWB</span>
                                    </button>
                                <?php elseif($dso->fulfillment_status === 'dispatched'): ?>
                                    <div class="flex items-center gap-2 bg-blue-50 px-3.5 py-2 rounded-xl border border-blue-200 text-xs text-blue-900">
                                        <i class="fa-solid fa-truck text-blue-600"></i>
                                        <span><strong><?php echo e($dso->courier_partner); ?></strong> (AWB: <?php echo e($dso->awb_number); ?>)</span>
                                        <?php if($dso->tracking_url): ?>
                                            <a href="<?php echo e($dso->tracking_url); ?>" target="_blank" class="underline font-bold ml-2">Track &rarr;</a>
                                        <?php endif; ?>
                                    </div>

                                    <form action="<?php echo e(route('seller.wholesaler.markDelivered', $dso->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition-colors">
                                            ✓ Mark Delivered & Settle Profit
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- COD Status Update -->
                                <form action="<?php echo e(route('seller.wholesaler.updateCodStatus', $dso->id)); ?>" method="POST" class="flex items-center gap-2 ml-auto text-xs">
                                    <?php echo csrf_field(); ?>
                                    <span class="text-gray-500 font-semibold">COD Status:</span>
                                    <select name="cod_remittance_status" onchange="this.form.submit()" class="px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-xl text-xs font-bold text-gray-800">
                                        <option value="pending" <?php echo e($dso->cod_remittance_status === 'pending' ? 'selected' : ''); ?>>Pending Collection</option>
                                        <option value="collected_by_courier" <?php echo e($dso->cod_remittance_status === 'collected_by_courier' ? 'selected' : ''); ?>>Collected by Courier</option>
                                        <option value="remitted_to_dropshipper" <?php echo e($dso->cod_remittance_status === 'remitted_to_dropshipper' ? 'selected' : ''); ?>>Remitted to Dropshipper</option>
                                    </select>
                                </form>
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

    <!-- Dispatch Modal -->
    <div id="dispatch-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeDispatchModal()"></div>

            <div class="inline-block bg-white rounded-3xl p-6 sm:p-8 text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full relative z-10 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-emerald-600"></i>
                        <span>Enter Courier Tracking Info</span>
                    </h3>
                    <button onclick="closeDispatchModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form id="dispatch-form" method="POST" class="space-y-4 text-xs">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Courier Partner Name <span class="text-red-500">*</span></label>
                        <select name="courier_partner" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none">
                            <option value="Delhivery">Delhivery</option>
                            <option value="BlueDart">BlueDart</option>
                            <option value="DTDC">DTDC</option>
                            <option value="Shadowfax">Shadowfax</option>
                            <option value="Ekart">Ekart</option>
                            <option value="Shiprocket">Shiprocket</option>
                            <option value="India Post">India Post Speed Post</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">AWB / Consignment Tracking Number <span class="text-red-500">*</span></label>
                        <input type="text" name="awb_number" required placeholder="e.g. 1423891029381" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Tracking URL (Optional)</label>
                        <input type="url" name="tracking_url" placeholder="https://track.courier.com/awb" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>

                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition-all">
                        Confirm Dispatch & Notify Dropshipper
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDispatchModal(orderId, orderNum) {
            let form = document.getElementById('dispatch-form');
            form.action = '/seller/wholesaler/orders/' + orderId + '/dispatch';
            document.getElementById('dispatch-modal').classList.remove('hidden');
        }

        function closeDispatchModal() {
            document.getElementById('dispatch-modal').classList.add('hidden');
        }
    </script>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/wholesaler/orders.blade.php ENDPATH**/ ?>