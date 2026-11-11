import sys
import json
import base64
import os
import hashlib
from PIL import Image
import io

def crop_boxes(payload_file):
    with open(payload_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    page_image_base64 = data.get('page_image', '')
    products = data.get('products', [])
    output_dir = data.get('output_dir', '')

    if not page_image_base64 or not products:
        print(json.dumps({'success': False, 'message': 'Missing image or products'}))
        return

    # Decode page image
    if ',' in page_image_base64:
        header, b64_str = page_image_base64.split(',', 1)
    else:
        b64_str = page_image_base64

    img_bytes = base64.b64decode(b64_str)
    full_img = Image.open(io.BytesIO(img_bytes))
    img_w, img_h = full_img.size

    os.makedirs(output_dir, exist_ok=True)

    results = []
    for idx, prod in enumerate(products):
        bbox = prod.get('image_box_percent', None)
        crop_url = None
        crop_filename = None

        if bbox and len(bbox) == 4:
            # bbox is [ymin, xmin, ymax, xmax] in 0-100 percentage
            ymin, xmin, ymax, xmax = bbox
            px_left = max(0, min(int((xmin / 100.0) * img_w), img_w - 1))
            px_top = max(0, min(int((ymin / 100.0) * img_h), img_h - 1))
            px_right = max(px_left + 10, min(int((xmax / 100.0) * img_w), img_w))
            px_bottom = max(px_top + 10, min(int((ymax / 100.0) * img_h), img_h))

            # Crop if dimension valid
            if (px_right - px_left) > 15 and (px_bottom - px_top) > 15:
                crop = full_img.crop((px_left, px_top, px_right, px_bottom))
                
                # Save file
                hash_id = hashlib.md5(f"{idx}_{px_left}_{px_top}_{px_right}_{px_bottom}".encode()).hexdigest()[:10]
                slug = "".join([c if c.isalnum() else "_" for c in prod.get('name', 'crop')]).strip("_")[:25]
                filename = f"crop_p{data.get('page', 1)}_{idx+1}_{slug}_{hash_id}.jpg"
                filepath = os.path.join(output_dir, filename)

                # Convert to RGB if RGBA/P
                if crop.mode in ('RGBA', 'P', 'LA'):
                    background = Image.new('RGB', crop.size, (255, 255, 255))
                    if crop.mode == 'P':
                        crop = crop.convert('RGBA')
                    background.paste(crop, mask=crop.split()[3] if crop.mode == 'RGBA' else None)
                    crop = background
                elif crop.mode != 'RGB':
                    crop = crop.convert('RGB')

                crop.save(filepath, 'JPEG', quality=90)
                crop_url = f"/storage/catalog/crops/{filename}"
                crop_filename = filename

        prod_copy = dict(prod)
        prod_copy['image_url'] = crop_url
        prod_copy['image_filename'] = crop_filename
        results.append(prod_copy)

    print(json.dumps({'success': True, 'products': results}))

if __name__ == '__main__':
    if len(sys.argv) > 1:
        crop_boxes(sys.argv[1])
    else:
        print(json.dumps({'success': False, 'message': 'No input file'}))
