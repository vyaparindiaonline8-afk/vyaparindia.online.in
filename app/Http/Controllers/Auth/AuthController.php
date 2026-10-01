<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $roleParam = $request->query('role');
        $defaultRole = ($roleParam === 'buyer') ? 2 : 3; // Default to Seller (3)
        return view('auth.register', compact('defaultRole'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'in:2,3'], // 2 for buyer, 3 for seller
            'firm_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        // Auto initialize seller records if registering as seller
        if ($user->role_id == 3) {
            $companyName = trim($request->firm_name) ?: ($user->name . ' Enterprises');
            $city = trim($request->city);
            $pincode = trim($request->pincode);

            \App\Models\SellerProfile::firstOrCreate(['user_id' => $user->id], [
                'company_name' => $companyName,
                'city' => $city,
            ]);
            
            $baseSlug = \Illuminate\Support\Str::slug($companyName);
            $slug = $baseSlug ?: 'store';
            if (\App\Models\SellerPage::where('slug', $slug)->exists()) {
                $slug .= '-' . $user->id;
            }

            \App\Models\SellerPage::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'slug' => $slug,
                    'page_title' => $companyName,
                    'city' => $city,
                    'pincode' => $pincode,
                    'welcome_message' => 'Welcome to our verified store on VyaparIndia!',
                ]
            );
        }

        // Send branded welcome email via Brevo SMTP
        try {
            $companyName = $request->firm_name ?: ($user->name . ' Enterprises');
            $storeUrl = ($user->role_id == 3 && isset($slug)) ? url('/store/' . $slug) : null;

            \Illuminate\Support\Facades\Mail::send('emails.welcome', [
                'user' => $user,
                'companyName' => $companyName,
                'storeUrl' => $storeUrl,
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('VyaparIndia में आपका स्वागत है! 🎉 Welcome to VyaparIndia');
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Registration welcome email failed: ' . $e->getMessage());
        }

        Auth::login($user);

        // Redirect based on role
        if ($user->is_seller()) {
            return redirect()->route('seller.dashboard');
        }
        return redirect()->route('buyer.dashboard');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->is_admin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_seller()) {
                return redirect()->route('seller.dashboard');
            } else {
                return redirect()->route('buyer.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
