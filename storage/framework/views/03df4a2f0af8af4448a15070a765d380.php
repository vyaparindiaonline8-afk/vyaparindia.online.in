<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesale Dropship Hub - VyaparIndia</title>
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
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Wholesale Dropshipping Hub</h1>
                        <p class="text-xs text-gray-500">Source verified factory products & push to your mini-site in 1-Click</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="openBulkModal()" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>1-Click Bulk Import All</span>
                    </button>
                    <a href="<?php echo e(route('seller.dropship.myProducts')); ?>" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs">
                        My Imported Products
                    </a>
                    <a href="<?php echo e(route('seller.dropship.orders')); ?>" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs">
                        Dropship Orders
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Alerts -->
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

        <!-- Filter & Search Bar -->
        <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs">
            <form action="<?php echo e(route('seller.dropship.hub')); ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="sm:col-span-2 relative">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search products or manufacturers..." class="w-full pl-10 pr-4 py-2.5 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-xs"></i>
                </div>

                <div>
                    <select name="wholesaler_id" class="w-full px-3 py-2.5 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="">All Wholesalers / Suppliers</option>
                        <?php $__currentLoopData = $wholesalers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ws->id); ?>" <?php echo e(request('wholesaler_id') == $ws->id ? 'selected' : ''); ?>>
                                <?php echo e($ws->name); ?> (<?php echo e($ws->sellerProfile->city ?? 'Verified'); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select name="category_id" class="w-full px-3 py-2.5 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <option value="">All Categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 shrink-0">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-extrabold text-gray-900">Available Wholesale Items</h2>
                <span class="text-xs text-gray-500 font-semibold"><?php echo e($products->total()); ?> Products Found</span>
            </div>

            <?php if($products->isEmpty()): ?>
                <div class="bg-white rounded-3xl p-16 text-center border border-gray-200">
                    <i class="fa-solid fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-base font-bold text-gray-800">No wholesale products available</h3>
                    <p class="text-xs text-gray-400 mt-1">Other registered sellers will list wholesale inventory soon.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isImported = in_array($p->id, $importedProductIds);
                        ?>
                        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs hover:shadow-md transition-all flex flex-col overflow-hidden">
                            <!-- Image -->
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                <img src="<?php echo e($p->image_url); ?>" alt="<?php echo e($p->name); ?>" class="w-full h-full object-cover">
                                <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-1 rounded-md">
                                    Wholesale: ₹<?php echo e(number_format($p->price, 2)); ?>

                                </div>
                                <?php if($isImported): ?>
                                    <div class="absolute top-3 right-3 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">
                                        ✓ Imported
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Info -->
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="text-[11px] text-gray-400 font-semibold truncate">
                                        <i class="fa-solid fa-building text-[10px] mr-1"></i> <?php echo e($p->seller->name); ?>

                                    </div>
                                    <h3 class="font-bold text-xs text-gray-900 line-clamp-2 mt-1"><?php echo e($p->name); ?></h3>
                                </div>

                                <div class="pt-3 border-t border-gray-100">
                                    <div class="flex items-center justify-between text-xs mb-3">
                                        <span class="text-gray-500">Base Cost:</span>
                                        <span class="font-extrabold text-gray-900">₹<?php echo e(number_format($p->price, 2)); ?></span>
                                    </div>

                                    <button onclick='openSingleModal(<?php echo json_encode($p, 15, 512) ?>)' class="w-full py-2.5 px-4 rounded-xl <?php echo e($isImported ? "bg-emerald-50 text-emerald-700 border border-emerald-300 font-bold" : "bg-purple-600 hover:bg-purple-700 text-white font-bold"); ?> text-xs shadow-xs transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid <?php echo e($isImported ? "fa-pen-to-square" : "fa-plus"); ?>"></i>
                                        <span><?php echo e($isImported ? "Edit Retail Price" : "1-Click Add to Store"); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-8">
                    <?php echo e($products->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Single Product Import Modal -->
    <div id="single-import-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeSingleModal()"></div>

            <div class="inline-block bg-white rounded-3xl p-6 sm:p-8 text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full relative z-10 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-store text-purple-600"></i>
                        <span>Push Product to Your Mini-Site</span>
                    </h3>
                    <button onclick="closeSingleModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="<?php echo e(route('seller.dropship.importSingle')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="supplier_product_id" id="modal-product-id">

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Product Title (Display in your store)</label>
                        <input type="text" name="custom_name" id="modal-product-name" required class="w-full px-4 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4 bg-purple-50 p-4 rounded-2xl border border-purple-100 text-xs">
                        <div>
                            <span class="text-purple-700 font-semibold">Wholesale Base Cost:</span>
                            <div class="text-lg font-black text-purple-950 mt-0.5" id="modal-wholesale-price">₹0.00</div>
                        </div>
                        <div>
                            <span class="text-purple-700 font-semibold">Your Profit Margin:</span>
                            <div class="text-lg font-black text-emerald-600 mt-0.5" id="modal-profit-margin">₹0.00</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Your Selling Retail Price (₹) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="retail_price" id="modal-retail-price" oninput="calculateMargin()" required class="w-full px-4 py-2.5 text-sm font-bold bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        <p class="text-[11px] text-gray-400 mt-1">Set the price customers will pay on your mini-site.</p>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Publish to My Store Catalog</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Import All Modal -->
    <div id="bulk-import-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeBulkModal()"></div>

            <div class="inline-block bg-white rounded-3xl p-6 sm:p-8 text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full relative z-10 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-extrabold text-base text-gray-900 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-purple-600"></i>
                        <span>1-Click Bulk Import All Products</span>
                    </h3>
                    <button onclick="closeBulkModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="<?php echo e(route('seller.dropship.importBulk')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Select Wholesaler / Manufacturer <span class="text-red-500">*</span></label>
                        <select name="wholesaler_id" required class="w-full px-4 py-2.5 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">-- Choose Wholesaler --</option>
                            <?php $__currentLoopData = $wholesalers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ws->id); ?>"><?php echo e($ws->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Select Category (Optional)</label>
                        <select name="category_id" class="w-full px-4 py-2.5 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">All Categories (Entire Catalog)</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Markup Type</label>
                            <select name="margin_type" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none">
                                <option value="percentage">Percentage (+ %)</option>
                                <option value="fixed">Fixed Amount (+ ₹)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Margin Value</label>
                            <input type="number" name="margin_value" value="30" min="1" required class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-purple-500 focus:outline-none font-bold">
                        </div>
                    </div>

                    <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200 text-[11px] text-gray-500">
                        ⚡ Example: If wholesale price is ₹500 and markup is 30%, selling price will automatically be set to ₹650 (₹150 profit per order).
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Import All Products in 1-Click</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentWholesale = 0;

        function openSingleModal(product) {
            currentWholesale = parseFloat(product.price);
            document.getElementById('modal-product-id').value = product.id;
            document.getElementById('modal-product-name').value = product.name;
            document.getElementById('modal-wholesale-price').innerText = '₹' + currentWholesale.toFixed(2);
            
            // Suggest 35% margin
            let suggestedRetail = Math.round(currentWholesale * 1.35);
            document.getElementById('modal-retail-price').value = suggestedRetail;
            calculateMargin();

            document.getElementById('single-import-modal').classList.remove('hidden');
        }

        function closeSingleModal() {
            document.getElementById('single-import-modal').classList.add('hidden');
        }

        function calculateMargin() {
            let retail = parseFloat(document.getElementById('modal-retail-price').value) || 0;
            let profit = retail - currentWholesale;
            let profitEl = document.getElementById('modal-profit-margin');
            if (profit > 0) {
                profitEl.innerText = '+₹' + profit.toFixed(2);
                profitEl.className = 'text-lg font-black text-emerald-600 mt-0.5';
            } else {
                profitEl.innerText = '₹' + profit.toFixed(2);
                profitEl.className = 'text-lg font-black text-red-600 mt-0.5';
            }
        }

        function openBulkModal() {
            document.getElementById('bulk-import-modal').classList.remove('hidden');
        }

        function closeBulkModal() {
            document.getElementById('bulk-import-modal').classList.add('hidden');
        }
    </script>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/dropship/hub.blade.php ENDPATH**/ ?>