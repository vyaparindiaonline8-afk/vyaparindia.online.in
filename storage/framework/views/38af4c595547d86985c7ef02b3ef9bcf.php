<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Slip & Invoice - #<?php echo e($dsOrder->ds_order_number); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background-color: white !important; font-size: 12px; }
            .print-border { border: 1px solid #000 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased p-4 sm:p-8">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-3xl shadow-sm border border-gray-200 print-border space-y-6">
        <!-- Print Trigger -->
        <div class="no-print flex items-center justify-between pb-4 border-b border-gray-100">
            <span class="text-xs text-gray-500 font-semibold">White-label Packaging Slip & Tax Invoice</span>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 flex items-center gap-2">
                <span>🖨️ Print Packing Slip</span>
            </button>
        </div>

        <!-- Header: Dropshipper Brand Header -->
        <div class="flex items-start justify-between pb-6 border-b-2 border-gray-900">
            <div>
                <h1 class="text-2xl font-black uppercase tracking-tight text-gray-900">
                    <?php echo e($dsOrder->dropshipper->sellerPage->page_title ?? $dsOrder->dropshipper->name); ?>

                </h1>
                <p class="text-xs text-gray-500 mt-1">
                    <?php echo e($dsOrder->dropshipper->sellerPage->tagline ?? 'Verified Direct Storefront'); ?>

                </p>
                <p class="text-xs text-gray-500">
                    Support: <?php echo e($dsOrder->dropshipper->sellerPage->support_phone ?? $dsOrder->dropshipper->email); ?>

                </p>
            </div>

            <div class="text-right">
                <div class="text-xs font-mono font-bold text-gray-400">PACKING SLIP / INVOICE</div>
                <div class="text-base font-mono font-black text-gray-900 mt-1">#<?php echo e($dsOrder->ds_order_number); ?></div>
                <div class="text-xs text-gray-500 mt-1">Date: <?php echo e($dsOrder->created_at->format('d M Y')); ?></div>
                <div class="mt-2 inline-block px-3 py-1 bg-gray-900 text-white text-[11px] font-black uppercase rounded">
                    Mode: <?php echo e(strtoupper($dsOrder->payment_collection_mode)); ?>

                </div>
            </div>
        </div>

        <!-- Shipping Label Address Box -->
        <div class="grid grid-cols-2 gap-6 p-4 rounded-2xl bg-gray-50 border border-gray-200 text-xs">
            <div>
                <div class="text-[10px] font-bold uppercase text-gray-400 mb-1">SHIP TO (CUSTOMER):</div>
                <div class="font-extrabold text-sm text-gray-900"><?php echo e($dsOrder->order->customer_name); ?></div>
                <div class="text-gray-700 mt-1 leading-relaxed"><?php echo e($dsOrder->order->shipping_address); ?></div>
                <div class="font-bold text-gray-900 mt-1"><?php echo e($dsOrder->order->city); ?> - <?php echo e($dsOrder->order->pincode); ?></div>
                <div class="font-bold text-gray-900 mt-1">Phone: <?php echo e($dsOrder->order->customer_phone); ?></div>
            </div>

            <div>
                <div class="text-[10px] font-bold uppercase text-gray-400 mb-1">COURIER DISPATCH DETAILS:</div>
                <div class="font-bold text-gray-800">Partner: <?php echo e($dsOrder->courier_partner ?: 'Express Logistics'); ?></div>
                <div class="font-mono font-bold text-gray-900 mt-1">AWB: <?php echo e($dsOrder->awb_number ?: 'PENDING'); ?></div>
                <div class="text-gray-500 mt-2 text-[11px]">
                    Note: If COD, courier will collect exact amount of <strong>₹<?php echo e(number_format($dsOrder->customer_retail_total, 2)); ?></strong> upon delivery.
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div>
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 text-[11px] uppercase font-bold text-gray-700">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Item Description</th>
                        <th class="p-3 text-center">Qty</th>
                        <th class="p-3 text-right">Price</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $dsOrder->order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="p-3 text-gray-400"><?php echo e($idx + 1); ?></td>
                            <td class="p-3 font-bold text-gray-900"><?php echo e($p->name); ?></td>
                            <td class="p-3 text-center font-bold"><?php echo e($p->pivot->quantity); ?></td>
                            <td class="p-3 text-right">₹<?php echo e(number_format($p->price, 2)); ?></td>
                            <td class="p-3 text-right font-bold">₹<?php echo e(number_format($p->price * $p->pivot->quantity, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="border-t-2 border-gray-900 text-xs font-bold">
                    <tr>
                        <td colspan="4" class="p-3 text-right">Delivery / Shipping:</td>
                        <td class="p-3 text-right text-emerald-600">FREE</td>
                    </tr>
                    <tr class="text-sm font-black">
                        <td colspan="4" class="p-3 text-right">Total Invoice Amount:</td>
                        <td class="p-3 text-right">₹<?php echo e(number_format($dsOrder->customer_retail_total, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Footer Notice -->
        <div class="pt-6 border-t border-gray-200 text-center text-[11px] text-gray-500">
            Thank you for shopping with <strong><?php echo e($dsOrder->dropshipper->sellerPage->page_title ?? $dsOrder->dropshipper->name); ?></strong>! For support & warranty, please contact support details above.
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/wholesaler/invoice.blade.php ENDPATH**/ ?>