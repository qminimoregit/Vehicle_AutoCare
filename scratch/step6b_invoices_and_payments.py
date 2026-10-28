import time
import json
import pymysql
from playwright.sync_api import sync_playwright

def run():
    print("Connecting to visible Google Chrome via CDP on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        # Dialog auto-accept handler for confirms
        page.on("dialog", lambda dialog: dialog.accept())

        # ========================================================
        # 1. PRIORITY C: INVOICE 2 (DIRECT INVOICE)
        # ========================================================
        print("--- Navigating to Direct Invoice Creation ---")
        page.goto("http://localhost:8000/operations/invoices/create", wait_until="networkidle")
        time.sleep(2)

        # Search and select customer Kasun Jayawardena
        print("Searching and selecting customer Kasun Jayawardena...")
        cust_picker = page.locator("[data-searchable-select='customer_id']").first
        cust_picker.locator("[role='combobox']").click()
        time.sleep(0.5)
        cust_picker.locator("input[type='text']").fill("Kasun")
        time.sleep(0.8)
        cust_opt = cust_picker.locator("[role='option']:has-text('Kasun Jayawardena')").first
        cust_opt.click()
        time.sleep(1)

        # Subject
        print("Entering subject...")
        page.locator("input[name='subject']").fill("Routine Automotive Maintenance")

        # Vehicle select
        veh_select = page.locator("select[name='vehicle_id']").first
        if veh_select.is_visible():
            veh_options = veh_select.locator("option").all()
            for opt in veh_options:
                val = opt.get_attribute("value")
                text = opt.text_content()
                if "CBF-6721" in text or "Vitz" in text or val == "55":
                    veh_select.select_option(value=val)
                    print(f"Selected vehicle: {text.strip()}")
                    break

        # Line items
        # Row 0: Select Service 15 (Engine Oil Change)
        print("Configuring Line 1: Engine Oil Change...")
        row0_select = page.locator("tbody tr").nth(0).locator("select").first
        row0_select.select_option(value="15")
        time.sleep(0.5)
        # Fill unit price: 2500
        page.locator("tbody tr").nth(0).locator("input[type='number']").first.fill("2500")

        # Row 1: Click "+ Add Service"
        print("Configuring Line 2: Air Conditioning Service...")
        page.locator("button:has-text('+ Add Service')").first.click()
        time.sleep(0.5)
        row1_select = page.locator("tbody tr").nth(1).locator("select").first
        row1_select.select_option(value="19") # Air Conditioning Service
        time.sleep(0.5)
        page.locator("tbody tr").nth(1).locator("input[type='number']").first.fill("6500")

        time.sleep(1)
        page.screenshot(path="scratch/step6_invoice2_before_submit.png", full_page=True)

        print("Submitting Direct Invoice form...")
        page.locator("button:has-text('Create & Issue Invoice')").first.click()
        time.sleep(4)

        page.screenshot(path="scratch/step6_invoice2_saved.png", full_page=True)
        print("Captured scratch/step6_invoice2_saved.png")

        # ========================================================
        # 2. PRIORITY C: INVOICE 3 (QUOTATION CONVERSION)
        # ========================================================
        print("--- Converting Quotation 12 to Invoice ---")
        page.goto("http://localhost:8000/billing/quotations/12", wait_until="networkidle")
        time.sleep(2)

        accept_btn = page.locator("button:has-text('Accept')").first
        if accept_btn.is_visible():
            print("Clicking 'Accept' on Quotation 12...")
            accept_btn.click()
            time.sleep(2)

        print("Clicking 'Convert to Invoice' on Quotation 12...")
        convert_btn = page.locator("button:has-text('Convert to Invoice')").first
        convert_btn.click()
        time.sleep(4)

        page.screenshot(path="scratch/step6_invoice3_converted.png", full_page=True)
        print("Captured scratch/step6_invoice3_converted.png")

        # ========================================================
        # 3. PRIORITY D: INVOICE PAYMENTS TESTING
        # ========================================================
        conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
        with conn.cursor() as cur:
            cur.execute("SELECT id, invoice_number, grand_total, balance_amount, status, job_card_id, quotation_id FROM invoices ORDER BY id ASC")
            invoices = cur.fetchall()
            print("--- Current Invoices in DB ---")
            for inv in invoices:
                print(inv)

        # Identify Invoices
        inv1_id = None
        inv2_id = None
        inv3_id = None
        for inv in invoices:
            if inv[5] == 46:
                inv1_id = inv[0]
            elif inv[6] == 12:
                inv3_id = inv[0]
            elif inv[5] is None and inv[6] is None:
                inv2_id = inv[0]

        print(f"Invoice 1 (Job Card 46): ID {inv1_id}")
        print(f"Invoice 2 (Direct Invoice): ID {inv2_id}")
        print(f"Invoice 3 (Quotation 12): ID {inv3_id}")

        # CASE 1: UNPAID INVOICE (Invoice 2)
        print("--- Testing Case 1: Unpaid Invoice (Invoice 2) ---")
        page.goto(f"http://localhost:8000/operations/invoices/{inv2_id}", wait_until="networkidle")
        time.sleep(2)
        page.screenshot(path="scratch/step7_case1_unpaid.png", full_page=True)
        print("Captured scratch/step7_case1_unpaid.png")

        # CASE 2: PARTIALLY PAID INVOICE (Invoice 1)
        print("--- Testing Case 2: Partially Paid Invoice (Invoice 1) ---")
        page.goto(f"http://localhost:8000/operations/invoices/{inv1_id}", wait_until="networkidle")
        time.sleep(2)

        print("Opening Record Payment modal on Invoice 1...")
        page.locator("button:has-text('Record Payment')").first.click()
        time.sleep(1)

        print("Entering partial payment of LKR 10,000.00...")
        amt_input = page.locator("input[x-model='paymentAmount']").first
        amt_input.fill("")
        amt_input.type("10000.00")
        time.sleep(0.5)

        # Payment method
        page.locator("select[name='payment_method']").first.select_option("cash")
        # Reference
        page.locator("input[name='reference_number']").first.fill("REC-PARTIAL-001")
        time.sleep(0.5)

        # Save payment
        page.locator("button:has-text('Save Payment')").first.click()
        time.sleep(4)

        page.screenshot(path="scratch/step7_case2_partial.png", full_page=True)
        print("Captured scratch/step7_case2_partial.png")

        # CASE 3: FULLY PAID INVOICE (Invoice 3)
        print("--- Testing Case 3: Fully Paid Invoice (Invoice 3) ---")
        page.goto(f"http://localhost:8000/operations/invoices/{inv3_id}", wait_until="networkidle")
        time.sleep(2)

        print("Opening Record Payment modal on Invoice 3...")
        page.locator("button:has-text('Record Payment')").first.click()
        time.sleep(1)

        # For Invoice 3, pay full balance (5,500.00)
        amt_input = page.locator("input[x-model='paymentAmount']").first
        amt_input.fill("")
        amt_input.type("5500.00")
        time.sleep(0.5)

        page.locator("select[name='payment_method']").first.select_option("bank_transfer")
        page.locator("input[name='reference_number']").first.fill("BANK-TXN-99812")
        time.sleep(0.5)

        page.locator("button:has-text('Save Payment')").first.click()
        time.sleep(4)

        page.screenshot(path="scratch/step7_case3_full.png", full_page=True)
        print("Captured scratch/step7_case3_full.png")

        # ========================================================
        # 4. DATABASE VERIFICATION
        # ========================================================
        print("--- Verifying Final Payment and Invoice State in MySQL ---")
        with conn.cursor() as cur:
            cur.execute("""
                SELECT id, invoice_number, grand_total, amount_paid, balance_amount, payment_status, status 
                FROM invoices 
                ORDER BY id ASC
            """)
            for row in cur.fetchall():
                print("Invoice:", row)

            cur.execute("""
                SELECT id, payment_number, invoice_id, amount, payment_method, reference_number, payment_date 
                FROM payments 
                ORDER BY id ASC
            """)
            for row in cur.fetchall():
                print("Payment:", row)

        print("--- Step 6b & Priority D completed successfully! ---")

if __name__ == "__main__":
    run()
