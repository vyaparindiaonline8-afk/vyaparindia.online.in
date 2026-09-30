<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $products = Product::with(['seller.sellerProfile', 'seller.sellerPage', 'category'])->latest()->take(16)->get();
        $featuredSellers = User::whereHas('sellerProfile')->with(['sellerProfile', 'sellerPage'])->take(8)->get();

        return view('public.home', compact('products', 'categories', 'featuredSellers'));
    }

    public function showProduct(Product $product)
    {
        return view('public.product', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $categoryId = $request->input('category_id');
        $location = trim($request->input('location', ''));

        $categories = Category::all();

        $prodQuery = Product::with(['seller.sellerProfile', 'seller.sellerPage', 'category']);

        if (!empty($query)) {
            $prodQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function ($cq) use ($query) {
                      $cq->where('name', 'like', "%{$query}%");
                  });
            });
        }

        if (!empty($categoryId)) {
            $prodQuery->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('categories', function ($cq) use ($categoryId) {
                      $cq->where('categories.id', $categoryId);
                  });
            });
        }

        if (!empty($location)) {
            $prodQuery->whereHas('seller', function ($sq) use ($location) {
                $sq->whereHas('sellerProfile', function ($spq) use ($location) {
                    $spq->where('city', 'like', "%{$location}%")
                        ->orWhere('state', 'like', "%{$location}%");
                })->orWhereHas('sellerPage', function ($spg) use ($location) {
                    $spg->where('city', 'like', "%{$location}%")
                        ->orWhere('pincode', 'like', "%{$location}%");
                });
            });
        }

        $products = $prodQuery->latest()->paginate(16);

        // Matching shops/dealers
        $sellerQuery = User::whereHas('sellerProfile');
        if (!empty($location)) {
            $sellerQuery->whereHas('sellerProfile', function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%");
            });
        }
        if (!empty($query)) {
            $sellerQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereHas('sellerProfile', function ($spq) use ($query) {
                      $spq->where('company_name', 'like', "%{$query}%");
                  });
            });
        }
        $shops = $sellerQuery->with(['sellerProfile', 'sellerPage'])->take(6)->get();

        return view('public.search', compact('products', 'shops', 'categories', 'query', 'categoryId', 'location'));
    }

    public function searchSellersByCity(Request $request)
    {
        $city = $request->input('city');

        $sellers = User::whereHas('sellerProfile', function ($query) use ($city) {
            $query->where('city', 'like', '%' . $city . '%');
        })->with(['sellerProfile', 'sellerPage'])->get();

        return view('public.seller_search_results', compact('sellers', 'city'));
    }
}
