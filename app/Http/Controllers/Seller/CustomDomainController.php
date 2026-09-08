<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use App\Models\SellerPage;
use App\Services\DomainVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomDomainController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sellerPage = $user ? $user->sellerPage : SellerPage::first();
        $customDomain = $sellerPage ? CustomDomain::where('seller_page_id', $sellerPage->id)->first() : null;

        return view('seller.minisite.custom_domain', compact('sellerPage', 'customDomain'));
    }

    public function store(Request $request, DomainVerificationService $service)
    {
        $request->validate([
            'domain' => 'required|string',
        ]);

        $user = Auth::user();
        $sellerPage = $user ? $user->sellerPage : SellerPage::first();

        if (!$sellerPage) {
            return back()->with('error', 'Please configure your mini-site before connecting a custom domain.');
        }

        $domain = $service->registerDomain($sellerPage, $request->domain);

        return redirect()->route('seller.minisite.custom_domain.index')->with('success', "Domain {$domain->domain} added! Please configure DNS CNAME record.");
    }

    public function verify(CustomDomain $customDomain, DomainVerificationService $service)
    {
        $res = $service->verifyDns($customDomain);
        if ($res['success']) {
            return back()->with('success', $res['message']);
        }
        return back()->with('error', $res['message']);
    }
}