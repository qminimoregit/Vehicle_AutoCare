import time
from playwright.sync_api import sync_playwright
import pymysql

def run():
    print("Connecting to visible Chrome on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        # Auto-accept any confirmation dialogs
        page.on("dialog", lambda dialog: dialog.accept())

        print("Navigating to /inventory/grn/create...")
        page.goto("http://localhost:8000/inventory/grn/create", wait_until="networkidle")
        time.sleep(1)

        # 1. Select Supplier: Colombo Motor Spares
        print("Selecting supplier 'Colombo Motor Spares'...")
        supp_picker = page.locator("[data-searchable-select='supplier_id']").first
        supp_picker.locator("[role='combobox']").click()
        time.sleep(0.4)
        supp_picker.locator("input[type='text']").fill("Colombo")
        time.sleep(0.6)
        supp_option = supp_picker.locator("[role='option']:has-text('Colombo Motor Spares')").first
        if supp_option.count() > 0:
            supp_option.click()
        else:
            page.locator("[role='option']").first.click()
        time.sleep(0.5)

        # 2. Enter Supplier Invoice Number
        print("Entering supplier invoice number...")
        page.fill("input[name='supplier_invoice_number']", "INV-CMS-2026-002")
        time.sleep(0.4)

        # 3. Fill in Item 1: Toyota Engine Oil 0W-20 (ID: 35)
        print("Configuring Item 1: Toyota Engine Oil 0W-20...")
        row0 = page.locator("tbody tr").nth(0)
        row0.locator("select").first.select_option("35")
        time.sleep(0.4)
        row0.locator("input[name*='purchase_quantity']").fill("50")
        time.sleep(0.3)
        row0.locator("input[name*='purchase_unit_price']").fill("14000")
        time.sleep(0.4)

        # Item 2: Toyota Genuine Engine Oil Filter (ID: 34)
        print("Adding Item 2: Toyota Genuine Engine Oil Filter...")
        page.click("button:has-text('Add Product Line')")
        time.sleep(0.5)
        row1 = page.locator("tbody tr").nth(1)
        row1.locator("select").first.select_option("34")
        time.sleep(0.4)
        row1.locator("input[name*='purchase_quantity']").fill("40")
        time.sleep(0.3)
        row1.locator("input[name*='purchase_unit_price']").fill("1800")
        time.sleep(0.4)

        # Item 3: Toyota Genuine Brake Pads (ID: 37)
        print("Adding Item 3: Toyota Genuine Brake Pads...")
        page.click("button:has-text('Add Product Line')")
        time.sleep(0.5)
        row2 = page.locator("tbody tr").nth(2)
        row2.locator("select").first.select_option("37")
        time.sleep(0.4)
        row2.locator("input[name*='purchase_quantity']").fill("30")
        time.sleep(0.3)
        row2.locator("input[name*='purchase_unit_price']").fill("9500")
        time.sleep(0.4)

        # Item 4: Toyota Genuine Air Filter (ID: 36)
        print("Adding Item 4: Toyota Genuine Air Filter...")
        page.click("button:has-text('Add Product Line')")
        time.sleep(0.5)
        row3 = page.locator("tbody tr").nth(3)
        row3.locator("select").first.select_option("36")
        time.sleep(0.4)
        row3.locator("input[name*='purchase_quantity']").fill("30")
        time.sleep(0.3)
        row3.locator("input[name*='purchase_unit_price']").fill("2500")
        time.sleep(0.5)

        page.screenshot(path="scratch/step2_grn_filled.png")
        print("Submitting GRN with 'Confirm & Add to Stock'...")
        
        confirm_btn = page.locator("button:has-text('Confirm & Add to Stock')").first
        confirm_btn.click()
        page.wait_for_load_state("networkidle")
        time.sleep(2)

        print("Current URL after GRN submission:", page.url)
        page.screenshot(path="scratch/step2_grn_confirmed.png")

    # Verify MySQL
    conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    cursor.execute("SELECT * FROM goods_received_notes ORDER BY id DESC LIMIT 1")
    grn = cursor.fetchone()
    print("Latest GRN:", grn)
    assert grn is not None, "No GRN found!"
    assert grn['status'] == 'confirmed', f"GRN status is {grn['status']}, expected 'confirmed'!"

    cursor.execute("SELECT COUNT(*) AS c FROM goods_received_note_items WHERE grn_id = %s", (grn['id'],))
    items_count = cursor.fetchone()['c']
    print("GRN Items count:", items_count)
    assert items_count == 4, f"Expected 4 items, got {items_count}"

    cursor.execute("SELECT COUNT(*) AS c FROM stock_batches WHERE grn_id = %s", (grn['id'],))
    batches_count = cursor.fetchone()['c']
    print("Generated Stock Batches count:", batches_count)
    assert batches_count == 4, f"Expected 4 batches, got {batches_count}"

    cursor.execute("SELECT COUNT(*) AS c FROM stock_movements WHERE reference_id = %s AND movement_type = 'grn_receipt'", (grn['id'],))
    movements_count = cursor.fetchone()['c']
    print("Stock Movements count:", movements_count)
    assert movements_count == 4, f"Expected 4 movements, got {movements_count}"

    print("ALL GRN, STOCK BATCH, AND MOVEMENT VERIFICATIONS PASSED WITH 100% SUCCESS!")

if __name__ == "__main__":
    run()
