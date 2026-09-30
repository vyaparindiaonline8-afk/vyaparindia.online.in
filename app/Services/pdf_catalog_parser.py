import sys
import os
import json
import re
import fitz  # PyMuPDF

def clean_text(text):
    if not text:
        return ""
    return re.sub(r'\s+', ' ', text).strip()

def is_noise_block(text):
    text_lower = text.lower()
    noise_patterns = [
        r'about\s+us',
        r'company\s+profile',
        r'our\s+vision',
        r'our\s+mission',
        r'chairman\s+message',
        r'director\s+message',
        r'iso\s+9001',
        r'iso\s+\d+',
        r'all\s+rights\s+reserved',
        r'terms\s+and\s+conditions',
        r'established\s+in',
        r'an\s+iso\s+certified',
        r'corporate\s+office',
        r'registered\s+office',
        r'www\.[a-z0-9\-\.]+\.[a-z]{2,}',
        r'toll\s+free',
        r'cin\s*:\s*[a-z0-9]+',
        r'gstin\s*:\s*[a-z0-9]+',
    ]
    for pattern in noise_patterns:
        if re.search(pattern, text_lower):
            return True
    return False

def extract_price(text):
    match = re.search(r'(?:rs\.?|inr|₹|price|rate)\s*:?\s*([\d,]+(?:\.\d{1,2})?)', text, re.IGNORECASE)
    if match:
        try:
            return float(match.group(1).replace(',', ''))
        except:
            pass
    # Standalone price-like numbers
    match2 = re.search(r'\b([\d]{2,6}(?:\.\d{2})?)\s*(?:per|\/-|\b)', text)
    if match2:
        try:
            return float(match2.group(1))
        except:
            pass
    return None

def extract_sizes_and_specs(text):
    # Detect plumbing / hardware / electrical / garment sizes
    sizes = re.findall(r'(\d+(?:/\d+)?(?:\s*-\s*\d+(?:/\d+)?)?\s*(?:inch|"|mm|cm|mtr|ft|kg|gm|ltr|sdr|sch|size))\b', text, re.IGNORECASE)
    grades = re.findall(r'\b(sdr\s*11|sdr\s*13\.5|sdr\s*9|sch\s*40|sch\s*80|grade\s*[a-z0-9]+|class\s*[a-z0-9]+)\b', text, re.IGNORECASE)
    return list(set(sizes)), list(set(grades))

def process_pdf(pdf_path, output_dir, job_id):
    os.makedirs(output_dir, exist_ok=True)
    images_dir = os.path.join(output_dir, f"job_{job_id}_images")
    os.makedirs(images_dir, exist_ok=True)

    doc = fitz.open(pdf_path)
    total_pages = len(doc)

    extracted_images = []
    # 1. Extract all meaningful images
    img_counter = 1
    for page_idx in range(total_pages):
        page = doc[page_idx]
        image_list = page.get_images(full=True)
        for img_info in image_list:
            xref = img_info[0]
            base_image = doc.extract_image(xref)
            image_bytes = base_image["image"]
            image_ext = base_image["ext"]
            width = base_image["width"]
            height = base_image["height"]

            # Filter out tiny icons, spacers, and header lines (under 100x100)
            if width >= 100 and height >= 100:
                img_filename = f"prod_img_{job_id}_{img_counter}.{image_ext}"
                img_save_path = os.path.join(images_dir, img_filename)
                with open(img_save_path, "wb") as f:
                    f.write(image_bytes)
                extracted_images.append({
                    "page": page_idx + 1,
                    "filename": img_filename,
                    "relative_url": f"storage/catalog_extracted/job_{job_id}_images/{img_filename}",
                    "width": width,
                    "height": height
                })
                img_counter += 1

    # 2. Extract Text Blocks & Filter Noise
    products = []
    current_product = None

    for page_idx in range(total_pages):
        page = doc[page_idx]
        text_blocks = page.get_text("blocks")
        
        # Sort blocks top-to-bottom
        text_blocks.sort(key=lambda b: (b[1], b[0]))

        for block in text_blocks:
            block_text = clean_text(block[4])
            if len(block_text) < 4:
                continue

            # Skip noise blocks (About company, certifications, terms)
            if is_noise_block(block_text):
                continue

            # Check if this block looks like a table row (contains multiple pipes, tabs or size keywords)
            lines = [l.strip() for l in block[4].split("\n") if l.strip()]
            
            # Detect Table Grid lines (e.g. Size | Grade | Price | Code)
            table_rows = []
            for line in lines:
                cols = re.split(r'\s{2,}|\t|\|', line)
                if len(cols) >= 2:
                    table_rows.append(cols)

            if len(table_rows) >= 2:
                # This is a variant table
                if current_product:
                    for row in table_rows:
                        row_text = " ".join(row)
                        price = extract_price(row_text)
                        sizes, grades = extract_sizes_and_specs(row_text)
                        var_name = row[0] if row else "Standard"
                        if sizes:
                            var_name = f"{var_name} ({', '.join(sizes)})"
                        if grades:
                            var_name = f"{var_name} [{', '.join(grades)}]"

                        current_product["variants"].append({
                            "variant_name": var_name[:100],
                            "size": sizes[0] if sizes else None,
                            "grade": grades[0] if grades else None,
                            "raw_rate": price or current_product.get("base_price", 100.0),
                            "sku": f"SKU-{re.sub(r'[^A-Za-z0-9]', '', var_name)[:10].upper()}-{len(current_product['variants'])+1}",
                            "stock_quantity": None  # Optional: dale to thik na dale to thik
                        })
                continue

            # Otherwise, detect if this is a product headline / title
            # A product title usually is 6-120 chars
            if 6 <= len(block_text) <= 120 and not block_text.endswith((".", ":")):
                matched_img = None
                for img in extracted_images:
                    if img["page"] == page_idx + 1:
                        matched_img = img["relative_url"]
                        break
                if not matched_img and extracted_images:
                    matched_img = extracted_images[min(len(products), len(extracted_images)-1)]["relative_url"]

                if current_product and (current_product["name"] != block_text):
                    products.append(current_product)

                price_found = extract_price(block_text) or 199.0
                current_product = {
                    "name": block_text,
                    "category": "Industrial & Commercial",
                    "description": "",
                    "base_price": price_found,
                    "mrp": round(price_found * 1.35, 2),
                    "image_url": matched_img,
                    "hsn_code": "39174000",
                    "sku": f"PRD-{re.sub(r'[^A-Za-z0-9]', '', block_text)[:8].upper()}-{len(products)+1}",
                    "variants": [],
                    "stock_quantity": None  # Optional
                }
            elif current_product:
                if len(current_product["description"]) < 500:
                    current_product["description"] += " " + block_text

    if current_product:
        products.append(current_product)

    # Fallback if no specific products isolated
    if not products:
        default_img = extracted_images[0]["relative_url"] if extracted_images else None
        products.append({
            "name": os.path.splitext(os.path.basename(pdf_path))[0].replace("_", " ").title(),
            "category": "General Catalog",
            "description": "Extracted from uploaded commercial catalog brochure.",
            "base_price": 299.0,
            "mrp": 399.0,
            "image_url": default_img,
            "hsn_code": "85176290",
            "sku": f"CAT-{job_id}-01",
            "variants": [
                {"variant_name": "Standard Option", "raw_rate": 299.0, "sku": f"SKU-{job_id}-01", "stock_quantity": None}
            ],
            "stock_quantity": None
        })

    # Ensure every product has at least 1 variant
    for p in products:
        if not p["variants"]:
            p["variants"].append({
                "variant_name": "Standard (Base)",
                "raw_rate": p["base_price"],
                "sku": f"{p['sku']}-STD",
                "stock_quantity": None
            })

    output_data = {
        "job_id": job_id,
        "total_products": len(products),
        "total_images_extracted": len(extracted_images),
        "products": products
    }

    output_json_path = os.path.join(output_dir, f"job_{job_id}_extracted.json")
    with open(output_json_path, "w", encoding="utf-8") as f:
        json.dump(output_data, f, indent=2, ensure_ascii=False)

    print(json.dumps({"success": True, "output_json": output_json_path, "products_count": len(products), "images_count": len(extracted_images)}))

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print(json.dumps({"success": False, "error": "Usage: python extract_catalog.py <pdf_path> <output_dir> <job_id>"}))
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    output_dir = sys.argv[2]
    job_id = sys.argv[3]
    try:
        process_pdf(pdf_path, output_dir, job_id)
    except Exception as e:
        print(json.dumps({"success": False, "error": str(e)}))
        sys.exit(1)