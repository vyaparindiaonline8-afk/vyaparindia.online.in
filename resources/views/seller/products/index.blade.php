<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Catalog Products - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
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
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Product Catalog</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('seller.catalog.upload') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i> Upload Catalog PDF
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-blue-600 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tier Status & Quota Banner -->
        @php
            $isProfileOnly = Auth::user()->isProfileOnly();
            $productCount = $products->count();
        @endphp

        @if($isProfileOnly)
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 font-black text-xs">
                            Basic Profile Tier
                        </span>
                        <span class="text-xs font-bold text-amber-800">
                            Quota: <strong>{{ $productCount }} / 50</strong> products listed
                        </span>
                    </div>
                    <p class="text-xs text-amber-700">
                        Aapka Basic Profile plan active hai (Limit: 50 products). Unlimited products, custom domain, aur WhatsApp cart checkout ke liye Mini-Website activate karein.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('seller.minisite.create') }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-rocket"></i>
                        <span>Upgrade to Mini-Website</span>
                    </a>
                </div>
            </div>
        @else
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-3xl p-4 sm:p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-emerald-900">Mini-Website Tier Active</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-bold">Unlimited Products</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">Aapka branded online storefront live hai with direct WhatsApp order & cart checkout.</p>
                    </div>
                </div>
                <a href="{{ route('seller.minisite.edit') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline">
                    Customize Storefront
                </a>
            </div>
        @endif

        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Products ({{ $productCount }})</h1>
                <p class="text-xs text-gray-500">Manage pricing, variants, and stock of your catalog items.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('seller.brand-master.index') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold text-xs shadow-md shadow-amber-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-award"></i>
                    <span>ब्रांड मास्टर कैटलॉग (Plasto) ⚡</span>
                </a>

                @if(Auth::user()->canAddProduct())
                    <a href="{{ route('seller.products.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Single Product</span>
                    </a>
                @else
                    <button disabled class="px-5 py-2.5 rounded-xl bg-gray-200 text-gray-400 font-bold text-xs cursor-not-allowed flex items-center gap-2" title="Limit reached (50/50)">
                        <i class="fa-solid fa-lock"></i>
                        <span>50 Products Limit Reached</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- 📊 Excel Sync & Daily Rate Revision Suite Banner -->
        <div class="bg-gradient-to-br from-indigo-900 via-slate-900 to-blue-900 rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
                <div class="space-y-1.5 max-w-xl">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                        <i class="fa-solid fa-bolt"></i> 1-क्लिक एक्सेल सिंक & दैनिक भाव रिवीज़न
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">
                        एक्सेल शीट डाउनलोड व बल्क रेट अपडेट
                    </h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        हार्डवेयर, मंडी व किराना के दैनिक रेट्स बदलें या पूरे कैटलॉग को एक्सेल में डाउनलोड करके एडिट करें। सिस्टम <strong>Product ID</strong> से पहचान कर नाम व रेट्स तुरंत अपडेट कर देता है।
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <!-- Daily Rates Sheet Export -->
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'rates']) }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-2 backdrop-blur-xs hover:scale-102 active:scale-98" title="Download quick rates revision template">
                        <i class="fa-solid fa-file-invoice-dollar text-amber-400 text-sm"></i>
                        <span>दैनिक भाव शीट (.csv)</span>
                    </a>

                    <!-- Full Catalog Export -->
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'full']) }}" class="px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-2 backdrop-blur-xs hover:scale-102 active:scale-98" title="Download entire catalog master excel">
                        <i class="fa-solid fa-table-list text-emerald-400 text-sm"></i>
                        <span>पूरा कैटलॉग (.csv)</span>
                    </a>

                    <!-- Upload & Sync Button -->
                    <button type="button" onclick="openExcelSyncModal()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-black text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-2 hover:scale-102 active:scale-98">
                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                        <span>एक्सेल अपलोड / अपडेट</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Selling Price</th>
                            <th class="py-3 px-4">Variants</th>
                            <th class="py-3 px-4">Stock Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gray-100 overflow-hidden flex items-center justify-center shrink-0 border border-gray-100">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" class="h-full w-full object-contain">
                                            @else
                                                <i class="fa-solid fa-cube text-gray-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-gray-900 text-sm">{{ $product->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono">{{ $product->sku ?? 'PRD-STD' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-700">
                                    {{ $product->category->name ?? 'General' }}
                                </td>
                                <td class="py-3 px-4 font-black text-gray-900">
                                    ₹{{ number_format($product->price, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($product->variants && $product->variants->count() > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px]">
                                            {{ $product->variants->count() }} Variants
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-[11px]">Single Item</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if(!$product->track_inventory)
                                        <span class="text-gray-500 font-medium text-[11px]">On-Demand</span>
                                    @elseif($product->stock_quantity <= 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[10px]">Out of Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px]">{{ $product->stock_quantity }} in stock</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('seller.products.edit', $product) }}" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Edit Product">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete Product">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-box-open text-4xl mb-2 text-gray-300"></i>
                                    <p class="font-bold text-gray-600">No products added yet</p>
                                    <p class="text-xs mt-1">Start listing items or upload a catalog PDF.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- 📊 Excel Sync & Round-Trip Bulk Update Modal -->
    <div id="excelSyncModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-gray-100 space-y-5 animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-xs">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base text-gray-900">एक्सेल शीट अपलोड व ऑटो-सिंक</h3>
                        <p class="text-[11px] text-gray-500">ID के आधार पर नाम, रेट्स व स्टॉक का 1-क्लिक राउंड-ट्रिप अपडेट</p>
                    </div>
                </div>
                <button type="button" onclick="closeExcelSyncModal()" class="h-8 w-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Features Highlights -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-[11px] text-slate-700">
                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>सिस्टम कैसे काम करता है?</span>
                </div>
                <ul class="space-y-1 text-slate-600 list-disc list-inside">
                    <li><strong>Product ID से ट्रैकिंग:</strong> शीट में मौजूद <code>Product ID</code> के जरिए प्रोडक्ट सटीक रूप से पहचाना जाता है।</li>
                    <li><strong>नाम बदलना सुरक्षित:</strong> यदि आप एक्सेल में प्रोडक्ट का नाम भी बदल देंगे, तब भी ID से सही आइटम का नाम व रेट अपडेट होगा (डुप्लीकेट नहीं बनेगा)।</li>
                    <li><strong>नई लाइन = नया आइटम:</strong> यदि आप शीट के नीचे बिना ID के नई लाइन जोड़ते हैं, तो वह नया प्रोडक्ट लिस्ट हो जाएगा।</li>
                </ul>
            </div>

            <!-- Download Shortcuts in Modal -->
            <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50 border border-amber-200 gap-3">
                <div class="text-[11px] text-amber-900 leading-snug">
                    <strong>शीट डाउनलोड नहीं की?</strong><br>
                    अपनी लाइव लिस्टिंग की शीट अभी डाउनलोड करें:
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'rates']) }}" class="px-2.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-black text-[11px] flex items-center gap-1 transition">
                        <i class="fa-solid fa-download"></i> दैनिक भाव
                    </a>
                    <a href="{{ route('seller.products.export_sheet', ['type' => 'full']) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-black text-[11px] flex items-center gap-1 transition">
                        <i class="fa-solid fa-download"></i> पूरा कैटलॉग
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('seller.products.import_sheet') }}" method="POST" enctype="multipart/form-data" id="excelSyncForm" onsubmit="return submitExcelSyncForm(event)" class="space-y-4">
                @csrf
                <input type="hidden" name="spreadsheet_rows" id="spreadsheetRowsJson" value="">

                <!-- Upload Drag-and-Drop Area -->
                <div class="border-2 border-dashed border-gray-300 hover:border-emerald-500 rounded-2xl p-6 text-center bg-gray-50/70 hover:bg-emerald-50/20 cursor-pointer transition group" onclick="document.getElementById('excelFileInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up text-emerald-600 text-3xl mb-2 group-hover:scale-110 transition"></i>
                    <h4 class="text-xs font-bold text-gray-800">एडिट की हुई एक्सेल या CSV फाइल चुनें</h4>
                    <p class="text-[11px] text-gray-400 mt-0.5">Click karein ya file yahan drop karein (.xlsx, .xls, .csv)</p>
                    <input type="file" id="excelFileInput" name="spreadsheet_file" accept=".xlsx, .xls, .csv" class="hidden" onchange="handleExcelFileSelect(event)">
                </div>

                <!-- Preview Box -->
                <div id="excelFilePreviewBox" class="hidden p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                        <span id="excelFileName" class="truncate max-w-[280px]">filename.xlsx</span>
                        <span id="excelRowCount" class="px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[10px] font-black">0 Rows Found</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">
                        शीट लोड हो चुकी है! "सिंक व अपडेट करें" बटन दबाकर नए रेट्स व नाम लागू करें।
                    </p>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" onclick="closeExcelSyncModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                        रद्द करें (Cancel)
                    </button>
                    <button type="submit" id="btnApplyExcelSync" disabled class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-xs font-black shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>सिंक व अपडेट करें (Apply Changes)</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openExcelSyncModal() {
            const modal = document.getElementById('excelSyncModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeExcelSyncModal() {
            const modal = document.getElementById('excelSyncModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function handleExcelFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            document.getElementById('excelFileName').textContent = file.name;
            const previewBox = document.getElementById('excelFilePreviewBox');
            const rowCountEl = document.getElementById('excelRowCount');
            const btnApply = document.getElementById('btnApplyExcelSync');

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const sheetName = workbook.SheetNames[0];
                    const sheet = workbook.Sheets[sheetName];
                    const jsonRows = XLSX.utils.sheet_to_json(sheet, { defval: '' });

                    if (jsonRows.length === 0) {
                        alert('Sheet khali hai ya koi row nahi mili!');
                        return;
                    }

                    document.getElementById('spreadsheetRowsJson').value = JSON.stringify(jsonRows);
                    rowCountEl.textContent = `${jsonRows.length} Rows Detected`;
                    previewBox.classList.remove('hidden');
                    btnApply.disabled = false;
                } catch (err) {
                    console.error(err);
                    // Fallback to native multipart form submit if SheetJS fails
                    rowCountEl.textContent = 'File Ready for Server Processing';
                    previewBox.classList.remove('hidden');
                    btnApply.disabled = false;
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function submitExcelSyncForm(event) {
            const btn = document.getElementById('btnApplyExcelSync');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> सिंक हो रहा है...';
            return true;
        }
    </script>
</body>
</html>
