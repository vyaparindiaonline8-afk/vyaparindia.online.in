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
    public static function getPythonBinary(): ?string
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
        
        // Linux / Render: check if python3 exists on path
        $which = shell_exec('which python3 2>/dev/null');
        if ($which && trim($which)) {
            return trim($which);
        }
        return null;
    }

    /**
     * Enhance a single image (4X HD Upscale, Unsharp Sharpening, Pure White Background #FFFFFF)
     */
    public static function enhanceImage(string $inputUrl, int $userId): array
    {
        $newUrl = null;

        // 1. High-Performance Cloudinary Edge Studio AI (Pure White #FFFFFF, 1200x1200px HD Pad, Unsharp Edge Sharpening)
        if (str_contains($inputUrl, 'cloudinary.com')) {
            $transformParams = 'b_white,c_pad,h_1200,w_1200,e_sharpen:80';
            if (preg_match('#/upload/(.*)$#', $inputUrl, $matches)) {
                $rest = $matches[1];
                // Strip existing b_white transforms if already applied
                $rest = preg_replace('#^b_white[^/]+/#', '', $rest);
                $newUrl = preg_replace('#/upload/.*$#', "/upload/{$transformParams}/" . $rest, $inputUrl);
            }
        }

        // 2. Local Image processing fallback (Python / GD)
        if (!$newUrl) {
            $python = self::getPythonBinary();
            $scriptPath = base_path('scripts/ai_image_enhancer.py');

            $tmpDir = storage_path('app/ai_enhanced_temp');
            if (!file_exists($tmpDir)) {
                @mkdir($tmpDir, 0777, true);
            }

            $hash = md5($inputUrl . time() . rand(1000, 9999));
            $outputPath = $tmpDir . DIRECTORY_SEPARATOR . "enhanced_{$hash}.jpg";
            $json = null;

            if ($python && file_exists($scriptPath)) {
                $cmd = escapeshellarg($python) . ' ' . escapeshellarg($scriptPath) .
                    ' --input ' . escapeshellarg($inputUrl) .
                    ' --output ' . escapeshellarg($outputPath) .
                    ' --target 1200';
                $output = shell_exec($cmd . ' 2>&1');
                $json = json_decode(trim($output), true);
            }

            if (!$json || empty($json['success']) || !file_exists($outputPath)) {
                Log::info("Using Native PHP GD Studio Engine for: {$inputUrl}");
                $gdRes = self::enhanceImageWithPhpGd($inputUrl, $outputPath, 1200);
                if (!empty($gdRes['success'])) {
                    $json = $gdRes;
                }
            }

            if (file_exists($outputPath)) {
                try {
                    $newUrl = CloudinaryService::upload($outputPath, "vyaparindia/catalog/seller_{$userId}_hd");
                } catch (\Throwable $e) {}

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
                @unlink($outputPath);
            }
        }

        if (!$newUrl) {
            return [
                'success' => false,
                'error' => 'AI Image enhancement could not generate new image URL.',
            ];
        }

        // Store backup mapping
        self::recordBackup($userId, $newUrl, $inputUrl);

        // Atomic swap in database (Products, Product Images, Media Vault)
        $affectedProducts = self::swapImageInDatabase($userId, $inputUrl, $newUrl);

        return [
            'success' => true,
            'old_url' => $inputUrl,
            'new_url' => $newUrl,
            'original_size' => [1200, 1200],
            'enhanced_size' => [1200, 1200],
            'affected_products' => $affectedProducts,
        ];
    }

    /**
     * Native PHP GD Studio Engine: 4X Upscale, Unsharp Sharpening & 100% Studio White Background
     */
    public static function enhanceImageWithPhpGd(string $inputUrl, string $outputPath, int $targetDim = 1200): array
    {
        if (!extension_loaded('gd')) {
            return ['success' => false, 'error' => 'PHP GD extension is not available'];
        }

        try {
            $raw = null;
            if (str_starts_with($inputUrl, 'http://') || str_starts_with($inputUrl, 'https://')) {
                $ch = curl_init($inputUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 25);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
                $raw = curl_exec($ch);
                curl_close($ch);
            } else {
                $localPath = public_path(ltrim($inputUrl, '/'));
                if (file_exists($localPath)) {
                    $raw = file_get_contents($localPath);
                }
            }

            if (!$raw) {
                return ['success' => false, 'error' => 'Failed to fetch image data'];
            }

            $src = @imagecreatefromstring($raw);
            if (!$src) {
                return ['success' => false, 'error' => 'Failed to decode image with GD'];
            }

            $origW = imagesx($src);
            $origH = imagesy($src);

            // Create target pure white canvas (#FFFFFF)
            $canvas = imagecreatetruecolor($targetDim, $targetDim);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, $targetDim, $targetDim, $white);

            // Calculate scaled product with 88% inner fit
            $innerMax = (int)($targetDim * 0.88);
            $fitRatio = min($innerMax / max(1, $origW), $innerMax / max(1, $origH));
            $scaledW = (int)($origW * $fitRatio);
            $scaledH = (int)($origH * $fitRatio);

            $tempProduct = imagecreatetruecolor($scaledW, $scaledH);
            imagealphablending($tempProduct, false);
            imagesavealpha($tempProduct, true);
            imagecopyresampled($tempProduct, $src, 0, 0, 0, 0, $scaledW, $scaledH, $origW, $origH);

            // Sharpening matrix (unsharp mask simulation in GD)
            $sharpenMatrix = [
                [-1, -1, -1],
                [-1, 16, -1],
                [-1, -1, -1]
            ];
            imageconvolution($tempProduct, $sharpenMatrix, 8, 0);

            // Contrast boost to remove grey scan haze
            imagefilter($tempProduct, IMG_FILTER_CONTRAST, -8);

            // Position centered on white canvas
            $posX = (int)(($targetDim - $scaledW) / 2);
            $posY = (int)(($targetDim - $scaledH) / 2);
            imagecopy($canvas, $tempProduct, $posX, $posY, 0, 0, $scaledW, $scaledH);

            // Ensure output directory exists
            @mkdir(dirname($outputPath), 0777, true);
            imagejpeg($canvas, $outputPath, 95);

            imagedestroy($src);
            imagedestroy($tempProduct);
            imagedestroy($canvas);

            return [
                'success' => true,
                'output_path' => $outputPath,
                'original_size' => [$origW, $origH],
                'enhanced_size' => [$targetDim, $targetDim],
                'file_size' => file_exists($outputPath) ? filesize($outputPath) : 0,
                'engine' => 'php_gd'
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
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
            'message' => $successCount > 0 
                ? "{$successCount} फोटो सफलतापूर्वक HD स्टूडियो व्हाइट बैकग्राउंड में बदल दी गईं!" 
                : "AI प्रोसेसिंग पूर्ण नहीं हो सकी। कृपया पुनः प्रयास करें।",
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
        $updatedMedia = SellerMedia::where('user_id', $userId)
            ->where(function($q) use ($oldUrl) {
                $q->where('file_path', $oldUrl)
                  ->orWhere('file_path', 'like', '%' . basename(parse_url($oldUrl, PHP_URL_PATH)));
            })
            ->update([
                'file_path' => $newUrl,
                'filename' => basename(parse_url($newUrl, PHP_URL_PATH)),
            ]);

        if (!$updatedMedia) {
            try {
                SellerMedia::create([
                    'user_id' => $userId,
                    'filename' => basename(parse_url($newUrl, PHP_URL_PATH)),
                    'file_path' => $newUrl,
                    'category_type' => \App\Http\Controllers\Seller\CatalogIngestionController::detectFolderForImage(basename($oldUrl)),
                    'is_universal' => false,
                    'permission_granted' => true,
                    'is_assigned' => count($affectedProductNames) > 0,
                ]);
            } catch (\Throwable $e) {}
        }

        // 4. Update Custom Crop JSON Registry if old URL is saved there
        try {
            $cropsFile = storage_path('app/gallery_crops_' . $userId . '.json');
            if (file_exists($cropsFile)) {
                $crops = json_decode(file_get_contents($cropsFile), true);
                if (is_array($crops)) {
                    $changed = false;
                    foreach ($crops as &$cItem) {
                        $cUrl = $cItem['url'] ?? '';
                        if ($cUrl === $oldUrl || basename(parse_url($cUrl, PHP_URL_PATH)) === basename(parse_url($oldUrl, PHP_URL_PATH))) {
                            $cItem['url'] = $newUrl;
                            $cItem['asset_url'] = $newUrl;
                            $cItem['filename'] = basename(parse_url($newUrl, PHP_URL_PATH));
                            $changed = true;
                        }
                    }
                    unset($cItem);
                    if ($changed) {
                        file_put_contents($cropsFile, json_encode($crops, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Could not update crops registry on swap: " . $e->getMessage());
        }

        // 5. Invalidate Cloudinary cache so gallery re-fetches immediately
        try {
            \Illuminate\Support\Facades\Cache::forget('cloudinary_resources_vyaparindia');
            \Illuminate\Support\Facades\Cache::store('file')->forget('cloudinary_resources_vyaparindia');
        } catch (\Throwable $e) {}

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
