<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GST Tax Invoice #{{ $invoice->invoice_number }}</title>
    <script src="https://www.gstatic.com/antigravity/web/dev/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { background: white; color: black; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8 font-sans antialiased">
    <div class="max-w-3xl mx-auto bg-white border border-slate-300 rounded-2xl p-6 md:p-10 shadow-xl space-y-6">
        
        <!-- Header -->
        <div class="flex justify-between items-start pb-6 border-b-2 border-slate-900">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">TAX INVOICE</h1>
                <p class="text-xs text-slate-500 font-mono mt-1">Invoice No: <strong class="text-slate-900 font-bold">{{ $invoice->invoice_number }}</strong></p>
                <p class="text-xs text-slate-500 font-mono">Date: {{ $invoice->invoice_date->format('d M Y') }}</p>
            </div>
            <div class="text-right">
                <span class="bg-slate-900 text-white text-[11px] font-bold px-3 py-1 rounded-md uppercase tracking-wider">
                    {{ strtoupper($invoice->invoice_type) }} TAX INVOICE
                </span>
                <p class="text-xs font-bold text-slate-900 mt-2">Order #{{ $order->order_number }}</p>
            </div>
        </div>

        <!-- Seller & Buyer GST Information Grid -->
        <div class="grid grid-cols-2 gap-6 text-xs pb-6 border-b border-slate-200">
            <!-- Seller -->
            <div class="space-y-1">
                <span class="font-bold text-slate-400 uppercase text-[10px]">Seller (Biller):</span>
                <p class="font-bold text-slate-900 text-sm">{{ $invoice->seller_legal_name }}</p>
                <p class="text-slate-600">GSTIN: <strong class="font-mono text-slate-900">{{ $invoice->seller_gstin }}</strong></p>
                <p class="text-slate-600">State: {{ $invoice->seller_state_name }} (Code: {{ $invoice->seller_state_code }})</p>
            </div>

            <!-- Buyer -->
            <div class="space-y-1 text-right">
                <span class="font-bold text-slate-400 uppercase text-[10px]">Buyer (Consignee):</span>
                <p class="font-bold text-slate-900 text-sm">{{ $invoice->buyer_legal_name }}</p>
                @if($invoice->buyer_gstin)
                    <p class="text-slate-600">Buyer GSTIN: <strong class="font-mono text-slate-900">{{ $invoice->buyer_gstin }}</strong></p>
                @endif
                <p class="text-slate-600">State: {{ $invoice->buyer_state_name }} (Code: {{ $invoice->buyer_state_code }})</p>
                <p class="text-slate-600">{{ $order->shipping_address }}, {{ $order->city }} - {{ $order->pincode }}</p>
            </div>
        </div>

        <!-- Taxation Table -->
        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-100 text-slate-600 border-b border-slate-300">
                    <tr>
                        <th class="p-3">Description & HSN</th>
                        <th class="p-3 text-right">Taxable Amt</th>
                        @if($invoice->is_interstate)
                            <th class="p-3 text-right">IGST (18%)</th>
                        @else
                            <th class="p-3 text-right">CGST (9%)</th>
                            <th class="p-3 text-right">SGST (9%)</th>
                        @endif
                        <th class="p-3 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="p-3">
                            <strong class="text-slate-900">E-Commerce Goods / Dropship Fulfillment</strong>
                            <span class="block text-[10px] text-slate-500 font-mono">HSN: 85176290 / 650500</span>
                        </td>
                        <td class="p-3 text-right font-mono font-semibold">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
                        @if($invoice->is_interstate)
                            <td class="p-3 text-right font-mono">₹{{ number_format($invoice->igst_amount, 2) }}</td>
                        @else
                            <td class="p-3 text-right font-mono">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                            <td class="p-3 text-right font-mono">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                        @endif
                        <td class="p-3 text-right font-mono font-bold text-slate-900">₹{{ number_format($invoice->invoice_total, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Calculation Box -->
        <div class="flex justify-end pt-4">
            <div class="w-72 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">Taxable Value:</span> <span class="font-mono font-semibold">₹{{ number_format($invoice->taxable_amount, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Total GST:</span> <span class="font-mono font-semibold">₹{{ number_format($invoice->total_tax, 2) }}</span></div>
                <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-300">
                    <span>Grand Total:</span> <span class="font-mono">₹{{ number_format($invoice->invoice_total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="no-print pt-6 border-t border-slate-200 flex justify-end gap-3">
            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print GST Tax Invoice
            </button>
        </div>
    </div>
</body>
</html>