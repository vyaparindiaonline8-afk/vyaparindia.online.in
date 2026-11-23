<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'कृपया सेलर डैशबोर्ड या दुकान एडिट करने के लिए पहले लॉगिन करें।');
        }

        if (Auth::user()->is_seller()) {
            return $next($request);
        }

        return redirect('/')->with('error', 'आपके पास सेलर अनुमतियां नहीं हैं।');
    }
}
