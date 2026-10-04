<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\Auth;

class SellerProfileController extends Controller
{
    public function create()
    {
        if (Auth::user()->sellerProfile) {
            return redirect()->route('seller.profile.edit');
        }
        return view('seller.profile.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:30',
            'google_map_url' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'facebook_url' => 'nullable|string|max:500',
            'instagram_url' => 'nullable|string|max:500',
            'youtube_url' => 'nullable|string|max:500',
            'google_business_url' => 'nullable|string|max:500',
            'dispatch_radius' => 'nullable|integer|min:0',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
        ]);

        $profile = Auth::user()->sellerProfile()->create($request->all());

        // Sync with SellerPage if exists
        $sellerPage = Auth::user()->sellerPage;
        if ($sellerPage) {
            $sellerPage->update([
                'support_phone' => $request->office_phone ?? $sellerPage->support_phone,
                'google_map_link' => $request->google_map_url ?? $sellerPage->google_map_link,
                'facebook_link' => $request->facebook_url ?? $sellerPage->facebook_link,
                'instagram_link' => $request->instagram_url ?? $sellerPage->instagram_link,
                'youtube_link' => $request->youtube_url ?? $sellerPage->youtube_link,
            ]);
        }

        return redirect()->route('seller.dashboard')->with('success', 'Seller profile created successfully.');
    }

    public function edit()
    {
        $profile = Auth::user()->sellerProfile;
        if (!$profile) {
            return redirect()->route('seller.profile.create');
        }
        return view('seller.profile.form', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->sellerProfile;
        if (!$profile) {
            return redirect()->route('seller.profile.create');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'office_phone' => 'nullable|string|max:30',
            'google_map_url' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'facebook_url' => 'nullable|string|max:500',
            'instagram_url' => 'nullable|string|max:500',
            'youtube_url' => 'nullable|string|max:500',
            'google_business_url' => 'nullable|string|max:500',
            'dispatch_radius' => 'nullable|integer|min:0',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
        ]);

        $profile->update($request->all());

        // Sync with SellerPage if exists
        $sellerPage = Auth::user()->sellerPage;
        if ($sellerPage) {
            $sellerPage->update([
                'support_phone' => $request->office_phone ?? $sellerPage->support_phone,
                'google_map_link' => $request->google_map_url ?? $sellerPage->google_map_link,
                'facebook_link' => $request->facebook_url ?? $sellerPage->facebook_link,
                'instagram_link' => $request->instagram_url ?? $sellerPage->instagram_link,
                'youtube_link' => $request->youtube_url ?? $sellerPage->youtube_link,
            ]);
        }

        return redirect()->route('seller.dashboard')->with('success', 'Seller profile updated successfully.');
    }
}