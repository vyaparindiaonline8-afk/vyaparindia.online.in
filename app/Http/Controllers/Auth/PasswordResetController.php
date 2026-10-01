<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'कृपया अपना रजिस्टर्ड ईमेल एड्रेस दर्ज करें।',
            'email.email' => 'कृपया एक वैध ईमेल एड्रेस दर्ज करें।',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'यह ईमेल एड्रेस हमारे रिकॉर्ड्स में मौजूद नहीं है। कृपया सही ईमेल दर्ज करें या नया रजिस्ट्रेशन करें।'
            ])->withInput();
        }

        $token = Str::random(64);

        // Store hashed token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        try {
            Mail::send('emails.reset_password', ['resetUrl' => $resetUrl, 'email' => $request->email], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('VyaparIndia - पासवर्ड रीसेट लिंक (Password Reset Link)');
            });

            return back()->with('status', 'पासवर्ड रीसेट लिंक आपके ईमेल पर भेज दिया गया है। कृपया अपना इनबॉक्स या स्पैम फ़ोल्डर चेक करें।');
        } catch (\Throwable $e) {
            Log::error('Password reset email error: ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'ईमेल भेजने में समस्या आई। कृपया थोड़ी देर बाद पुनः प्रयास करें।'
            ]);
        }
    }

    public function showResetPasswordForm(string $token, Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'कृपया नया पासवर्ड दर्ज करें।',
            'password.min' => 'पासवर्ड कम से कम 8 अक्षरों का होना चाहिए।',
            'password.confirmed' => 'पासवर्ड कन्फर्मेशन मेल नहीं खा रहा है।',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return back()->withErrors(['email' => 'अमान्य या समाप्त हो चुका रीसेट टोकन। कृपया दोबारा रीसेट लिंक मंगवाएं।']);
        }

        // Check expiry (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'यह रीसेट लिंक समाप्त (Expire) हो चुका है। कृपया नया लिंक मंगवाएं।']);
        }

        // Verify token hash
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'अमान्य पासवर्ड रीसेट टोकन।']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'आपका पासवर्ड सफलतापूर्वक बदल दिया गया है! अब आप नए पासवर्ड से लॉगिन कर सकते हैं।');
    }
}
