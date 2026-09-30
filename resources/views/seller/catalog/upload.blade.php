<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI PDF Catalog & Brochure Ingestion - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-gray-900 text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">AI Catalog Engine</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i> Dashboard
                    </a>
                    <a href="{{ route('seller.inventory.index') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-boxes-stacked"></i> Inventory
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 font-medium">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-blue-600">Seller Hub</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
            <span class="text-gray-900 font-bold">PDF Catalog & Price List Ingestion</span>
        </nav>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden">
            <div class="max-w-2xl relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-xs font-bold backdrop-blur-xs">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                    <span>Amazon & Google Merchant Style Auto-Extraction</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                    PDF Brochure & Rate List Se 1-Click Multi-Variant Listing
                </h1>
                <p class="text-sm sm:text-base text-blue-100 leading-relaxed">
                    Apna PDF catalogue ya price list upload karein. Hamara AI system automatically non-product pages (About us, ISO cert, Chairman quotes) ko filter karke 1 image ke multiple sizes, grades aur rate tables ko e-commerce ready bana deta hai.
                </p>
            </div>
            
            <div class="absolute -right-8 -bottom-8 opacity-10 text-white pointer-events-none text-[160px]">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
        </div>

        <!-- 4 Superpowers Feature Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs space-y-2">
                <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm">Smart Noise Filter</h3>
                <p class="text-xs text-gray-500 leading-normal">
                    Company profile, terms & conditions aur About us pages ko automatically skip karta hai.
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs space-y-2">
                <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm">1 Image + Multi-Variant</h3>
                <p class="text-xs text-gray-500 leading-normal">
                    CPVC pipe, fitting ya kapde ke 1 photo ke sath saare sizes (1/2", 1") aur grades ko table me group karta hai.
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs space-y-2">
                <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm">GST Costing Engine</h3>
                <p class="text-xs text-gray-500 leading-normal">
                    List Price se Trade Discount hata kar Net Landing Cost (With/Without GST) aur Wholesale/Retail prices calculate karein.
                </p>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs space-y-2">
                <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm">Optional Stock Control</h3>
                <p class="text-xs text-gray-500 leading-normal">
                    "Dale to thik, na dale to thik". Stock daalenge to auto-deduct/restock hoga, khali chhodenge to on-demand chalega.
                </p>
            </div>
        </div>

        <!-- Upload Card -->
        <div class="bg-white rounded-3xl border border-gray-200 p-8 shadow-xs space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div>
                    <h2 class="text-lg font-black text-gray-900">Upload Product Brochure / Rate List</h2>
                    <p class="text-xs text-gray-500">PDF files up to 50MB supported (High-res product images auto-extracted)</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 bg-blue-50 text-blue-700 rounded-full">
                    <i class="fa-solid fa-bolt mr-1"></i> AI Powered
                </span>
            </div>

            <form action="{{ route('seller.catalog.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="space-y-6">
                @csrf

                <!-- Dropzone Area -->
                <div id="dropzone" class="border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-3xl p-10 text-center cursor-pointer transition bg-gray-50/50 hover:bg-blue-50/20 group">
                    <input type="file" name="catalog_pdf" id="catalog_pdf" accept="application/pdf" class="hidden" required onchange="handleFileSelect(this)">
                    
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="h-16 w-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition" id="fileLabel">
                                Click to choose or drag & drop PDF catalog here
                            </span>
                            <p class="text-xs text-gray-400 mt-1" id="fileSubtext">
                                PDF format only (e.g. Astral_Pipes_2026.pdf, Garment_Catalog.pdf)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="text-xs text-gray-500 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                        <span>Upload hone ke baad aapko visual screen milegi jahan aap rates aur details edit kar sakenge.</span>
                    </div>

                    <button type="submit" id="submitBtn" class="px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-lg shadow-blue-500/20 flex items-center gap-2 transition disabled:opacity-50">
                        <i class="fa-solid fa-gear fa-spin hidden" id="spinner"></i>
                        <span id="btnText">Parse & Ingest Catalog</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Ingestions History -->
        @if($recentJobs->count() > 0)
            <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xs space-y-4">
                <h3 class="font-extrabold text-base text-gray-900">
                    Previous Catalog Uploads ({{ $recentJobs->count() }})
                </h3>

                <div class="divide-y divide-gray-100">
                    @foreach($recentJobs as $job)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $job->filename }}</h4>
                                    <p class="text-xs text-gray-400">
                                        Uploaded {{ $job->created_at->diffForHumans() }} • 
                                        <span class="font-semibold text-gray-600">{{ $job->total_products_detected }} products detected</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($job->status === 'ready_for_review')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Ready for Review
                                    </span>
                                    <a href="{{ route('seller.catalog.review', $job->id) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition flex items-center gap-1.5">
                                        <span>Review & Publish</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @elseif($job->status === 'published')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Published to Live Store
                                    </span>
                                    <a href="{{ route('seller.inventory.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition flex items-center gap-1.5">
                                        <span>View in Inventory</span>
                                    </a>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('catalog_pdf');
        const fileLabel = document.getElementById('fileLabel');
        const fileSubtext = document.getElementById('fileSubtext');
        const uploadForm = document.getElementById('uploadForm');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('spinner');
        const btnText = document.getElementById('btnText');

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50/40');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50/40');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50/40');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect(fileInput);
            }
        });

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                fileLabel.textContent = "Selected: " + file.name;
                fileSubtext.textContent = (file.size / (1024 * 1024)).toFixed(2) + " MB • Ready to analyze";
                dropzone.classList.add('border-emerald-500', 'bg-emerald-50/20');
            }
        }

        uploadForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            spinner.classList.remove('hidden');
            btnText.textContent = "Analyzing PDF with AI...";
        });
    </script>
</body>
</html>
