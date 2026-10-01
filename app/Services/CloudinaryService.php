<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload an image to Cloudinary or fallback to local disk
     *
     * @param  UploadedFile|string  $file
     * @param  string  $folder
     * @return string|null URL or relative path of uploaded image
     */
    public static function upload($file, string $folder = 'products'): ?string
    {
        if (!$file) {
            return null;
        }

        $cloudName = config('cloudinary.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');
        $apiKey = config('cloudinary.api_key') ?: env('CLOUDINARY_API_KEY');
        $apiSecret = config('cloudinary.api_secret') ?: env('CLOUDINARY_API_SECRET');

        // If Cloudinary credentials are configured, upload to Cloudinary
        if ($cloudName && $apiKey && $apiSecret) {
            try {
                $timestamp = time();
                $toSign = "folder={$folder}&timestamp={$timestamp}" . $apiSecret;
                $signature = sha1($toSign);

                $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

                $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
                $fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
                $fileContents = file_get_contents($filePath);

                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->attach('file', $fileContents, $fileName)
                    ->post($url, [
                        'api_key' => $apiKey,
                        'timestamp' => $timestamp,
                        'folder' => $folder,
                        'signature' => $signature,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['secure_url'])) {
                        return $data['secure_url'];
                    }
                }

                Log::warning('Cloudinary upload returned non-success response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Cloudinary upload exception: ' . $e->getMessage());
            }
        }

        // Fallback: local storage
        if ($file instanceof UploadedFile) {
            return $file->store($folder, 'public');
        }

        return null;
    }
}
