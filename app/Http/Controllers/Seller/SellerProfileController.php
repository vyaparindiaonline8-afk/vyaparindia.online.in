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
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:191',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
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

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = \App\Services\CloudinaryService::upload($request->file('background_image'), 'vyaparindia/seller/backgrounds');
        }

        $validated = $this->sanitizeSocialLinks($validated);

        $profile = Auth::user()->sellerProfile()->create($validated);

        // Sync with SellerPage if exists
        $sellerPage = Auth::user()->sellerPage;
        if ($sellerPage) {
            $updates = [
                'support_phone' => $request->office_phone ?? $sellerPage->support_phone,
                'google_map_link' => $validated['google_map_url'] ?? $sellerPage->google_map_link,
                'facebook_link' => $validated['facebook_url'] ?? $sellerPage->facebook_link,
                'instagram_link' => $validated['instagram_url'] ?? $sellerPage->instagram_link,
                'youtube_link' => $validated['youtube_url'] ?? $sellerPage->youtube_link,
            ];
            if (!empty($validated['whatsapp_number'])) {
                $updates['whatsapp_number'] = $validated['whatsapp_number'];
            }
            if (!empty($validated['support_email'])) {
                $updates['support_email'] = $validated['support_email'];
            }
            $sellerPage->update($updates);
        }

        return redirect()->route('seller.dashboard')->with('success', 'Seller profile created successfully.');
    }

    public function edit()
    {
        $profile = Auth::user()->sellerProfile;
        if (!$profile) {
            return redirect()->route('seller.profile.create');
        }

        // Fetch existing connected channels for Store Names
        $userChannels = \App\Models\Channel::where('user_id', Auth::id())
            ->get()
            ->keyBy('channel_name');

        return view('seller.profile.form', compact('profile', 'userChannels'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->sellerProfile;
        if (!$profile) {
            return redirect()->route('seller.profile.create');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'support_email' => 'nullable|email|max:191',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
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

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = \App\Services\CloudinaryService::upload($request->file('background_image'), 'vyaparindia/seller/backgrounds');
        }

        $validated = $this->sanitizeSocialLinks($validated);

        $profile->update($validated);

        // 🛍️ Sync / Save Multi-Channel Store Display Names
        if ($request->has('channel_stores') && is_array($request->input('channel_stores'))) {
            foreach ($request->input('channel_stores') as $chName => $storeName) {
                if (!empty($storeName) && in_array($chName, ['amazon', 'flipkart', 'meesho', 'shopify', 'woocommerce', 'myntra', 'blinkit'])) {
                    $channel = \App\Models\Channel::firstOrNew([
                        'user_id' => Auth::id(),
                        'channel_name' => $chName,
                    ]);
                    $channel->store_name = trim($storeName);
                    $channel->is_active = true;
                    if (!$channel->exists) {
                        $channel->auto_sync_inventory = true;
                    }
                    $channel->save();
                }
            }
        }

        // Sync with SellerPage if exists
        $sellerPage = Auth::user()->sellerPage;
        if ($sellerPage) {
            $updates = [
                'support_phone' => $request->office_phone ?? $sellerPage->support_phone,
                'google_map_link' => $validated['google_map_url'] ?? $sellerPage->google_map_link,
                'facebook_link' => $validated['facebook_url'] ?? $sellerPage->facebook_link,
                'instagram_link' => $validated['instagram_url'] ?? $sellerPage->instagram_link,
                'youtube_link' => $validated['youtube_url'] ?? $sellerPage->youtube_link,
            ];
            if (!empty($validated['whatsapp_number'])) {
                $updates['whatsapp_number'] = $validated['whatsapp_number'];
            }
            if (!empty($validated['support_email'])) {
                $updates['support_email'] = $validated['support_email'];
            }
            $sellerPage->update($updates);
        }

        return redirect()->route('seller.dashboard')->with('success', 'Seller profile updated successfully.');
    }

    /**
     * Auto sanitize handle or raw username inputs to full HTTPS URLs
     */
    protected function sanitizeSocialLinks(array $data): array
    {
        if (!empty($data['instagram_url'])) {
            $handle = trim($data['instagram_url']);
            $handle = preg_replace('#^https?://(www\.)?instagram\.com/#i', '', $handle);
            $handle = ltrim($handle, '@');
            $handle = rtrim($handle, '/');
            if (!empty($handle)) {
                $data['instagram_url'] = 'https://instagram.com/' . $handle;
            }
        }
        if (!empty($data['facebook_url'])) {
            $handle = trim($data['facebook_url']);
            $handle = preg_replace('#^https?://(www\.)?facebook\.com/#i', '', $handle);
            $handle = ltrim($handle, '@');
            $handle = rtrim($handle, '/');
            if (!empty($handle)) {
                $data['facebook_url'] = 'https://facebook.com/' . $handle;
            }
        }
        if (!empty($data['youtube_url'])) {
            $handle = trim($data['youtube_url']);
            $handle = preg_replace('#^https?://(www\.)?youtube\.com/#i', '', $handle);
            $handle = rtrim($handle, '/');
            if (!empty($handle)) {
                if (!str_starts_with($handle, '@') && !str_starts_with($handle, 'channel/') && !str_starts_with($handle, 'c/')) {
                    $handle = '@' . $handle;
                }
                $data['youtube_url'] = 'https://youtube.com/' . $handle;
            }
        }
        return $data;
    }
}