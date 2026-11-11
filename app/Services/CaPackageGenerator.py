import sys
import json
import os
import openpyxl
from openpyxl.styles import Font, PatternFill, Alignment, Border, Side
from openpyxl.utils import get_column_letter

def build_ca_package(payload_path, output_path):
    with open(payload_path, 'r', encoding='utf-8') as f:
        data = json.load(f)

    seller_info = data.get('seller_info', {})
    summary = data.get('summary', {})
    sales = data.get('sales', [])
    expenses = data.get('expenses', [])

    wb = openpyxl.Workbook()

    # Font definitions
    font_title = Font(name='Segoe UI', size=14, bold=True, color='FFFFFF')
    font_subtitle = Font(name='Segoe UI', size=10, italic=True, color='E0E7FF')
    font_header = Font(name='Segoe UI', size=10, bold=True, color='FFFFFF')
    font_bold = Font(name='Segoe UI', size=10, bold=True, color='1E293B')
    font_regular = Font(name='Segoe UI', size=9, color='334155')
    font_mono = Font(name='Consolas', size=9, color='0F172A')

    # Border definitions
    thin_border = Border(
        left=Side(style='thin', color='E2E8F0'),
        right=Side(style='thin', color='E2E8F0'),
        top=Side(style='thin', color='E2E8F0'),
        bottom=Side(style='thin', color='E2E8F0')
    )

    # -------------------------------------------------------------
    # TAB 1: Outward Supplies (GSTR-1 Format)
    # -------------------------------------------------------------
    ws1 = wb.active
    ws1.title = "GSTR-1 Outward Supplies"
    ws1.views.sheetView[0].showGridLines = True

    # Title Banner
    ws1.merge_cells("A1:N1")
    title_cell = ws1["A1"]
    title_cell.value = f"GSTR-1 Outward E-Commerce Sales Register: {seller_info.get('company_name', 'VyaparIndia Store')} (Period: {data.get('period_label', 'All Time')})"
    title_cell.font = font_title
    title_cell.fill = PatternFill(start_color="1E1B4B", end_color="1E1B4B", fill_type="solid")
    title_cell.alignment = Alignment(horizontal="left", vertical="center", indent=1)
    ws1.row_dimensions[1].height = 36

    ws1.merge_cells("A2:N2")
    sub_cell = ws1["A2"]
    sub_cell.value = f"GSTIN: {seller_info.get('gst_number') or 'Unregistered / None'} | State: {seller_info.get('state', 'India')} | Scheme: {seller_info.get('tax_scheme_label', 'Regular')}"
    sub_cell.font = font_subtitle
    sub_cell.fill = PatternFill(start_color="312E81", end_color="312E81", fill_type="solid")
    sub_cell.alignment = Alignment(horizontal="left", vertical="center", indent=1)
    ws1.row_dimensions[2].height = 20

    headers1 = [
        "Invoice / Order No", "Invoice Date", "Sales Channel", "Customer Name",
        "Place of Supply (POS)", "Supply Type", "HSN Code", "Product Items",
        "Qty", "Taxable Value (₹)", "GST Rate", "CGST (₹)", "SGST (₹)", "IGST (₹)", "Total Invoice (₹)"
    ]
    ws1.row_dimensions[4].height = 26
    fill_h1 = PatternFill(start_color="4338CA", end_color="4338CA", fill_type="solid")

    for col_idx, h in enumerate(headers1, 1):
        cell = ws1.cell(row=4, column=col_idx, value=h)
        cell.font = font_header
        cell.fill = fill_h1
        cell.alignment = Alignment(horizontal="center", vertical="center", wrap_text=True)

    r_idx = 5
    for s in sales:
        ws1.row_dimensions[r_idx].height = 20
        row_vals = [
            s.get("order_number", ""),
            s.get("date", ""),
            s.get("channel", "Direct"),
            s.get("customer_name", "Retail Customer"),
            s.get("pos_state", ""),
            s.get("supply_type", "Intra-State"),
            s.get("hsn", "39174000"),
            s.get("product_name", "Hardware Item"),
            s.get("quantity", 1),
            round(float(s.get("taxable_value", 0)), 2),
            f"{s.get('gst_rate', 18)}%",
            round(float(s.get("cgst", 0)), 2),
            round(float(s.get("sgst", 0)), 2),
            round(float(s.get("igst", 0)), 2),
            round(float(s.get("total_amount", 0)), 2)
        ]
        for c_idx, val in enumerate(row_vals, 1):
            c = ws1.cell(row=r_idx, column=c_idx, value=val)
            c.font = font_regular if c_idx not in (1, 10, 15) else (font_mono if c_idx == 1 else font_bold)
            c.border = thin_border
            if c_idx in (10, 12, 13, 14, 15):
                c.number_format = '#,##0.00'
                c.alignment = Alignment(horizontal="right", vertical="center")
            elif c_idx in (2, 5, 6, 7, 9, 11):
                c.alignment = Alignment(horizontal="center", vertical="center")
            else:
                c.alignment = Alignment(horizontal="left", vertical="center")
        r_idx += 1

    # Total Row for Tab 1
    ws1.row_dimensions[r_idx].height = 24
    total_fill = PatternFill(start_color="F1F5F9", end_color="F1F5F9", fill_type="solid")
    ws1.cell(row=r_idx, column=1, value="TOTALS").font = font_bold
    ws1.cell(row=r_idx, column=1).alignment = Alignment(horizontal="center")
    for c_idx in range(1, 16):
        c = ws1.cell(row=r_idx, column=c_idx)
        c.fill = total_fill
        c.font = font_bold
        c.border = thin_border

    if len(sales) > 0:
        ws1.cell(row=r_idx, column=9, value=f"=SUM(I5:I{r_idx-1})")
        ws1.cell(row=r_idx, column=10, value=f"=SUM(J5:J{r_idx-1})").number_format = '#,##0.00'
        ws1.cell(row=r_idx, column=12, value=f"=SUM(L5:L{r_idx-1})").number_format = '#,##0.00'
        ws1.cell(row=r_idx, column=13, value=f"=SUM(M5:M{r_idx-1})").number_format = '#,##0.00'
        ws1.cell(row=r_idx, column=14, value=f"=SUM(N5:N{r_idx-1})").number_format = '#,##0.00'
        ws1.cell(row=r_idx, column=15, value=f"=SUM(O5:O{r_idx-1})").number_format = '#,##0.00'

    # -------------------------------------------------------------
    # TAB 2: Platform Expenses & ITC Claim (GSTR-2B / 3B)
    # -------------------------------------------------------------
    ws2 = wb.create_sheet(title="Platform Fees & ITC Claim")
    ws2.views.sheetView[0].showGridLines = True

    ws2.merge_cells("A1:J1")
    t2 = ws2["A1"]
    t2.value = f"E-Commerce Platform Fees & Input Tax Credit (ITC) Register: {seller_info.get('company_name', 'Store')}"
    t2.font = font_title
    t2.fill = PatternFill(start_color="064E3B", end_color="064E3B", fill_type="solid")
    t2.alignment = Alignment(horizontal="left", vertical="center", indent=1)
    ws2.row_dimensions[1].height = 36

    ws2.merge_cells("A2:J2")
    sub2 = ws2["A2"]
    sub2.value = "Eligible ITC claimed on Amazon / Meesho / Flipkart commission & courier fees (To be reported in GSTR-3B Table 4A)"
    sub2.font = font_subtitle
    sub2.fill = PatternFill(start_color="065F46", end_color="065F46", fill_type="solid")
    sub2.alignment = Alignment(horizontal="left", vertical="center", indent=1)
    ws2.row_dimensions[2].height = 20

    headers2 = [
        "Date", "Service Provider (Supplier)", "Expense / Fee Type", "Reference Order / Invoice",
        "Net Taxable Fee (₹)", "GST Rate", "Eligible ITC (18% GST ₹)", "1% TCS Deducted (₹)",
        "Total Deducted by Platform (₹)", "Eligible Scheme Note"
    ]
    fill_h2 = PatternFill(start_color="047857", end_color="047857", fill_type="solid")
    ws2.row_dimensions[4].height = 26

    for col_idx, h in enumerate(headers2, 1):
        cell = ws2.cell(row=4, column=col_idx, value=h)
        cell.font = font_header
        cell.fill = fill_h2
        cell.alignment = Alignment(horizontal="center", vertical="center", wrap_text=True)

    r_idx2 = 5
    for exp in expenses:
        ws2.row_dimensions[r_idx2].height = 20
        row_vals2 = [
            exp.get("date", ""),
            exp.get("provider", "Amazon Seller Services Pvt Ltd"),
            exp.get("type", "Commission"),
            exp.get("ref", ""),
            round(float(exp.get("taxable_fee", 0)), 2),
            "18%",
            round(float(exp.get("itc_amount", 0)), 2),
            round(float(exp.get("tcs_amount", 0)), 2),
            round(float(exp.get("total_fee", 0)), 2),
            exp.get("note", "Eligible for ITC in Regular GST")
        ]
        for c_idx, val in enumerate(row_vals2, 1):
            c = ws2.cell(row=r_idx2, column=c_idx, value=val)
            c.font = font_regular if c_idx not in (5, 7, 9) else font_bold
            c.border = thin_border
            if c_idx in (5, 7, 8, 9):
                c.number_format = '#,##0.00'
                c.alignment = Alignment(horizontal="right", vertical="center")
            elif c_idx in (1, 6):
                c.alignment = Alignment(horizontal="center", vertical="center")
            else:
                c.alignment = Alignment(horizontal="left", vertical="center")
        r_idx2 += 1

    ws2.row_dimensions[r_idx2].height = 24
    ws2.cell(row=r_idx2, column=1, value="TOTALS").font = font_bold
    ws2.cell(row=r_idx2, column=1).alignment = Alignment(horizontal="center")
    for c_idx in range(1, 11):
        c = ws2.cell(row=r_idx2, column=c_idx)
        c.fill = total_fill
        c.font = font_bold
        c.border = thin_border

    if len(expenses) > 0:
        ws2.cell(row=r_idx2, column=5, value=f"=SUM(E5:E{r_idx2-1})").number_format = '#,##0.00'
        ws2.cell(row=r_idx2, column=7, value=f"=SUM(G5:G{r_idx2-1})").number_format = '#,##0.00'
        ws2.cell(row=r_idx2, column=8, value=f"=SUM(H5:H{r_idx2-1})").number_format = '#,##0.00'
        ws2.cell(row=r_idx2, column=9, value=f"=SUM(I5:I{r_idx2-1})").number_format = '#,##0.00'

    # -------------------------------------------------------------
    # TAB 3: CA Executive Summary (Net Computation)
    # -------------------------------------------------------------
    ws3 = wb.create_sheet(title="CA Executive Summary")
    ws3.views.sheetView[0].showGridLines = True

    ws3.merge_cells("A1:F1")
    t3 = ws3["A1"]
    t3.value = "CA Executive Tax Computation & Reconciliation Summary"
    t3.font = font_title
    t3.fill = PatternFill(start_color="581C87", end_color="581C87", fill_type="solid")
    t3.alignment = Alignment(horizontal="left", vertical="center", indent=1)
    ws3.row_dimensions[1].height = 36

    ws3.merge_cells("A2:F2")
    sub3 = ws3["A2"]
    sub3.value = "Ready for direct monthly/quarterly GST return filing (GSTR-1, GSTR-3B & CMP-08)"
    sub3.font = font_subtitle
    sub3.fill = PatternFill(start_color="6B21A8", end_color="6B21A8", fill_type="solid")
    sub3.alignment = Alignment(horizontal="left", vertical="center", indent=1)
    ws3.row_dimensions[2].height = 20

    summary_rows = [
        ("Business Profile Details", ""),
        ("Trade / Company Name", seller_info.get("company_name", "VyaparIndia Seller")),
        ("GSTIN / Enrolment ID", seller_info.get("gst_number") or seller_info.get("gst_enrolment_id") or "Unregistered / None"),
        ("Registered State", seller_info.get("state", "India")),
        ("Current Tax Scheme", seller_info.get("tax_scheme_label", "Regular GST Dealer")),
        ("Filing Period / Range", data.get("period_label", "All Time")),
        ("", ""),
        ("E-Commerce Sales & Output Tax Summary", ""),
        ("Total Gross Orders Processed", summary.get("total_orders", 0)),
        ("Total Gross Turnover / Invoice Value (₹)", round(float(summary.get("gross_sales", 0)), 2)),
        ("Taxable Value of Outward Supplies (₹)", round(float(summary.get("taxable_sales", 0)), 2)),
        ("Output CGST Liability (₹)", round(float(summary.get("total_cgst", 0)), 2)),
        ("Output SGST Liability (₹)", round(float(summary.get("total_sgst", 0)), 2)),
        ("Output IGST Liability (₹)", round(float(summary.get("total_igst", 0)), 2)),
        ("TOTAL OUTPUT TAX PAYABLE (A) (₹)", round(float(summary.get("total_output_tax", 0)), 2)),
        ("", ""),
        ("Input Tax Credit (ITC) & TCS Credits Available", ""),
        ("Platform Commission & Courier Net Expenses (₹)", round(float(summary.get("total_platform_expenses", 0)), 2)),
        ("ELIGIBLE INPUT TAX CREDIT (ITC) CLAIMABLE (B) (₹)", round(float(summary.get("total_itc_claimable", 0)), 2)),
        ("1% TCS / TDS Deducted by Marketplaces (C) (₹)", round(float(summary.get("total_tcs_deducted", 0)), 2)),
        ("", ""),
        ("Final Tax Position to be Filed by CA", ""),
        ("NET TAX PAYABLE IN CASH [(A) - (B)] (₹)", round(float(summary.get("net_tax_payable", 0)), 2)),
        ("TCS Cash Ledger Credit Available to Claim (₹)", round(float(summary.get("total_tcs_deducted", 0)), 2)),
        ("CA Next Steps / Recommendation", summary.get("ca_recommendation", "Directly file GSTR-1 using Sheet 1 and claim ITC via Sheet 2."))
    ]

    for s_idx, (k, v) in enumerate(summary_rows, 4):
        ws3.row_dimensions[s_idx].height = 22
        cell_k = ws3.cell(row=s_idx, column=2, value=k)
        cell_v = ws3.cell(row=s_idx, column=4, value=v)

        if k in ("Business Profile Details", "E-Commerce Sales & Output Tax Summary", "Input Tax Credit (ITC) & TCS Credits Available", "Final Tax Position to be Filed by CA"):
            ws3.merge_cells(start_row=s_idx, start_column=2, end_row=s_idx, end_column=5)
            cell_k.font = Font(name='Segoe UI', size=11, bold=True, color='1E1B4B')
            cell_k.fill = PatternFill(start_color="EDE9FE", end_color="EDE9FE", fill_type="solid")
            cell_k.alignment = Alignment(horizontal="left", vertical="center", indent=1)
        elif "TOTAL" in k or "NET" in k or "ELIGIBLE" in k:
            cell_k.font = Font(name='Segoe UI', size=10, bold=True, color='0F172A')
            cell_v.font = Font(name='Segoe UI', size=11, bold=True, color='4338CA')
            cell_k.fill = PatternFill(start_color="F8FAFC", end_color="F8FAFC", fill_type="solid")
            cell_v.fill = PatternFill(start_color="F1F5F9", end_color="F1F5F9", fill_type="solid")
            if isinstance(v, (int, float)):
                cell_v.number_format = '₹#,##0.00'
        else:
            cell_k.font = font_regular
            cell_v.font = font_bold
            if isinstance(v, (int, float)):
                cell_v.number_format = '₹#,##0.00' if '₹' in k else '#,##0'

    # Auto-fit column widths across sheets
    for ws in [ws1, ws2, ws3]:
        for col in ws.columns:
            max_len = 0
            col_letter = get_column_letter(col[0].column)
            for cell in col:
                val_str = str(cell.value or '')
                if len(val_str) > max_len and not cell.coordinate in ws.merged_cells:
                    max_len = len(val_str)
            ws.column_dimensions[col_letter].width = max(max_len + 3, 12)

    os.makedirs(os.path.dirname(output_path), exist_ok=True)
    wb.save(output_path)
    print(json.dumps({"success": True, "file": output_path}))

if __name__ == '__main__':
    if len(sys.argv) > 2:
        build_ca_package(sys.argv[1], sys.argv[2])
    else:
        print(json.dumps({"success": False, "message": "Usage: python CaPackageGenerator.py payload.json output.xlsx"}))
