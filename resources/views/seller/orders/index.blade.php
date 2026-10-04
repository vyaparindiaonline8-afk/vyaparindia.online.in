<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders & Daily Dispatches - VyaparIndia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

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
                        <span class="ml-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Order Management Console</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('seller.dashboard') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Title & Daily Highlights -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Customer Orders & Deliveries</h1>
                <p class="text-xs text-gray-500 mt-1">Track daily orders from your mini-website, marketplace listings & WhatsApp quick cart.</p>
            </div>

            <!-- Highlights -->
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-2 text-center">
                    <span class="block text-[11px] font-bold text-blue-700 uppercase">Today's Orders</span>
                    <span class="text-lg font-black text-blue-900">{{ $todayOrdersCount }}</span>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2 text-center">
                    <span class="block text-[11px] font-bold text-emerald-700 uppercase">Today's Sales</span>
                    <span class="text-lg font-black text-emerald-900">₹{{ number_format($todayRevenue, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Date Filter Tabs -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-gray-500 mr-1">Quick Date:</span>
                    @php
                        $d = request('date', 'all');
                        $hasCustom = request('from_date') || request('to_date');
                    @endphp
                    <a href="{{ route('seller.orders.index', array_merge(request()->except(['from_date', 'to_date']), ['date' => 'all'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($d === 'all' && !$hasCustom) ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        All Time
                    </a>
                    <a href="{{ route('seller.orders.index', array_merge(request()->except(['from_date', 'to_date']), ['date' => 'today'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($d === 'today' && !$hasCustom) ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        <i class="fa-solid fa-calendar-day mr-1"></i> Today
                    </a>
                    <a href="{{ route('seller.orders.index', array_merge(request()->except(['from_date', 'to_date']), ['date' => 'yesterday'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($d === 'yesterday' && !$hasCustom) ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Yesterday
                    </a>
                    <a href="{{ route('seller.orders.index', array_merge(request()->except(['from_date', 'to_date']), ['date' => 'this_week'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($d === 'this_week' && !$hasCustom) ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        This Week
                    </a>
                    <a href="{{ route('seller.orders.index', array_merge(request()->except(['from_date', 'to_date']), ['date' => 'this_month'])) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ ($d === 'this_month' && !$hasCustom) ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        This Month
                    </a>
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-500">Status:</span>
                    <select onchange="window.location.href=this.value" class="text-xs font-bold bg-gray-50 border border-gray-300 rounded-lg px-2.5 py-1.5 focus:outline-hidden">
                        @php $currStatus = request('status', 'all'); @endphp
                        <option value="{{ route('seller.orders.index', array_merge(request()->query(), ['status' => 'all'])) }}" {{ $currStatus === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="{{ route('seller.orders.index', array_merge(request()->query(), ['status' => 'pending'])) }}" {{ $currStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="{{ route('seller.orders.index', array_merge(request()->query(), ['status' => 'processing'])) }}" {{ $currStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="{{ route('seller.orders.index', array_merge(request()->query(), ['status' => 'shipped'])) }}" {{ $currStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="{{ route('seller.orders.index', array_merge(request()->query(), ['status' => 'delivered'])) }}" {{ $currStatus === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="{{ route('seller.orders.index', array_merge(request()->query(), ['status' => 'cancelled'])) }}" {{ $currStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Custom Date Range Form -->
            <form action="{{ route('seller.orders.index') }}" method="GET" class="pt-3 border-t border-gray-100 flex flex-wrap items-center gap-3">
                @if(request('status') && request('status') !== 'all')
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-600 flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-week text-blue-600"></i> Custom Date:
                    </span>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] text-gray-500">From:</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $fromDate ?? '') }}" class="text-xs font-medium px-2.5 py-1.5 border border-gray-300 rounded-lg bg-gray-50 focus:border-blue-500 focus:outline-hidden">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <label class="text-[11px] text-gray-500">To:</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $toDate ?? '') }}" class="text-xs font-medium px-2.5 py-1.5 border border-gray-300 rounded-lg bg-gray-50 focus:border-blue-500 focus:outline-hidden">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-xs transition flex items-center gap-1">
                        <i class="fa-solid fa-filter"></i>
                        <span>Apply Filter</span>
                    </button>
                    @if(request('from_date') || request('to_date') || (request('date') && request('date') !== 'all') || (request('status') && request('status') !== 'all'))
                        <a href="{{ route('seller.orders.index') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition flex items-center gap-1" title="Reset all filters">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Orders Table / Empty State -->
        @if($orders->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center max-w-md mx-auto shadow-xs">
                <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mx-auto mb-4">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">No orders found</h3>
                <p class="text-xs text-gray-500 mt-1">There are no orders matching your selected date or status filter.</p>
                <div class="mt-4">
                    <a href="{{ route('seller.orders.index') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition">
                        Show All Orders
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-2xl shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">Order # & Date</th>
                                <th class="py-3.5 px-4">Customer Details</th>
                                <th class="py-3.5 px-4">Delivery Address</th>
                                <th class="py-3.5 px-4">Items & Bill</th>
                                <th class="py-3.5 px-4">Payment</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @foreach($orders as $order)
                                @php
                                    $custName = $order->customer_name ?: ($order->buyer->name ?? 'Guest Customer');
                                    $custPhone = $order->customer_phone ?: ($order->buyer->phone_number ?? '');
                                    $custPhoneClean = preg_replace('/[^0-9]/', '', $custPhone);
                                    $itemCount = $order->products->sum(fn($p) => $p->pivot->quantity ?? 1);
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition">
                                    <!-- Order Number & Date -->
                                    <td class="py-4 px-4 font-medium">
                                        <a href="{{ route('seller.orders.show', $order) }}" class="font-bold text-blue-600 hover:underline">
                                            #{{ $order->order_number ?: $order->id }}
                                        </a>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $order->created_at->format('d M Y, h:i A') }}
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400 mt-0.5">
                                            {{ $order->created_at->diffForHumans() }}
                                        </div>
                                    </td>

                                    <!-- Customer Name & Phone -->
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900">{{ $custName }}</div>
                                        @if($custPhone)
                                            <div class="flex items-center gap-2 mt-1">
                                                <a href="tel:{{ $custPhone }}" class="text-[11px] text-gray-600 hover:text-blue-600 flex items-center gap-1" title="Call Customer">
                                                    <i class="fa-solid fa-phone text-xs"></i>
                                                    <span>{{ $custPhone }}</span>
                                                </a>
                                                <a href="https://wa.me/91{{ $custPhoneClean }}?text=Hello%20{{ urlencode($custName) }},%20regarding%20your%20Order%20%23{{ $order->order_number ?: $order->id }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 text-xs" title="Chat on WhatsApp">
                                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Address -->
                                    <td class="py-4 px-4 text-gray-600 max-w-xs">
                                        <div class="line-clamp-2 text-xs">
                                            {{ $order->shipping_address ?: ($order->city ? $order->city . ', ' . $order->pincode : 'Pickup / Unspecified') }}
                                        </div>
                                        @if($order->city)
                                            <span class="text-[10px] font-bold text-gray-400">{{ $order->city }} ({{ $order->pincode }})</span>
                                        @endif
                                    </td>

                                    <!-- Items & Amount -->
                                    <td class="py-4 px-4">
                                        <div class="font-black text-gray-900 text-sm">₹{{ number_format($order->total_price, 2) }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">{{ $itemCount }} {{ Str::plural('Item', $itemCount) }}</div>
                                    </td>

                                    <!-- Payment Mode -->
                                    <td class="py-4 px-4">
                                        @if($order->payment_method === 'online')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <i class="fa-solid fa-check mr-0.5"></i> Online / UPI
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                COD
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-4">
                                        @php
                                            $badgeColors = [
                                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                'delivered' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            ];
                                            $st = strtolower($order->status ?? 'pending');
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wide {{ $badgeColors[$st] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                            {{ ucfirst($order->status ?? 'Pending') }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-4 px-4 text-right">
                                        <a href="{{ route('seller.orders.show', $order) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold text-gray-700 transition">
                                            <span>Manage</span>
                                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </main>

</body>
</html>
