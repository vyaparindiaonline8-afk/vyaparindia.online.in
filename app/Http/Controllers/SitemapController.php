<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SellerPage;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap.xml for Google & search engine crawlers.
     */
    public function index(): Response
    {
        $sellerPages = SellerPage::select('slug', 'updated_at')->get();
        $products = Product::with(['user.sellerPage'])
            ->whereNotNull('slug')
            ->select('id', 'user_id', 'slug', 'updated_at')
            ->latest('updated_at')
            ->limit(1000)
            ->get();

        $content = view('sitemap', compact('sellerPages', 'products'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
