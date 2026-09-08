<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Logistics & Anti-RTO Suite - VyaparIndia</title>
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
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Smart Shipping & Anti-RTO Suite</h1>
                        <p class="text-xs text-gray-500">Multi-carrier rate comparison, automated COD verification & bulk thermal dispatch labels</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.shipping.thermalLabels')); ?>" target="_blank" class="px-4 py-2 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-barcode"></i>
                        <span>1-Click Thermal Labels (4x6)</span>
                    </a>
                    <a href="<?php echo e(route('seller.shipping.manifest')); ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs">
                        <i class="fa-solid fa-file-lines mr-1"></i> Courier Manifest
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <?php if(session('success')): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <!-- Anti-RTO Shield & Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total COD Orders</span>
                <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-1"><?php echo e($totalCodOrders); ?></div>
                <div class="text-[11px] text-gray-400 mt-1">Pending verification</div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Verified COD</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1"><?php echo e($verifiedCodOrders); ?></div>
                <div class="text-[11px] text-gray-400 mt-1">Confirmed on WhatsApp</div>
            </div>

            <div class="bg-gradient-to-br from-purple-700 to-indigo-700 rounded-3xl p-6 text-white shadow-lg">
                <span class="text-xs font-bold text-purple-200 uppercase tracking-wider">Converted to Prepaid</span>
                <div class="text-2xl sm:text-3xl font-black mt-1"><?php echo e($convertedPrepaidOrders); ?></div>
                <div class="text-[11px] text-purple-200 mt-1">⚡ Zero RTO Risk & ₹40 Saved</div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Unverified / Timeout</span>
                <div class="text-2xl sm:text-3xl font-black text-amber-600 mt-1"><?php echo e($unverifiedCodOrders); ?></div>
                <div class="text-[11px] text-gray-400 mt-1">Awaiting 4-6 hr response</div>
            </div>
        </div>

        <!-- Multi-Carrier Smart Courier Rate Estimator -->
        <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-blue-600"></i>
                        <span>Smart Multi-Carrier Rate Comparator (Delhivery vs BlueDart vs Shadowfax vs DTDC)</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Calculates lowest freight + COD charges in real time</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <?php $__currentLoopData = $sampleRates['all_rates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 rounded-2xl border <?php echo e($loop->first ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-400' : 'border-gray-200 bg-gray-50'); ?> space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-xs text-gray-900 flex items-center gap-1.5">
                                <i class="<?php echo e($rate['icon']); ?> text-blue-600"></i>
                                <span><?php echo e($rate['courier_name']); ?></span>
                            </span>
                            <?php if($loop->first): ?>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500 text-white font-black text-[9px] uppercase">
                                    Cheapest
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-baseline justify-between text-xs">
                            <span class="text-gray-500">Delivery SLA:</span>
                            <span class="font-bold text-gray-800"><?php echo e($rate['sla_days']); ?></span>
                        </div>

                        <div class="pt-2 border-t border-gray-200 flex items-baseline justify-between">
                            <span class="text-xs text-gray-500">Total Rate:</span>
                            <span class="text-base font-black text-gray-900">₹<?php echo e(number_format($rate['total_rate'], 2)); ?></span>
                        </div>

                        <div class="text-[10px] text-emerald-700 bg-emerald-100/70 p-1.5 rounded-lg text-center font-bold">
                            💡 If Prepaid: Only ₹<?php echo e(number_format($rate['freight_charge'], 2)); ?> (Save ₹<?php echo e($rate['cod_charge']); ?>)
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Orders Table with Anti-RTO Status & 1-Click WhatsApp Trigger -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-base text-gray-900">Orders Dispatch & Verification Queue</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Verify COD orders before dispatch to eliminate fake orders</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('seller.shipping.thermalLabels')); ?>" target="_blank" class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800">
                        Print All Labels
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[11px] uppercase font-bold text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="p-4">Order</th>
                            <th class="p-4">Customer Details</th>
                            <th class="p-4">Total Amount</th>
                            <th class="p-4">Payment & Verification</th>
                            <th class="p-4">RTO Risk</th>
                            <th class="p-4 text-right">Anti-RTO Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="p-4">
                                    <div class="font-mono font-bold text-gray-900">#<?php echo e($ord->order_number ?: $ord->id); ?></div>
                                    <div class="text-[11px] text-gray-400"><?php echo e($ord->created_at->format('d M, h:i A')); ?></div>
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-900"><?php echo e($ord->customer_name); ?></div>
                                    <div class="text-[11px] text-gray-500"><?php echo e($ord->customer_phone); ?></div>
                                    <div class="text-[11px] text-gray-400 truncate max-w-xs"><?php echo e($ord->city); ?> - <?php echo e($ord->pincode); ?></div>
                                </td>
                                <td class="p-4 font-black text-gray-900 text-sm">₹<?php echo e(number_format($ord->total_price, 2)); ?></td>
                                <td class="p-4">
                                    <?php if($ord->cod_verification_status === 'converted_to_prepaid'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-800 border border-purple-200 flex items-center gap-1 w-fit">
                                            ⚡ PREPAID (₹50 DISCOUNT)
                                        </span>
                                    <?php elseif($ord->cod_verification_status === 'verified'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1 w-fit">
                                            ✓ COD VERIFIED
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1 w-fit">
                                            ⏳ UNVERIFIED COD
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase <?php echo e($ord->rto_risk_score === 'high' ? 'bg-red-50 text-red-700' : ($ord->rto_risk_score === 'medium' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700')); ?>">
                                        <?php echo e($ord->rto_risk_score ?? 'low'); ?> Risk
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if($ord->cod_verification_token): ?>
                                        <a href="<?php echo e(route('seller.shipping.sendVerification', $ord->id)); ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs inline-flex items-center gap-1">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                            <span>Send WhatsApp Verification</span>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-100">
                <?php echo e($orders->links()); ?>

            </div>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/shipping/dashboard.blade.php ENDPATH**/ ?>