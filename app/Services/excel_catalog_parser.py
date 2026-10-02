import sys
import os
import json
import re
import openpyxl

def extract_size_from_name(name):
    match = re.search(r'(\d+(?:\.\d+)?(?:\s*X\s*\d+(?:\.\d+)?)?\s*(?:MM|FEET|FT|MTR|KG|INCH|"|\'))', name, re.IGNORECASE)
    if match:
        return match.group(1).strip()
    return "Standard"

def extract_base_name(name):
    # Remove sizes, lengths, standards from name to get the parent family
    clean = name
    patterns = [
        r'\b\d+(?:\.\d+)?\s*X\s*\d+(?:\.\d+)?\s*MM\b',
        r'\b\d+(?:\.\d+)?\s*MM\b',
        r'\b\d+\s*FT\b',
        r'\b\d+\s*FEET\b',
        r'\b\d+\s*MTR\b',
        r'\b\d+\s*KG\b',
        r'\b\d+/\d+["\']*\b',
        r'\b\d+["\']\b',
        r'\b10\s*FEET\b',
        r'\bSDR\s*\d+(\.\d+)?\b',
        r'\bSCH\s*\d+\b',
    ]
    for p in patterns:
        clean = re.sub(p, '', clean, flags=re.IGNORECASE)
    clean = re.sub(r'\s+', ' ', clean).strip(' -*')
    return clean if len(clean) >= 2 else name

def get_image_for_product(name, category):
    n = name.upper()
    cat = (category or "").upper()
    
    if 'AGRI SOLVENT' in n:
        return 'images/catalog/plasto/items/agri_solvent.jpg', 22
    if 'SOLVENT' in n or 'CEMENT' in n:
        return 'images/catalog/plasto/items/solvent_cement.jpg', 21
    if 'SWR PIPE' in n or ('SWR' in cat and 'PIPE' in n):
        return 'images/catalog/plasto/items/swr_pipe.jpg', 15
    if any(k in n for k in ['SINGALE TEE', 'SINGLE TEE', 'DOUBALE TEE', 'DOUBLE TEE', 'REDUSING TEE']):
        return 'images/catalog/plasto/items/swr_single_tee.jpg', 16
    if any(k in n for k in ['BEND 87.5', 'SHOE BEND', 'LONG BEND']):
        return 'images/catalog/plasto/items/swr_bend.jpg', 16
    if 'VENT COWL' in n:
        return 'images/catalog/plasto/items/swr_vent_cowl.jpg', 17
    if any(k in n for k in ['TRAP', 'NAHANI', 'FLOOR TRAP']):
        return 'images/catalog/plasto/items/nahani_trap.jpg', 17
    if 'RUBBER RING' in n:
        return 'images/catalog/plasto/items/circuit_plug.jpg', 18

    # Brass
    if 'BRASS ELBOW' in n:
        return 'images/catalog/plasto/items/brass_elbow.jpg', 7
    if 'BRASS TEE' in n:
        return 'images/catalog/plasto/items/brass_tee.jpg', 7
    if 'BRASS FTA' in n or 'HEXA BRASS FTA' in n:
        return 'images/catalog/plasto/items/brass_fta.jpg', 7
    if 'BRASS MTA' in n or 'HEXA BRASS MTA' in n:
        return 'images/catalog/plasto/items/brass_mta.jpg', 7
    if '3 IN 1' in n:
        return 'images/catalog/plasto/items/brass_tee.jpg', 7

    # Valves
    if 'BALL VALVE' in n and 'UPVC' in n:
        return 'images/catalog/plasto/items/upvc_ball_valve.jpg', 11
    if 'BALL VALVE' in n or 'VALVE' in n:
        return 'images/catalog/plasto/items/cpvc_ball_valve.jpg', 6

    # Pipes
    if 'CPVC PIPE' in n or ('CPVC' in cat and 'PIPE' in n):
        return 'images/catalog/plasto/items/cpvc_pipe.jpg', 5
    if 'UPVC PIPE' in n or ('UPVC' in cat and 'PIPE' in n):
        return 'images/catalog/plasto/items/upvc_pipe.jpg', 2

    # Fittings
    if 'PIPE CLIP' in n:
        return 'images/catalog/plasto/items/pipe_clip.jpg', 12
    if 'TANK NIPPALE' in n or 'TANK NIPPLE' in n:
        return 'images/catalog/plasto/items/cpvc_tank_nipple.jpg', 6
    if 'UNION' in n:
        return 'images/catalog/plasto/items/cpvc_union.jpg', 6
    if 'END CAP' in n:
        return 'images/catalog/plasto/items/end_cap.jpg', 6
    if 'ELBOW' in n or '90*' in n:
        if 'CPVC' in cat or 'CPVC' in n:
            return 'images/catalog/plasto/items/cpvc_elbow.jpg', 6
        return 'images/catalog/plasto/items/upvc_elbow.jpg', 3
    if 'TEE' in n:
        if 'CPVC' in cat or 'CPVC' in n:
            return 'images/catalog/plasto/items/cpvc_tee.jpg', 6
        return 'images/catalog/plasto/items/upvc_tee.jpg', 3
    if 'COUPLER' in n or 'SOCKET' in n:
        if 'CPVC' in cat or 'CPVC' in n:
            return 'images/catalog/plasto/items/cpvc_coupler.jpg', 6
        return 'images/catalog/plasto/items/upvc_coupler.jpg', 3
    if 'MTA' in n:
        return 'images/catalog/plasto/items/upvc_mta.jpg', 4
    if 'FTA' in n:
        return 'images/catalog/plasto/items/cpvc_fta.jpg', 4
    if 'GARDEN' in n:
        return 'images/catalog/plasto/items/garden_pipe.jpg', 13

    if 'CPVC' in cat:
        return 'images/catalog/plasto/items/cpvc_coupler.jpg', 6
    return 'images/catalog/plasto/items/upvc_elbow.jpg', 3

def process_excel(excel_path, output_dir, job_id):
    os.makedirs(output_dir, exist_ok=True)
    
    wb = openpyxl.load_workbook(excel_path, data_only=True)
    sheet = wb.active

    rows = list(sheet.iter_rows(values_only=True))
    if not rows:
        raise ValueError("Excel file is empty")

    header = [str(c or '').strip().upper() for c in rows[0]]
    
    # Identify key column indexes
    def find_col(candidates):
        for c in candidates:
            for idx, h in enumerate(header):
                if c in h:
                    return idx
        return None

    code_idx = find_col(['ITAM CODE', 'ITEM CODE', 'CODE', 'SKU']) or 0
    name_idx = find_col(['PRODUCT NAME', 'ITEM NAME', 'NAME', 'DESCRIPTION']) or 1
    mrp_idx = find_col(['MRP', 'LIST PRICE', 'RATE']) or 8
    less_idx = find_col(['LESS', 'DISCOUNT', 'TRADE DISCOUNT']) or 9
    gst_idx = find_col(['GST', 'TAX', 'VAT']) or 10
    pcost_idx = find_col(['P.COST', 'PURCHASE COST', 'COST', 'PURCHASE RATE']) or 11
    sale_a_idx = find_col(['SALE A', 'RETAIL', 'PRICE A', 'COUNTER']) or 12
    sale_c_idx = find_col(['SALE C', 'WHOLESALE', 'PRICE C', 'DEALER']) or 14
    category_idx = find_col(['CATEGORY', 'GROUP']) or 16
    stock_idx = find_col(['OPN.STOCK', 'STOCK', 'QTY', 'QUANTITY']) or 19
    hsn_idx = find_col(['HSN CODE', 'HSN']) or 7
    image_idx = find_col(['PRODUCT_IMAGE', 'IMAGE_URL', 'IMAGE', 'PHOTO'])

    # Group products by Base Name
    groups = {}

    for r in rows[1:]:
        if not r or len(r) <= name_idx or not r[name_idx]:
            continue

        raw_name = str(r[name_idx]).strip()
        item_code = str(r[code_idx]).strip() if code_idx < len(r) and r[code_idx] is not None else f"SKU-{len(groups)+1}"
        mrp = float(r[mrp_idx]) if mrp_idx < len(r) and r[mrp_idx] is not None else 100.0
        less = float(r[less_idx]) if less_idx < len(r) and r[less_idx] is not None else 0.0
        gst = float(r[gst_idx]) if gst_idx < len(r) and r[gst_idx] is not None else 18.0
        pcost = float(r[pcost_idx]) if pcost_idx < len(r) and r[pcost_idx] is not None else round(mrp * (1 - less/100), 2)
        retail_price = float(r[sale_a_idx]) if sale_a_idx < len(r) and r[sale_a_idx] is not None else round(pcost * 1.35, 2)
        wholesale_price = float(r[sale_c_idx]) if sale_c_idx < len(r) and r[sale_c_idx] is not None else round(pcost * 1.15, 2)
        category = str(r[category_idx]).strip() if category_idx < len(r) and r[category_idx] is not None else "Plumbing & Hardware"
        stock = int(r[stock_idx]) if stock_idx < len(r) and r[stock_idx] is not None and str(r[stock_idx]).isdigit() else None
        hsn = str(r[hsn_idx]).strip() if hsn_idx < len(r) and r[hsn_idx] is not None else "3917"

        # Explicit image or auto-matched
        if image_idx is not None and image_idx < len(r) and r[image_idx]:
            img_url = str(r[image_idx]).strip()
            page_no = 1
        else:
            img_url, page_no = get_image_for_product(raw_name, category)

        base_name = extract_base_name(raw_name)
        size_name = extract_size_from_name(raw_name)

        if base_name not in groups:
            groups[base_name] = {
                "name": base_name,
                "category": category,
                "description": f"High durability {base_name} for plumbing and construction installations.",
                "base_price": mrp,
                "mrp": mrp,
                "image_url": img_url,
                "catalog_page": f"Page {page_no}",
                "hsn_code": hsn,
                "sku": f"PRD-{re.sub(r'[^A-Za-z0-9]', '', base_name)[:8].upper()}",
                "trade_discount_percent": less,
                "gst_percent": gst,
                "stock_quantity": stock,
                "variants": []
            }

        groups[base_name]["variants"].append({
            "variant_name": size_name if size_name != "Standard" else raw_name,
            "size": size_name,
            "grade": "Standard",
            "raw_rate": mrp,
            "sku": item_code,
            "trade_discount_percent": less,
            "gst_percent": gst,
            "landing_cost_without_gst": round(pcost, 2),
            "landing_cost_with_gst": round(pcost * (1 + gst/100), 2),
            "wholesale_price": round(wholesale_price, 2),
            "retail_price": round(retail_price, 2),
            "mrp": round(mrp, 2),
            "stock_quantity": stock
        })

    products_list = list(groups.values())

    output_data = {
        "job_id": job_id,
        "total_products": len(products_list),
        "total_images_extracted": len(set(p["image_url"] for p in products_list if p.get("image_url"))),
        "products": products_list
    }

    output_json_path = os.path.join(output_dir, f"job_{job_id}_extracted.json")
    with open(output_json_path, "w", encoding="utf-8") as f:
        json.dump(output_data, f, indent=2, ensure_ascii=False)

    print(json.dumps({
        "success": True,
        "output_json": output_json_path,
        "products_count": len(products_list),
        "variants_count": sum(len(p["variants"]) for p in products_list)
    }))

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print(json.dumps({"success": False, "error": "Usage: python excel_catalog_parser.py <excel_path> <output_dir> <job_id>"}))
        sys.exit(1)
    
    excel_path = sys.argv[1]
    output_dir = sys.argv[2]
    job_id = sys.argv[3]
    try:
        process_excel(excel_path, output_dir, job_id)
    except Exception as e:
        print(json.dumps({"success": False, "error": str(e)}))
        sys.exit(1)
