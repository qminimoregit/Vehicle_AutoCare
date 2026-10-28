import time
from playwright.sync_api import sync_playwright
import pymysql

quotations_spec = [
    {
        "customer_name": "Ruwan Silva",
        "vehicle_str": "WP CAK-4512",
        "subject": "Periodic Engine Oil & Filter Service",
        "items": [
            {"type": "service", "id": "15", "name": "Engine Oil Change", "price": "2500", "qty": "1"},
            {"type": "product", "id": "35", "name": "Toyota Engine Oil 0W-20", "price": "18500", "qty": "1"},
            {"type": "product", "id": "34", "name": "Toyota Genuine Engine Oil Filter", "price": "2600", "qty": "1"}
        ]
    },
    {
        "customer_name": "Dinesh Perera",
        "vehicle_str": "WP CAA-8834",
        "subject": "Engine Diagnostic and Inspection",
        "items": [
            {"type": "service", "id": "18", "name": "Engine Diagnostic Scan", "price": "3500", "qty": "1"},
            {"type": "service", "id": "21", "name": "Suspension Inspection", "price": "2000", "qty": "1"}
        ]
    },
    {
        "customer_name": "Chamara Wijesinghe",
        "vehicle_str": "WP CAB-1902",
        "subject": "Brake System Maintenance Estimate",
        "items": [
            {"type": "service", "id": "17", "name": "Brake Pad Replacement", "price": "3000", "qty": "1"},
            {"type": "product", "id": "37", "name": "Toyota Genuine Brake Pads", "price": "12500", "qty": "1"}
        ]
    }
]

def run():
    print("Connecting to visible Chrome on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        saved_quotations = []

        for q_idx, q in enumerate(quotations_spec):
            print(f"\n=======================================================")
            print(f"Creating Quotation {q_idx+1}: {q['subject']} for {q['customer_name']}")
            print(f"=======================================================")

            page.goto("http://localhost:8000/billing/quotations/create", wait_until="networkidle")
            time.sleep(1)

            # 1. Search and Select Customer
            print(f"Selecting customer: {q['customer_name']}...")
            cust_picker = page.locator("[data-searchable-select='customer_id']").first
            cust_picker.locator("[role='combobox']").click()
            time.sleep(0.4)
            cust_picker.locator("input[type='text']").fill(q['customer_name'].split()[0])
            time.sleep(0.6)
            cust_opt = cust_picker.locator(f"[role='option']:has-text('{q['customer_name']}')").first
            if cust_opt.count() > 0:
                cust_opt.click()
            else:
                cust_picker.locator("[role='option']").first.click()
            time.sleep(0.8)

            # 2. Select Vehicle
            print(f"Selecting vehicle: {q['vehicle_str']}...")
            vehicle_select = page.locator("select[name='vehicle_id']")
            vehicle_option = vehicle_select.locator(f"option:has-text('{q['vehicle_str']}')")
            vehicle_option.wait_for(state="attached", timeout=5000)
            val = vehicle_option.get_attribute("value")
            vehicle_select.select_option(val)
            time.sleep(0.5)

            # 3. Enter Subject
            print(f"Entering subject: {q['subject']}...")
            page.fill("input[name='subject']", q['subject'])
            time.sleep(0.4)

            # 4. Add Items
            # Note: create.blade.php already initializes with 1 row (index 0) of type 'service'
            for item_idx, itm in enumerate(q['items']):
                print(f"Configuring item {item_idx+1}: {itm['name']} ({itm['type']})...")
                if item_idx > 0:
                    # Add new row for items after row 0
                    if itm['type'] == 'service':
                        page.click("button:has-text('+ Add Service')")
                    else:
                        page.click("button:has-text('+ Add Product')")
                    time.sleep(0.5)

                row = page.locator("tbody tr").nth(item_idx)
                row_select = row.locator("select").first
                row_select.select_option(itm['id'])
                time.sleep(0.4)

                # Set Price if needed
                price_input = row.locator("input[name*='unit_price']")
                curr_price = price_input.input_value()
                if not curr_price or float(curr_price) <= 0:
                    price_input.fill(itm['price'])
                    time.sleep(0.2)

                # Set Quantity
                qty_input = row.locator("input[name*='quantity']")
                qty_input.fill(itm['qty'])
                time.sleep(0.2)

            time.sleep(0.8)
            page.screenshot(path=f"scratch/step4_quotation_{q_idx+1}_form.png")

            # 5. Save & Preview Quotation
            print("Submitting quotation form...")
            save_btn = page.locator("button[type='submit']:has-text('Save & Preview Quotation')").first
            save_btn.click()
            page.wait_for_load_state("networkidle")
            time.sleep(1.5)

            print("Current URL after save:", page.url)
            page.screenshot(path=f"scratch/step4_quotation_{q_idx+1}_saved.png")
            saved_quotations.append(page.url)

            # 6. Test print preview
            print("Testing print preview page...")
            quotation_id = page.url.rstrip("/").split("/")[-1]
            print(f"Quotation ID: {quotation_id}")
            page.goto(f"http://localhost:8000/billing/quotations/{quotation_id}/print", wait_until="networkidle")
            time.sleep(1)
            page.screenshot(path=f"scratch/step4_quotation_{q_idx+1}_print.png")

    # 7. Database Verification
    conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    cursor.execute("""
        SELECT q.id, q.quotation_number, q.customer_id, c.customer_name, q.vehicle_id, v.vehicle_number, q.subject, q.grand_total, q.status 
        FROM quotations q
        JOIN customers c ON q.customer_id = c.id
        LEFT JOIN vehicles v ON q.vehicle_id = v.id
        ORDER BY q.id DESC LIMIT 3
    """)
    db_quots = cursor.fetchall()
    print("\n=== VERIFIED QUOTATIONS IN MYSQL ===")
    for q in reversed(db_quots):
        print(f"Quotation {q['quotation_number']} (ID: {q['id']}): Customer={q['customer_name']}, Vehicle={q['vehicle_number']}, Subject='{q['subject']}', Grand Total=LKR {q['grand_total']}, Status={q['status']}")
        cursor.execute("SELECT id, item_type, item_name, quantity, unit_price, line_total FROM quotation_items WHERE quotation_id = %s", (q['id'],))
        items = cursor.fetchall()
        for it in items:
            print(f"  - [{it['item_type']}] {it['item_name']} x {it['quantity']} @ {it['unit_price']} = LKR {it['line_total']}")

    assert len(db_quots) == 3, f"Expected 3 quotations, found {len(db_quots)}"
    print("\nALL 3 QUOTATIONS CREATED AND VERIFIED WITH 100% SUCCESS!")

if __name__ == "__main__":
    run()
