<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management & Consolidation - VyaparIndia Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Admin Header -->
    <header class="bg-gray-900 text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="h-9 w-9 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-base shadow-sm">
                        V
                    </a>
                    <div>
                        <span class="font-black text-white text-base tracking-tight">VyaparIndia</span>
                        <span class="ml-2 text-xs font-bold text-blue-400 bg-blue-950 px-2 py-0.5 rounded-full border border-blue-800">Admin Console</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-gray-300 hover:text-white flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Admin Dashboard</span>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-external-link"></i>
                        <span>Live Marketplace</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Header Title Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Marketplace Categories</h1>
                <p class="text-xs text-gray-500 mt-1">Manage categories created by sellers, monitor product distribution, and merge duplicate tags.</p>
            </div>
            <div>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Category</span>
                </a>
            </div>
        </div>

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Category Consolidation & Merge Tool Card -->
        <div class="mb-8 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-800/80 text-blue-200 text-xs font-bold mb-3 border border-blue-700">
                    <i class="fa-solid fa-code-merge"></i>
                    <span>Smart Category Merge & Consolidation</span>
                </div>
                <h2 class="text-xl font-black text-white">Merge Duplicate or Redundant Categories</h2>
                <p class="text-xs text-blue-200 mt-1">
                    When multiple sellers create slightly different category names (e.g. <em>"CPVC Fittings"</em> and <em>"CPVC Pipes & Fittings"</em>), merge them together. All products will be instantly reassigned to the target category with zero data loss.
                </p>

                <form action="{{ route('admin.categories.merge') }}" method="POST" onsubmit="return confirm('Are you sure you want to merge these categories? All products from the source category will be moved to the target category, and the source category will be permanently deleted.');" class="mt-6 bg-white/10 backdrop-blur-md p-4 sm:p-6 rounded-2xl border border-white/20 grid grid-cols-1 sm:grid-cols-12 gap-4 items-end text-gray-900">
                    @csrf

                    <!-- Source Category -->
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-bold text-white mb-1.5">
                            <span class="text-rose-300">1. Source Category</span> (Will be deleted)
                        </label>
                        <select name="source_category_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-xs font-bold bg-white text-gray-800 focus:outline-hidden">
                            <option value="">-- Select Source Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }} ({{ $category->products_count }} products)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Arrow Indicator -->
                    <div class="sm:col-span-1 hidden sm:flex items-center justify-center pb-3 text-white text-lg">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>

                    <!-- Target Category -->
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-white mb-1.5">
                            <span class="text-emerald-300">2. Target Category</span> (Will keep all products)
                        </label>
                        <select name="target_category_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-xs font-bold bg-white text-gray-800 focus:outline-hidden">
                            <option value="">-- Select Target Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }} ({{ $category->products_count }} products)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Merge Button -->
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full py-2.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-check-double"></i>
                            <span>Merge Now</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-tags text-blue-600"></i>
                    <h3 class="text-sm font-bold text-gray-900">All Live Marketplace Categories ({{ $categories->count() }})</h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-6">ID</th>
                            <th class="py-3 px-6">Category Name</th>
                            <th class="py-3 px-6">URL Slug</th>
                            <th class="py-3 px-6 text-center">Listed Products</th>
                            <th class="py-3 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="py-3 px-6 text-gray-400 font-mono text-[11px]">#{{ $category->id }}</td>
                                <td class="py-3 px-6 font-bold text-gray-900 flex items-center gap-2">
                                    <span>{{ $category->name }}</span>
                                    <a href="{{ route('search', ['category' => $category->name]) }}" target="_blank" title="View live in marketplace" class="text-blue-500 hover:text-blue-700 text-[10px]">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </td>
                                <td class="py-3 px-6 text-gray-500 font-mono text-[11px]">{{ $category->slug }}</td>
                                <td class="py-3 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $category->products_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $category->products_count }} items
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category \'{{ $category->name }}\'?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 text-xs">
                                    No categories found. Click 'Create Category' to add one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
