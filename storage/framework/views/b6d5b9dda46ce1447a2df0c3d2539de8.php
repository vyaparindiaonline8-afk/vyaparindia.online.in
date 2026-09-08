<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropshipper Profit Wallet - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dashboard')); ?>" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Dropshipper Earnings & Wallet</h1>
                        <p class="text-xs text-gray-500">Track profits from your mini-site sales and request bank payouts</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?php echo e(route('seller.dropship.orders')); ?>" class="px-3.5 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold text-xs">
                        View Orders
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Balance Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Available Balance -->
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-6 text-white shadow-lg space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Available Wallet Balance</span>
                <div class="text-3xl font-black">₹<?php echo e(number_format($wallet->balance, 2)); ?></div>
                <p class="text-xs text-emerald-100 pt-2">Ready for withdrawal to registered bank account</p>
            </div>

            <!-- Total Earned -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs space-y-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Lifetime Earnings</span>
                <div class="text-3xl font-black text-gray-900">₹<?php echo e(number_format($wallet->total_earned, 2)); ?></div>
                <p class="text-xs text-gray-400 pt-2">Accumulated net margins across all delivered orders</p>
            </div>

            <!-- Total Withdrawn -->
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs space-y-2">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Payouts Settled</span>
                <div class="text-3xl font-black text-blue-600">₹<?php echo e(number_format($wallet->total_withdrawn, 2)); ?></div>
                <p class="text-xs text-gray-400 pt-2">Transferred to bank / UPI</p>
            </div>
        </div>

        <!-- Ledger Transactions -->
        <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="font-extrabold text-base text-gray-900">Wallet Transaction History</h3>
                <span class="text-xs text-gray-500 font-semibold"><?php echo e($transactions->total()); ?> Transactions</span>
            </div>

            <?php if($transactions->isEmpty()): ?>
                <div class="p-12 text-center text-gray-400">
                    <i class="fa-solid fa-receipt text-4xl mb-3 text-gray-300"></i>
                    <p class="text-xs font-bold text-gray-700">No transactions recorded yet</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">When orders are delivered, profits are automatically credited here.</p>
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100 text-xs">
                    <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl <?php echo e($tx->type === 'credit' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'); ?> flex items-center justify-center font-bold">
                                    <i class="fa-solid <?php echo e($tx->type === 'credit' ? 'fa-arrow-down-left' : 'fa-arrow-up-right'); ?>"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900"><?php echo e($tx->description); ?></div>
                                    <div class="text-gray-400 text-[11px] mt-0.5"><?php echo e($tx->created_at->format('d M Y, h:i A')); ?></div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-extrabold text-sm <?php echo e($tx->type === 'credit' ? 'text-emerald-600' : 'text-red-600'); ?>">
                                    <?php echo e($tx->type === 'credit' ? '+' : '-'); ?>₹<?php echo e(number_format($tx->amount, 2)); ?>

                                </div>
                                <span class="text-[10px] uppercase font-bold text-gray-400">Completed</span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div>
                    <?php echo e($transactions->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/seller/dropship/wallet.blade.php ENDPATH**/ ?>