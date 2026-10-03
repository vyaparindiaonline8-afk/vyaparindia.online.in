<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    'encrypt' => env('SESSION_ENCRYPT', false),

    'files' => (function() {
        $path = storage_path('framework/sessions');
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }
        return $path;
    })(),

    'connection' => env('SESSION_CONNECTION'),

    'table' => env('SESSION_TABLE', 'sessions'),

    'store' => env('SESSION_STORE'),

    'lottery' => [2, 100],

    'cookie' => env('SESSION_COOKIE', 'vyaparindia_session'),

    'path' => env('SESSION_PATH', '/'),

    // Fix string "null" from .env so it evaluates to actual null
    'domain' => (env('SESSION_DOMAIN') === 'null' || !env('SESSION_DOMAIN')) ? null : env('SESSION_DOMAIN'),

    // Force secure cookies over HTTPS
    'secure' => env('SESSION_SECURE_COOKIE', true),

    'http_only' => env('SESSION_HTTP_ONLY', true),

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
