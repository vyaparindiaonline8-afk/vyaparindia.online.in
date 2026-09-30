<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropshipping Partners & Retailer Network - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen">

    <!-- Top Nav -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-extrabold text-lg text-gray-900 leading-tight">Dropshipping Retailer Network</h1>
                        <p class="text-xs text-gray-500">Approve retailer requests, monitor connected stores, and grow your B2B sales</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.wholesaler.orders') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition">
                        Fulfillment Orders
                    </a>
                    <a href="{{ route('seller.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending Requests</span>
                    <div class="text-2xl sm:text-3xl font-black text-amber-600 mt-1">{{ $pendingCount }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-clock"></i>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Active Dropship Partners</span>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">{{ $approvedCount }}</div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-handshake"></i>
                </div>
            </div>

            <div class="col-span-2 lg:col-span-1 bg-white rounded-3xl p-6 border border-gray-200 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Order Split Routing</span>
                    <div class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full mt-2 inline-block">
                        Dual WhatsApp & DB Alert Active
                    </div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-arrows-split-up-and-left"></i>
                </div>
            </div>
        </div>

        <!-- Partnership Requests Table -->
        <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Retailers Requesting to Sell Your Products</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Approved retailers can import your products and route orders to you for fulfillment.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                            <th class="py-3 px-4">Retailer / Store</th>
                            <th class="py-3 px-4">Contact & Location</th>
                            <th class="py-3 px-4">Request Note</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($partnerships as $partner)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-gray-900 text-sm">
                                        {{ $partner->retailer->sellerProfile->company_name ?? $partner->retailer->name }}
                                    </div>
                                    @if($partner->retailer->sellerPage)
                                        <div class="text-[11px] text-blue-600 font-bold">
                                            Store: {{ $partner->retailer->sellerPage->page_title }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-gray-800">{{ $partner->retailer->email }}</div>
                                    <div class="text-gray-500 text-[11px]">{{ $partner->retailer->sellerProfile->city ?? 'India' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-gray-600 italic">
                                    "{{ $partner->request_note ?? 'Standard dropship partner request' }}"
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($partner->status === 'approved')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                            ✓ Approved Partner
                                        </span>
                                    @elseif($partner->status === 'rejected')
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">
                                            Pending Review
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($partner->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('seller.wholesaler.updatePartnership', $partner->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-xs">
                                                    Accept
                                                </button>
                                            </form>
                                            <form action="{{ route('seller.wholesaler.updatePartnership', $partner->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[11px] transition border border-rose-200">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($partner->status === 'approved')
                                        <form action="{{ route('seller.wholesaler.updatePartnership', $partner->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="text-rose-500 hover:underline text-[11px]">
                                                Revoke Access
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('seller.wholesaler.updatePartnership', $partner->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="text-blue-600 hover:underline text-[11px]">
                                                Re-Approve
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-handshake text-3xl mb-2 text-gray-300"></i>
                                    <p class="font-bold text-gray-600">No retailer partnership requests yet</p>
                                    <p class="text-xs mt-1">Retailers browsing your products in the Dropship Hub will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($partnerships->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $partnerships->links() }}
                </div>
            @endif
        </div>

    </div>
</body>
</html>
