<!DOCTYPE html>
<html lang="hi" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>पासवर्ड भूल गए? (Forgot Password) | VyaparIndia</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between bg-gradient-to-b from-slate-50 via-gray-100 to-slate-200 text-gray-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Minimal Navigation -->
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group transition">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-bolt-lightning text-lg"></i>
            </div>
            <div>
                <span class="text-xl font-extrabold tracking-tight text-gray-900">Vyapar<span class="text-blue-600">India</span></span>
                <span class="block text-[10px] font-bold text-gray-600 tracking-wider uppercase">B2B Portal</span>
            </div>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-gray-600 hover:text-blue-600 transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                लॉगिन पर वापस जाएं
            </a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 py-8 sm:py-12">
        <div class="w-full max-w-md">
            
            <!-- Card Container -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-gray-100 overflow-hidden">
                
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-blue-700 to-indigo-700 px-6 sm:px-8 pt-8 pb-7 text-white text-center relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md mb-3 text-white">
                        <i class="fa-solid fa-key text-xl"></i>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight">पासवर्ड रीसेट करें</h1>
                    <p class="text-blue-100 text-xs sm:text-sm mt-1">अपना रजिस्टर्ड ईमेल दर्ज करें, हम आपको रीसेट लिंक भेजेंगे</p>
                </div>

                <div class="p-6 sm:p-8">
                    <!-- Status / Success Alert -->
                    @if (session('status'))
                        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                            <div class="text-xs sm:text-sm font-semibold text-emerald-800">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if ($errors->any())
                        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg mt-0.5"></i>
                            <div class="text-xs sm:text-sm font-semibold text-rose-800">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                रजिस्टर्ड ईमेल (Registered Email) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                    placeholder="apki-email@gmail.com"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-sm shadow-md shadow-blue-500/25 hover:shadow-lg transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                            <span>रीसेट लिंक भेजें</span>
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-500 hover:text-blue-600 transition">
                            <i class="fa-solid fa-arrow-left text-[10px] mr-1"></i>
                            पासवर्ड याद आ गया? लॉगिन करें
                        </a>
                    </div>
                </div>
            </div>

            <!-- Trust Badge -->
            <div class="mt-6 text-center text-xs text-gray-500 flex items-center justify-center gap-2">
                <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                <span>256-Bit SSL एन्क्रिप्टेड एवं सुरक्षित पासवर्ड रिकवरी</span>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-5 text-center text-xs text-gray-600 border-t border-gray-200/60 bg-white/40">
        <p>© {{ date('Y') }} VyaparIndia Marketplace. सर्वाधिकार सुरक्षित।</p>
    </footer>

</body>
</html>
