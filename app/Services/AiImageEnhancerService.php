<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SellerMedia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiImageEnhancerService
{
    /**
     * Locate python executable (Windows or Linux / Render)
     */
    public static function getPythonBinary(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            if (file_exists('C:\\Python313\\python.exe')) {
                return 'C:\\Python313\\python.exe';
            }
            if (file_exists('C:\\Python312\\python.exe')) {
                return 'C:\\Python312\\python.exe';
            }
            return 'python';
        }
        return 'python3';
    }

    /**
     * Enhance a single image (4X HD Upscale, Unsharp Sharpening, Pure White Background #FFFFFF)
     */
    public static function enhanceImage(string $inputUrl, int $userId): array
    {
        $python = self::getPythonBinary();
        $scriptPath = base_path('scripts/ai_image_enhancer.py');

        if (!file_exists($scriptPath)) {
            return ['success' => false, 'error' => 'AI enhancer script missing: ' . $scriptPath];
        }

        $tmpDir = storage_path('app/ai_enhanced_temp');
        if (!file_exists($tmpDir)) {
            @mkdir($tmpDir, 0777, true);
        }

        $hash = md5($inputUrl . time() . rand(1000, 9999));
        $outputPath = $tmpDir . DIRECTORY_SEPARATOR . "enhanced_{$hash}.jpg";

        $cmd = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) .
            ' --input ' . escapeshellarg($inputUrl) .
            ' --output ' . escapeshellarg($outputPath) .
            ' --target 1200';

        $output = shell_exec($cmd . ' 2>&1');
        $json = json_decode(trim($output), true);

        if (!$json || empty($json['success']) || !file_exists($outputPath)) {
            Log::warning("AI Enhance execution error: {$output}");
            return [
                'success' => false,
                'error' => $json['error'] ?? 'AI enhancement failed: ' . substr($output, 0, 200)
            ];
        }

        // Upload enhanced image to Cloudinary CDN
        $newUrl = null;
        try {
            $newUrl = CloudinaryService::upload($outputPath, "vyaparindia/catalog/seller_{$userId}_hd");
        } catch (\Throwable $e) {
            Log::warning("Cloudinary upload error in AiImageEnhancer: " . $e->getMessage());
        }

        // Fallback to local public storage if Cloudinary fails
        if (!$newUrl) {
            $publicTargetDir = public_path('storage/enhanced_catalog/seller_' . $userId);
            if (!file_exists($publicTargetDir)) {
                @mkdir($publicTargetDir, 0777, true);
            }
            $publicFilename = "hd_{$hash}.jpg";
            $publicPath = $publicTargetDir . DIRECTORY_SEPARATOR . $publicFilename;
            @copy($outputPath, $publicPath);
            $newUrl = asset("storage/enhanced_catalog/seller_{$userId}/{$publicFilename}");
        }

        // Clean up temporary local file
        @unlink($outputPath);

        // Store backup mapping
        self::recordBackup($userId, $newUrl, $inputUrl);

        // Atomic swap in database (Products, Product Images, Media Vault)
        $affectedProducts = self::swapImageInDatabase($userId, $inputUrl, $newUrl);

        return [
            'success' => true,
            'old_url' => $inputUrl,
            'new_url' => $newUrl,
            'original_size' => $json['original_size'] ?? [],
            'enhanced_size' => $json['enhanced_size'] ?? [1200, 1200],
            'affected_products' => $affectedProducts,
        ];
    }

    /**
     * Enhance an entire folder/category of images
     */
    public static function enhanceFolder(string $folderName, int $userId): array
    {
        $allImages = \App\Http\Controllers\Seller\CatalogIngestionController::getAllGalleryImages($userId);
        
        $folderImages = [];
        $isAll = in_array(strtoupper(trim($folderName)), ['ALL', 'EVERYTHING', '']);

        foreach ($allImages as $img) {
            $f = trim($img['folder'] ?? $img['category'] ?? 'General');
            if ($isAll || strcasecmp($f, trim($folderName)) === 0) {
                $folderImages[] = $img['url'];
            }
        }

        $folderImages = array_values(array_unique(array_filter($folderImages)));

        if (empty($folderImages)) {
            return [
                'success' => false,
                'message' => "कैटेगरी '{$folderName}' में कोई इमेज नहीं मिली।"
            ];
        }

        $results = [];
        $successCount = 0;
        $totalAffectedProducts = 0;

        foreach ($folderImages as $imgUrl) {
            $res = self::enhanceImage($imgUrl, $userId);
            if (!empty($res['success'])) {
                $successCount++;
                $totalAffectedProducts += count($res['affected_products'] ?? []);
                $results[] = $res;
            }
        }

        return [
            'success' => $successCount > 0,
            'folder' => $folderName,
            'total_found' => count($folderImages),
            'success_count' => $successCount,
            'affected_products_count' => $totalAffectedProducts,
            'results' => $results,
        ];
    }

    /**
     * Atomic swap: update old image URL to new clean HD image URL across all models
     */
    public static function swapImageInDatabase(int $userId, string $oldUrl, string $newUrl): array
    {
        $affectedProductNames = [];

        // 1. Update Product main cover image
        $products = Product::where('user_id', $userId)
            ->where(function($q) use ($oldUrl) {
                $q->where('image', $oldUrl)
                  ->orWhere('image', 'like', '%' . basename(parse_url($oldUrl, PHP_URL_PATH)));
            })->get();

        foreach ($products as $p) {
            $p->image = $newUrl;
            // Also store original in attributes for direct rollback
            $attrs = $p->attributes ?? [];
            if (!isset($attrs['original_image'])) {
                $attrs['original_image'] = $oldUrl;
            }
            $p->attributes = $attrs;
            $p->save();
            $affectedProductNames[] = $p->name;
        }

        // 2. Update Product gallery images
        ProductImage::whereHas('product', fn($q) => $q->where('user_id', $userId))
            ->where('image_path', $oldUrl)
            ->update(['image_path' => $newUrl]);

        // 3. Update Media Vault
        SellerMedia::where('user_id', $userId)
            ->where('file_path', $oldUrl)
            ->update(['file_path' => $newUrl]);

        return $affectedProductNames;
    }

    /**
     * 1-Click Rollback: revert from new clean image back to the original backup
     */
    public static function revertImage(string $currentUrl, int $userId): array
    {
        $backupFile = storage_path("app/ai_enhancements_backup_{$userId}.json");
        if (!file_exists($backupFile)) {
            return ['success' => false, 'message' => 'कोई बैकअप रिकॉर्ड नहीं मिला।'];
        }

        $registry = json_decode(file_get_contents($backupFile), true) ?: [];
        $originalUrl = $registry[$currentUrl] ?? null;

        if (!$originalUrl) {
            return ['success' => false, 'message' => 'इस इमेज की ओरिजिनल फोटो नहीं मिली।'];
        }

        // Restore in database
        self::swapImageInDatabase($userId, $currentUrl, $originalUrl);

        // Remove from registry
        unset($registry[$currentUrl]);
        file_put_contents($backupFile, json_encode($registry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return [
            'success' => true,
            'message' => 'मूल (Original) फोटो सफलतापूर्वक वापस रीस्टोर कर दी गई है!',
            'restored_url' => $originalUrl,
        ];
    }

    /**
     * Record backup mapping in JSON file
     */
    private static function recordBackup(int $userId, string $newUrl, string $originalUrl): void
    {
        $backupFile = storage_path("app/ai_enhancements_backup_{$userId}.json");
        $registry = [];
        if (file_exists($backupFile)) {
            $registry = json_decode(file_get_contents($backupFile), true) ?: [];
        }
        $registry[$newUrl] = $originalUrl;
        file_put_contents($backupFile, json_encode($registry, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
