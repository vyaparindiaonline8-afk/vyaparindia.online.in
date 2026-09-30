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
            'dispatch_radius' => 'nullable|integer|min:0',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
        ]);

        Auth::user()->sellerProfile()->create($request->all());

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
            'dispatch_radius' => 'nullable|integer|min:0',
            'upi_id' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc' => 'nullable|string|max:20',
            'bank_account_holder' => 'nullable|string|max:150',
        ]);

        $profile->update($request->all());

        return redirect()->route('seller.dashboard')->with('success', 'Seller profile updated successfully.');
    }
}