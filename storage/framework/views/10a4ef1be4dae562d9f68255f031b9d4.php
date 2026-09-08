<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Header & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Product Catalog</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Showing all items available directly from <?php echo e($sellerPage->page_title); ?></p>
        </div>

        <!-- Search Input -->
        <form action="<?php echo e(route('minisite.products', $sellerPage->slug)); ?>" method="GET" class="flex items-center gap-2 max-w-md w-full">
            <?php if(request('category')): ?>
                <input type="hidden" name="category" value="<?php echo e(request('category')); ?>">
            <?php endif; ?>
            <div class="relative flex-1">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search by name or keyword..." class="w-full pl-10 pr-4 py-2 text-sm bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand focus:border-transparent shadow-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-gray-400 text-xs"></i>
            </div>
            <button type="submit" class="py-2 px-4 rounded-xl bg-gray-900 text-white font-semibold text-sm hover:bg-gray-800 transition-colors shadow-xs">
                Search
            </button>
            <?php if(request('search') || request('category')): ?>
                <a href="<?php echo e(route('minisite.products', $sellerPage->slug)); ?>" class="p-2 text-gray-400 hover:text-gray-700" title="Clear Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Category Filter Pills -->
    <?php if(isset($categories) && $categories->isNotEmpty()): ?>
        <div class="flex items-center gap-2 overflow-x-auto py-4 scrollbar-none">
            <a href="<?php echo e(route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('category', 'page')))); ?>" class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors <?php echo e(!request('category') ? 'bg-gray-900 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'); ?>">
                All Items
            </a>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('minisite.products', array_merge(['sellerPage' => $sellerPage->slug], request()->except('page'), ['category' => $cat->id]))); ?>" class="px-4 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors <?php echo e(request('category') == $cat->id ? 'bg-gray-900 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'); ?>">
                    <?php echo e($cat->name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <!-- Product Grid -->
    <div class="mt-6">
        <?php if($products->isEmpty()): ?>
            <div class="bg-white rounded-2xl p-16 text-center border border-gray-200 max-w-lg mx-auto my-8">
                <i class="fa-solid fa-magnifying-glass text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-900">No products found</h3>
                <p class="text-xs text-gray-500 mt-1">Try clearing your search query or choosing a different category.</p>
                <a href="<?php echo e(route('minisite.products', $sellerPage->slug)); ?>" class="mt-4 inline-block px-5 py-2 rounded-xl bg-gray-900 text-white font-semibold text-xs hover:bg-gray-800">
                    Reset All Filters
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-2xl border border-gray-200 hover:border-gray-300 hover:shadow-lg transition-all flex flex-col overflow-hidden group">
                        <!-- Product Image -->
                        <a href="<?php echo e(route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug])); ?>" class="relative block aspect-square bg-gray-100 overflow-hidden">
                            <img src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php if($product->category): ?>
                                <span class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-xs text-gray-800 text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs">
                                    <?php echo e($product->category->name); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Product Info -->
                        <div class="p-4 flex-1 flex flex-col">
                            <a href="<?php echo e(route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug])); ?>" class="font-bold text-xs sm:text-sm text-gray-900 hover:text-brand-custom line-clamp-2 transition-colors">
                                <?php echo e($product->name); ?>

                            </a>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-base font-extrabold text-gray-900">₹<?php echo e(number_format($product->price, 2)); ?></span>
                                <span class="text-xs text-gray-400 line-through">₹<?php echo e(number_format($product->price * 1.3, 2)); ?></span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 mt-auto">
                                <button onclick='addToCart(<?php echo json_encode($product, 15, 512) ?>)' class="py-2 px-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold text-xs transition-colors flex items-center justify-center gap-1.5" title="Add to Bag">
                                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                                    <span class="hidden sm:inline">Add</span>
                                </button>

                                <button onclick="buySingleOnWhatsapp('<?php echo e(addslashes($product->name)); ?>', '<?php echo e($product->price); ?>', '<?php echo e(route('minisite.product', ['sellerPage' => $sellerPage->slug, 'productSlug' => $product->slug])); ?>')" class="py-2 px-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs transition-colors flex items-center justify-center gap-1.5" title="Order via WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span class="hidden sm:inline">Order</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                <?php echo e($products->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller-site.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller-site/products.blade.php ENDPATH**/ ?>