<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('public.home', compact('products'));
    }

    public function showProduct(Product $product)
    {
        return view('public.product', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('name', 'like', '%' . $query . '%')
                           ->orWhere('description', 'like', '%' . $query . '%')
                           ->get();

        return view('public.search', compact('products'));
    }

    public function searchSellersByCity(Request $request)
    {
        $city = $request->input('city');

        $sellers = User::whereHas('sellerProfile', function ($query) use ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        })->with('sellerProfile')->get();

        return view('public.seller_search_results', compact('sellers', 'city'));
    }
}
