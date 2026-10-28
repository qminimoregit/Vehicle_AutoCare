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
        # 1. APPOINTMENTS
        # ========================================================
        print("\n--- 1. Creating Appointment for Sunil Fernando (Hilux) ---")
        page.goto("http://localhost:8000/appointments", wait_until="networkidle")
        time.sleep(2)

        # Click "New Appointment"
        new_app_btn = page.locator("button:has-text('New Appointment')").first
        new_app_btn.click()
        time.sleep(1)

        # Fill appointment modal
        print("Entering vehicle number: WP CBA-9045...")
        page.locator("input[name='vehicle_number']").fill("WP CBA-9045")
        time.sleep(0.5)

        print("Entering customer name: Sunil Fernando...")
        page.locator("input[name='customer_name']").fill("Sunil Fernando")
        time.sleep(0.3)

        print("Entering mobile number: 0775556677...")
        page.locator("input[name='mobile_number']").fill("0775556677")
        time.sleep(0.3)

        # Vehicle Type
        veh_type_select = page.locator("select[name='vehicle_type_id']").first
        veh_type_select.select_option(value="10")
        time.sleep(0.3)

        # Date & Time (Time is a select dropdown)
        print("Setting appointment date and time...")
        page.locator("input[name='appointment_date']").fill("2026-10-28")
        time.sleep(0.3)
        page.locator("select[name='appointment_time']").select_option("10:00 AM")
        time.sleep(0.3)

        # Notes
        notes_input = page.locator("textarea[name='notes']").first
        if notes_input.is_visible():
            notes_input.fill("Scheduled 60,000km periodic maintenance and filter check")

        time.sleep(1)
        page.screenshot(path="scratch/step8_appointment_filled.png", full_page=True)

        print("Submitting Appointment...")
        page.locator("button:has-text('Confirm & Schedule Appointment')").first.click()
        time.sleep(4)

        page.screenshot(path="scratch/step8_appointment_saved.png", full_page=True)
        print("Captured scratch/step8_appointment_saved.png")

        # ========================================================
        # 2. DIRECT SALES (POS)
        # ========================================================
        print("\n--- 2. Creating Direct Sale (POS) ---")
        page.goto("http://localhost:8000/operations/direct-sales", wait_until="networkidle")
        time.sleep(2)

        # Click the first product card or add to cart button
        print("Adding product to sale cart...")
        page.locator("button:has-text('Add'), .bi-cart-plus").first.click()
        time.sleep(1.5)

        # Amount paid
        amount_paid_input = page.locator("input[x-model='amountPaid']").first
        if amount_paid_input.is_visible():
            amount_paid_input.fill("3000")
            time.sleep(0.5)

        time.sleep(1)
        page.screenshot(path="scratch/step8_direct_sale_cart.png", full_page=True)

        # Complete Sale & Generate Bill
        print("Submitting Direct Sale...")
        checkout_btn = page.locator("button:has-text('Complete Sale & Generate Bill')").first
        checkout_btn.scroll_into_view_if_needed()
        time.sleep(0.5)
        checkout_btn.click()
        time.sleep(4)

        page.screenshot(path="scratch/step8_direct_sale_success.png", full_page=True)
        print("Captured scratch/step8_direct_sale_success.png")

        # ========================================================
        # 3. EXPENSES
        # ========================================================
        print("\n--- 3. Creating Shop Expense ---")
        page.goto("http://localhost:8000/expenses/shop/create", wait_until="networkidle")
        time.sleep(2)

        # Category: Electricity (ID 19)
        print("Selecting category: Electricity...")
        page.locator("select[name='category_id']").select_option(value="19")
        time.sleep(0.3)

        # Amount
        print("Entering amount: 18,500.00...")
        page.locator("input[name='amount']").fill("18500")
        time.sleep(0.3)

        # Payment Amount
        pay_amt_input = page.locator("input[name='payment_amount']").first
        if pay_amt_input.is_visible():
            pay_amt_input.fill("18500")
            time.sleep(0.3)

        # Description
        desc_input = page.locator("textarea[name='description']").first
        if desc_input.is_visible():
            desc_input.fill("Monthly CEB Electricity Bill for Workshop Operations")

        time.sleep(1)
        page.screenshot(path="scratch/step8_expense_filled.png", full_page=True)

        print("Submitting Shop Expense...")
        page.locator("button:has-text('Save Expense')").first.click()
        time.sleep(4)

        page.screenshot(path="scratch/step8_expense_saved.png", full_page=True)
        print("Captured scratch/step8_expense_saved.png")

        # ========================================================
        # 4. DASHBOARD CHECK
        # ========================================================
        print("\n--- 4. Checking Dashboard Summary ---")
        page.goto("http://localhost:8000/dashboard", wait_until="networkidle")
        time.sleep(3)
        page.screenshot(path="scratch/step8_dashboard_live.png", full_page=True)
        print("Captured scratch/step8_dashboard_live.png")

        # ========================================================
        # 5. MYSQL VERIFICATION
        # ========================================================
        print("\n--- Database Verification for Priority E ---")
        conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
        with conn.cursor() as cur:
            cur.execute("SELECT id, appointment_number, customer_name, vehicle_number, appointment_date, appointment_time, status FROM appointments")
            for r in cur.fetchall():
                print("Appointment:", r)

            cur.execute("SELECT id, sale_number, grand_total, payment_status, payment_method FROM direct_sales")
            for r in cur.fetchall():
                print("Direct Sale:", r)

            cur.execute("SELECT id, expense_number, category_id, amount, status FROM expenses")
            for r in cur.fetchall():
                print("Expense:", r)

        print("\n--- Step 8 execution complete! ---")

if __name__ == "__main__":
    run()
