<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->alias([
            'is_admin' => \App\Middleware\IsAdmin::class,
            'is_seller' => \App\Middleware\IsSeller::class,
            'is_buyer' => \App\Middleware\IsBuyer::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/webhooks/*',
            'webhooks/*',
            'ai-copilot/voice-webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Please refresh.'], 419);
            }
            return redirect()->route('login')
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Aapka session expire ho gaya tha (Page Expired). Kripya dobara login karein.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'CSRF token mismatch. Please refresh.'], 419);
                }
                return redirect()->route('login')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Aapka session expire ho gaya tha (Page Expired). Kripya dobara login karein.');
            }
        });
    })->create();
