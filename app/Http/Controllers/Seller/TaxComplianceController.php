<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Services\TaxComplianceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxComplianceController extends Controller
{
    /**
     * Display the Dedicated Tax & CA Compliance Center
     */
    public function index(Request $request, TaxComplianceService $service)
    {
        $userId = Auth::id() ?: 1;

        // Date Presets or Custom Range
        $preset = $request->input('period_preset', 'this_month');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if (!$fromDate || !$toDate) {
            if ($preset === 'last_month') {
                $fromDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
                $toDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
            } elseif ($preset === 'this_quarter') {
                $fromDate = Carbon::now()->startOfQuarter()->format('Y-m-d');
                $toDate = Carbon::now()->endOfQuarter()->format('Y-m-d');
            } elseif ($preset === 'financial_year') {
                $currentYear = (int)date('Y');
                $startYear = ((int)date('m') >= 4) ? $currentYear : ($currentYear - 1);
                $fromDate = "{$startYear}-04-01";
                $toDate = Carbon::now()->format('Y-m-d');
            } else {
                // this_month
                $fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $toDate = Carbon::now()->format('Y-m-d');
                $preset = 'this_month';
            }
        } else {
            $preset = 'custom';
        }

        $channel = $request->input('channel', 'all');

        $reportData = $service->compileTaxReport($userId, $fromDate, $toDate, $channel);

        return view('seller.tax_compliance.index', compact('reportData', 'preset', 'fromDate', 'toDate', 'channel'));
    }

    /**
     * Update Seller's Tax Profile (Tax Scheme, GSTIN, Enrolment ID, State)
     */
    public function updateProfile(Request $request)
    {
        $userId = Auth::id() ?: 1;
        $request->validate([
            'tax_scheme' => 'required|in:regular,composition,unregistered',
            'gst_number' => 'nullable|string|max:30',
            'gst_enrolment_id' => 'nullable|string|max:50',
            'state' => 'required|string|max:100',
        ]);

        $profile = SellerProfile::firstOrCreate(['user_id' => $userId]);
        $profile->update([
            'tax_scheme' => $request->tax_scheme,
            'gst_number' => $request->gst_number ? strtoupper(trim($request->gst_number)) : null,
            'gst_enrolment_id' => $request->gst_enrolment_id ? trim($request->gst_enrolment_id) : null,
            'state' => trim($request->state),
        ]);

        return back()->with('success', '🎉 Tax & GST Profile successfully update ho gaya!');
    }

    /**
     * 📥 Download Complete 3-Tab CA Excel Workbook (.xlsx)
     */
    public function exportCaPackage(Request $request, TaxComplianceService $service)
    {
        $userId = Auth::id() ?: 1;
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $channel = $request->input('channel', 'all');

        $filePath = $service->generateCaPackageExcel($userId, $fromDate, $toDate, $channel);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Excel file generate karne me samasya aayi.');
        }

        $downloadName = 'CA_GST_Report_' . ($fromDate ?: 'from') . '_to_' . ($toDate ?: 'to') . '.xlsx';
        return response()->download($filePath, $downloadName);
    }

    /**
     * 📥 Download GSTR-1 Sales Register (.csv)
     */
    public function exportGstr1(Request $request, TaxComplianceService $service)
    {
        $userId = Auth::id() ?: 1;
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $channel = $request->input('channel', 'all');

        $reportData = $service->compileTaxReport($userId, $fromDate, $toDate, $channel);
        $sales = $reportData['sales'];

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=GSTR1_Sales_" . ($fromDate ?: 'from') . "_to_" . ($toDate ?: 'to') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Invoice / Order No', 'Invoice Date', 'Channel', 'Customer Name',
            'Place of Supply', 'Supply Type', 'HSN Code', 'Product', 'Qty',
            'Taxable Value', 'GST Rate', 'CGST', 'SGST', 'IGST', 'Total Invoice'
        ];

        $callback = function () use ($sales, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($sales as $s) {
                fputcsv($file, [
                    $s['order_number'], $s['date'], $s['channel'], $s['customer_name'],
                    $s['pos_state'], $s['supply_type'], $s['hsn'], $s['product_name'],
                    $s['quantity'], $s['taxable_value'], $s['gst_rate'] . '%',
                    $s['cgst'], $s['sgst'], $s['igst'], $s['total_amount']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 📥 Download Platform Fees & ITC Register (.csv)
     */
    public function exportItc(Request $request, TaxComplianceService $service)
    {
        $userId = Auth::id() ?: 1;
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $channel = $request->input('channel', 'all');

        $reportData = $service->compileTaxReport($userId, $fromDate, $toDate, $channel);
        $expenses = $reportData['expenses'];

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Platform_Fees_ITC_" . ($fromDate ?: 'from') . "_to_" . ($toDate ?: 'to') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Date', 'Service Provider', 'Expense Type', 'Reference Order',
            'Net Fee Before GST', 'GST Rate', 'Eligible ITC (18% GST)', '1% TCS Deducted',
            'Total Charged by Platform', 'Eligibility Note'
        ];

        $callback = function () use ($expenses, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($expenses as $e) {
                fputcsv($file, [
                    $e['date'], $e['provider'], $e['type'], $e['ref'],
                    $e['taxable_fee'], '18%', $e['itc_amount'], $e['tcs_amount'],
                    $e['total_fee'], $e['note']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
