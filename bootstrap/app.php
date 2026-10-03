<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'is_admin' => \App\Middleware\IsAdmin::class,
            'is_seller' => \App\Middleware\IsSeller::class,
            'is_buyer' => \App\Middleware\IsBuyer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'CSRF token mismatch. Please refresh.'], 419);
            }
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', 'Aapka session expire ho gaya tha ya page purana tha. Kripya dobara submit karein.');
        });

        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            return response(
                "<div style='font-family:monospace;background:#0d1117;color:#58a6ff;padding:24px;border-radius:12px;margin:20px;'>" .
                "<h2 style='color:#f85149;margin-top:0;'>⚠️ Application Error Encountered</h2>" .
                "<p style='color:#e6edf3;font-size:16px;'><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
                "<p style='color:#8b949e;'><strong>Location:</strong> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>" .
                "<pre style='background:#161b22;color:#c9d1d9;padding:16px;border-radius:8px;overflow:auto;max-height:400px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>" .
                "</div>",
                500
            );
        });
    })->create();
