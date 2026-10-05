<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | VyaparIndia - India's Smart B2B & D2C Business Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-gray-800 min-h-screen flex flex-col justify-between antialiased relative selection:bg-blue-600 selection:text-white">

    <!-- Ambient background light effects -->
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="w-full py-6 px-4 sm:px-8 relative z-10">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white font-black flex items-center justify-center text-xl shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-200">
                    V
                </div>
                <div>
                    <span class="font-extrabold text-white text-xl tracking-tight">Vyapar<span class="text-blue-400">India</span></span>
                    <span class="hidden sm:inline-block ml-2 text-[10px] font-bold text-blue-300 bg-blue-950/80 border border-blue-800/60 px-2 py-0.5 rounded-full">B2B & D2C Hub</span>
                </div>
            </a>
            
            <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-300 hover:text-white transition px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 backdrop-blur-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Marketplace</span>
            </a>
        </div>
    </header>

    <!-- Main Login Container (Centered) -->
    <main class="w-full flex-1 flex items-center justify-center px-4 py-8 relative z-10">
        <div class="w-full max-w-md bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden transition-all duration-300">
            
            <!-- Card Top Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 sm:p-8 text-white text-center relative">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white mb-2 backdrop-blur-sm">
                    <i class="fa-solid fa-shield-halved text-emerald-300"></i> Secure Login
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Welcome Back
                </h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1.5">
                    Log in to access your Seller Hub or Buyer Dashboard.
                </p>
            </div>

            <div class="p-6 sm:p-8 space-y-6">

                <!-- Display Validation Errors -->
                <?php if($errors->any()): ?>
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-2 text-rose-700">
                            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                            <span>Unable to log in:</span>
                        </div>
                        <ul class="list-disc pl-5 space-y-0.5">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg mt-0.5"></i>
                        <div class="text-xs sm:text-sm font-semibold text-rose-800">
                            <?php echo e(session('error')); ?>

                        </div>
                    </div>
                <?php endif; ?>

                <?php if(session('success') || session('status')): ?>
                    <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                        <div class="text-xs sm:text-sm font-semibold text-emerald-800">
                            <?php echo e(session('success') ?? session('status')); ?>

                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Registered Email Address <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </div>
                            <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                                placeholder="name@business.com"
                                class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Password (पासवर्ड) <span class="text-rose-500">*</span>
                            </label>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                पासवर्ड भूल गए? (Forgot?)
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </div>
                            <input id="password" type="password" name="password" required
                                placeholder="Enter your account password"
                                class="w-full pl-10 pr-11 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye text-sm" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Security note -->
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span>Keep me logged in</span>
                        </label>
                        <span class="text-gray-400 flex items-center gap-1">
                            <i class="fa-solid fa-shield text-emerald-500"></i> Encrypted
                        </span>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-xl shadow-blue-600/30 hover:shadow-blue-600/40 transform active:scale-[0.99] transition duration-200 flex items-center justify-center gap-2">
                            <span>Log In to Dashboard</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                </form>

                <!-- Divider & Register Link -->
                <div class="pt-4 border-t border-gray-100 text-center space-y-3">
                    <p class="text-xs text-gray-600">
                        Don't have an account yet?
                    </p>
                    <div class="flex items-center justify-center gap-2">
                        <a href="<?php echo e(route('register', ['role' => 'seller'])); ?>" class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition">
                            Register as Seller
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="<?php echo e(route('register', ['role' => 'buyer'])); ?>" class="px-3.5 py-1.5 rounded-xl bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs font-bold transition">
                            Register as Buyer
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer Trust Info & Links -->
    <footer class="w-full py-6 text-center text-xs text-gray-400 relative z-10">
        <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 mb-3">
            <a href="<?php echo e(route('about')); ?>" class="hover:text-gray-200 transition">About Us</a>
            <span>•</span>
            <a href="<?php echo e(route('help')); ?>" class="hover:text-gray-200 transition">Help & Support</a>
            <span>•</span>
            <a href="<?php echo e(route('privacy')); ?>" class="hover:text-gray-200 transition">Privacy Policy</a>
            <span>•</span>
            <a href="<?php echo e(route('terms')); ?>" class="hover:text-gray-200 transition">Terms of Service</a>
        </div>
        <div class="flex items-center justify-center gap-6 mb-2">
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-lock text-green-400"></i> SSL 256-bit Secure</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-blue-400"></i> Pan-India Logistics</span>
            <span class="flex items-center gap-1.5"><i class="fa-brands fa-whatsapp text-emerald-400"></i> Direct WhatsApp Orders</span>
        </div>
        <p>&copy; <?php echo e(date('Y')); ?> VyaparIndia.online - All rights reserved.</p>
    </footer>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Lenovo1\Desktop\vyaparindia\resources\views/auth/login.blade.php ENDPATH**/ ?>