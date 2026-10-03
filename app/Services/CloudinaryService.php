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
    public static function getCredentials(): array
    {
        $cloudName = config('cloudinary.cloud_name') ?: env('CLOUDINARY_CLOUD_NAME');
        $apiKey = config('cloudinary.api_key') ?: env('CLOUDINARY_API_KEY');
        $apiSecret = config('cloudinary.api_secret') ?: env('CLOUDINARY_API_SECRET');

        if ((!$cloudName || !$apiKey || !$apiSecret) && (env('CLOUDINARY_URL') ?: config('cloudinary.url'))) {
            $parsed = parse_url(env('CLOUDINARY_URL') ?: config('cloudinary.url'));
            if ($parsed && isset($parsed['host'])) {
                $cloudName = $cloudName ?: $parsed['host'];
                $apiKey = $apiKey ?: ($parsed['user'] ?? null);
                $apiSecret = $apiSecret ?: ($parsed['pass'] ?? null);
            }
        }

        return [$cloudName, $apiKey, $apiSecret];
    }

    public static function upload($file, string $folder = 'products'): ?string
    {
        if (!$file) {
            return null;
        }

        [$cloudName, $apiKey, $apiSecret] = self::getCredentials();

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

    /**
     * Upload a Base64 data URI image (e.g. data:image/jpeg;base64,...) directly to Cloudinary.
     *
     * @param string $base64Data
     * @param string $folder
     * @return string|null
     */
    public static function uploadBase64(string $base64Data, string $folder = 'vyaparindia/catalog/crops'): ?string
    {
        [$cloudName, $apiKey, $apiSecret] = self::getCredentials();

        if ($cloudName && $apiKey && $apiSecret) {
            try {
                $timestamp = time();
                $toSign = "folder={$folder}&timestamp={$timestamp}" . $apiSecret;
                $signature = sha1($toSign);

                $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->post($url, [
                        'file' => $base64Data, // Cloudinary natively accepts base64 data URI
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

                Log::warning('Cloudinary base64 upload returned non-success response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Cloudinary base64 upload exception: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * List resources from Cloudinary Admin API
     *
     * @param string $prefix
     * @param int $maxResults
     * @return array
     */
    public static function listResources(string $prefix = '', int $maxResults = 500): array
    {
        [$cloudName, $apiKey, $apiSecret] = self::getCredentials();

        if (!$cloudName || !$apiKey || !$apiSecret) {
            return [];
        }

        try {
            $allResources = [];
            $nextCursor = null;

            do {
                $url = "https://api.cloudinary.com/v1_1/{$cloudName}/resources/image?max_results=" . min(500, $maxResults) . "&type=upload";
                if (!empty($prefix)) {
                    $url .= "&prefix=" . urlencode($prefix);
                }
                if ($nextCursor) {
                    $url .= "&next_cursor=" . urlencode($nextCursor);
                }

                $response = Http::timeout(25)
                    ->withoutVerifying()
                    ->withBasicAuth($apiKey, $apiSecret)
                    ->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    $resources = $data['resources'] ?? [];
                    $allResources = array_merge($allResources, $resources);
                    $nextCursor = $data['next_cursor'] ?? null;
                } else {
                    Log::warning('Cloudinary listResources failed', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    break;
                }
            } while ($nextCursor && count($allResources) < $maxResults);

            return $allResources;
        } catch (\Throwable $e) {
            Log::error('Cloudinary listResources exception: ' . $e->getMessage());
        }

        return [];
    }
}
