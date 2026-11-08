#!/usr/bin/env python3
"""
VyaparIndia AI Image Enhancer Studio
- 4X HD Super-Resolution Upscaling (Lanczos)
- Edge Sharpening & Denoising (Unsharp Mask)
- Pure Studio White Background (#FFFFFF)
- Text/Artifact Cleaning & 1200x1200px Amazon/Meesho Framing
"""

import sys
import os
import argparse
import json
import requests
import numpy as np
from PIL import Image, ImageEnhance, ImageFilter
from io import BytesIO

def enhance_image(input_source, output_path, upscale_target=1200, bg_white=True, sharpen=True):
    try:
        # 1. Load Image (from URL or local path)
        if input_source.startswith("http://") or input_source.startswith("https://"):
            headers = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"}
            resp = requests.get(input_source, headers=headers, timeout=25)
            if resp.status_code != 200:
                return {"success": False, "error": f"Failed to download image (HTTP {resp.status_code})"}
            img = Image.open(BytesIO(resp.content)).convert("RGB")
        else:
            if not os.path.exists(input_source):
                return {"success": False, "error": f"Local file not found: {input_source}"}
            img = Image.open(input_source).convert("RGB")

        orig_w, orig_h = img.size

        # 2. HD Super-Resolution Upscaling (Lanczos)
        max_dim = max(orig_w, orig_h)
        scale = max(float(upscale_target) / float(max_dim), 1.0)
        if scale > 1.0:
            new_w, new_h = int(orig_w * scale), int(orig_h * scale)
            img = img.resize((new_w, new_h), Image.Resampling.LANCZOS)
        else:
            new_w, new_h = orig_w, orig_h

        # 3. Smart Sharpening & Contrast (Removes PDF scan blur)
        if sharpen:
            # Unsharp mask to make blurry edges razor-sharp
            img = img.filter(ImageFilter.UnsharpMask(radius=2, percent=155, threshold=3))
            
            # Sharpness boost
            enhancer = ImageEnhance.Sharpness(img)
            img = enhancer.enhance(1.35)

            # Contrast boost to clear milky grey PDF haze
            contrast = ImageEnhance.Contrast(img)
            img = contrast.enhance(1.12)

        # 4. Pure White Studio Background (#FFFFFF)
        if bg_white:
            arr = np.array(img).astype(np.float32)
            # Brightness and saturation calculation
            brightness = np.mean(arr, axis=2)
            dist_to_white = np.sqrt(np.sum((arr - 255.0) ** 2, axis=2))
            
            # Identify background pixels (light background / slight grey gradient)
            is_bg = (brightness > 195) & (dist_to_white < 95)
            arr[is_bg] = [255.0, 255.0, 255.0]
            cleaned_img = Image.fromarray(np.uint8(arr))

            # Auto-frame into Amazon/Meesho 1200x1200px square canvas with 8% safe margin
            canvas_w, canvas_h = upscale_target, upscale_target
            final_canvas = Image.new("RGB", (canvas_w, canvas_h), (255, 255, 255))
            
            # Scale to fit 1060px inner area
            inner_max = int(upscale_target * 0.88)
            cw, ch = cleaned_img.size
            fit_ratio = min(inner_max / float(cw), inner_max / float(ch))
            scaled_w, scaled_h = int(cw * fit_ratio), int(ch * fit_ratio)
            scaled_product = cleaned_img.resize((scaled_w, scaled_h), Image.Resampling.LANCZOS)
            
            # Center on canvas
            pos_x = (canvas_w - scaled_w) // 2
            pos_y = (canvas_h - scaled_h) // 2
            final_canvas.paste(scaled_product, (pos_x, pos_y))
            result_img = final_canvas
        else:
            result_img = img

        # 5. Save Output
        os.makedirs(os.path.dirname(os.path.abspath(output_path)), exist_ok=True)
        result_img.save(output_path, "JPEG", quality=95, optimize=True)

        return {
            "success": True,
            "output_path": output_path,
            "original_size": [orig_w, orig_h],
            "enhanced_size": list(result_img.size),
            "file_size": os.path.getsize(output_path)
        }

    except Exception as e:
        return {"success": False, "error": str(e)}

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="VyaparIndia AI Image Enhancer")
    parser.add_argument("--input", required=True, help="Input image URL or local path")
    parser.add_argument("--output", required=True, help="Output image file path")
    parser.add_argument("--target", type=int, default=1200, help="Target dimension (e.g. 1200)")
    parser.add_argument("--no-bg", action="store_true", help="Disable white background")
    parser.add_argument("--no-sharpen", action="store_true", help="Disable sharpening")

    args = parser.parse_args()
    res = enhance_image(
        input_source=args.input,
        output_path=args.output,
        upscale_target=args.target,
        bg_white=not args.no_bg,
        sharpen=not args.no_sharpen
    )
    print(json.dumps(res))
