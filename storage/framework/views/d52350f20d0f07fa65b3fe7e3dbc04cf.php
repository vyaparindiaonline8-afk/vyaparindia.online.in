<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Imported Dropship Products - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dropship.hub')); ?>" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">My Imported Dropship Inventory</h1>
                        <p class="text-xs text-gray-500">Products currently published to your mini-site from wholesale suppliers</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dropship.hub')); ?>" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Source More Products</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <?php if($importedProducts->isEmpty()): ?>
            <div class="bg-white rounded-3xl p-16 text-center border border-gray-200">
                <i class="fa-solid fa-boxes-stacked text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-base font-bold text-gray-800">You have not imported any products yet</h3>
                <p class="text-xs text-gray-400 mt-1">Browse our verified wholesale catalog and add products with 1-click.</p>
                <a href="<?php echo e(route('seller.dropship.hub')); ?>" class="mt-4 inline-block px-5 py-2.5 rounded-xl bg-purple-600 text-white font-bold text-xs">
                    Open Dropship Hub
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-[11px] uppercase font-bold text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="p-4">Product Details</th>
                            <th class="p-4">Wholesaler / Supplier</th>
                            <th class="p-4">Wholesale Cost</th>
                            <th class="p-4">Your Retail Price</th>
                            <th class="p-4">Profit Per Sale</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $importedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img src="<?php echo e($ip->supplierProduct->image_url ?? ''); ?>" class="h-12 w-12 object-cover rounded-xl border border-gray-100 shrink-0">
                                        <div>
                                            <div class="font-bold text-gray-900"><?php echo e($ip->custom_name ?: $ip->supplierProduct->name); ?></div>
                                            <div class="text-[11px] text-gray-400 mt-0.5"><?php echo e($ip->supplierProduct->category->name ?? 'General'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-gray-700">
                                    <div class="font-semibold"><?php echo e($ip->wholesaler->name); ?></div>
                                    <div class="text-[11px] text-gray-400"><?php echo e($ip->wholesaler->sellerProfile->city ?? 'Verified Supplier'); ?></div>
                                </td>
                                <td class="p-4 font-bold text-gray-600">₹<?php echo e(number_format($ip->wholesale_price, 2)); ?></td>
                                <td class="p-4 font-extrabold text-gray-900 text-sm">₹<?php echo e(number_format($ip->retail_price, 2)); ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +₹<?php echo e(number_format($ip->profit_margin, 2)); ?>

                                    </span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700">
                                        Live on Mini-Site
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div>
                <?php echo e($importedProducts->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/dropship/my_products.blade.php ENDPATH**/ ?>