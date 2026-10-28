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
        # 1. JOB CARD 46: ASSIGN SERVICE PRICES AND COMPLETE JOB
        # ========================================================
        print("--- Navigating to Job Card 46 (Ruwan Silva / Toyota Aqua) ---")
        page.goto("http://localhost:8000/operations/job-cards/46", wait_until="networkidle")
        time.sleep(2)

        # Set prices for services with 'Edit Price' button
        while True:
            edit_btns = page.locator("button:has-text('Edit Price')")
            if edit_btns.count() == 0 or not edit_btns.first.is_visible():
                print("No more pending price services on Job 46.")
                break
            edit_btn = edit_btns.first
            edit_btn.scroll_into_view_if_needed()
            time.sleep(0.5)
            edit_btn.click()
            time.sleep(1)

            title_el = page.locator("div[x-show='showEditItemModal'] strong[x-text='editingItem.title']").first
            title_el.wait_for(state="visible", timeout=5000)
            item_name = title_el.text_content().strip()
            print(f"Job 46 - Editing price for: {item_name}")

            price_input = page.locator("div[x-show='showEditItemModal'] input[type='number']").first
            price_input.fill("")
            if "Oil Change" in item_name:
                price_input.type("2500")
            else:
                price_input.type("1500")
            time.sleep(0.5)

            # Click save inside showEditItemModal
            save_btn = page.locator("div[x-show='showEditItemModal'] button.lv-btn-brand").first
            save_btn.click()
            time.sleep(2)
            print(f"Job 46 - Saved price for {item_name}")

        # Now click Complete Job
        print("Completing Job Card 46...")
        complete_btn = page.locator("button:has-text('Complete Job')").first
        complete_btn.scroll_into_view_if_needed()
        time.sleep(1)
        complete_btn.click()
        time.sleep(4)

        # Take screenshot of completed job
        page.screenshot(path="scratch/step6_job1_completed.png", full_page=True)
        print("Captured scratch/step6_job1_completed.png")

        # ========================================================
        # 2. JOB CARD 47: ASSIGN SERVICE PRICES
        # ========================================================
        print("--- Navigating to Job Card 47 (Dinesh Perera / Prius) ---")
        page.goto("http://localhost:8000/operations/job-cards/47", wait_until="networkidle")
        time.sleep(2)

        while True:
            edit_btns = page.locator("button:has-text('Edit Price')")
            if edit_btns.count() == 0 or not edit_btns.first.is_visible():
                print("No more pending price services on Job 47.")
                break
            edit_btn = edit_btns.first
            edit_btn.scroll_into_view_if_needed()
            time.sleep(0.5)
            edit_btn.click()
            time.sleep(1)

            title_el = page.locator("div[x-show='showEditItemModal'] strong[x-text='editingItem.title']").first
            title_el.wait_for(state="visible", timeout=5000)
            item_name = title_el.text_content().strip()
            print(f"Job 47 - Editing price for: {item_name}")

            price_input = page.locator("div[x-show='showEditItemModal'] input[type='number']").first
            price_input.fill("")
            if "Diagnostic" in item_name:
                price_input.type("3500")
            else:
                price_input.type("2000")
            time.sleep(0.5)

            save_btn = page.locator("div[x-show='showEditItemModal'] button.lv-btn-brand").first
            save_btn.click()
            time.sleep(2)
            print(f"Job 47 - Saved price for {item_name}")

        # ========================================================
        # 3. JOB CARD 48: ASSIGN SERVICE PRICES
        # ========================================================
        print("--- Navigating to Job Card 48 (Chamara Wijesinghe / Axio) ---")
        page.goto("http://localhost:8000/operations/job-cards/48", wait_until="networkidle")
        time.sleep(2)

        while True:
            edit_btns = page.locator("button:has-text('Edit Price')")
            if edit_btns.count() == 0 or not edit_btns.first.is_visible():
                print("No more pending price services on Job 48.")
                break
            edit_btn = edit_btns.first
            edit_btn.scroll_into_view_if_needed()
            time.sleep(0.5)
            edit_btn.click()
            time.sleep(1)

            title_el = page.locator("div[x-show='showEditItemModal'] strong[x-text='editingItem.title']").first
            title_el.wait_for(state="visible", timeout=5000)
            item_name = title_el.text_content().strip()
            print(f"Job 48 - Editing price for: {item_name}")

            price_input = page.locator("div[x-show='showEditItemModal'] input[type='number']").first
            price_input.fill("")
            price_input.type("3000")
            time.sleep(0.5)

            save_btn = page.locator("div[x-show='showEditItemModal'] button.lv-btn-brand").first
            save_btn.click()
            time.sleep(2)
            print(f"Job 48 - Saved price for {item_name}")

        # ========================================================
        # 4. PRIORITY C: INVOICES
        # ========================================================
        # Check Invoice 1 (generated from Job Card 46)
        print("--- Checking Invoice 1 (from Job Card 46) ---")
        conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
        with conn.cursor() as cur:
            cur.execute("SELECT id, invoice_number, grand_total, balance_amount, status FROM invoices WHERE job_card_id = 46")
            inv1 = cur.fetchone()
            print(f"Invoice from Job Card 46 in DB: {inv1}")

        if inv1:
            inv1_id = inv1[0]
            print(f"Navigating to Invoice 1 (ID {inv1_id}) in visible Chrome...")
            page.goto(f"http://localhost:8000/operations/invoices/{inv1_id}", wait_until="networkidle")
            time.sleep(2)
            page.screenshot(path="scratch/step6_invoice1_from_job.png", full_page=True)
            print("Captured scratch/step6_invoice1_from_job.png")

        # Create Invoice 2 (Direct Invoice)
        print("--- Creating Invoice 2 (Direct Invoice for Kasun Jayawardena / Vitz) ---")
        page.goto("http://localhost:8000/operations/invoices/create", wait_until="networkidle")
        time.sleep(2)

        # Search customer Kasun
        search_box = page.locator("input[placeholder*='Search Customer']").first
        search_box.click()
        search_box.type("Kasun")
        time.sleep(1.5)
        page.locator("div[role='option']:has-text('Kasun Jayawardena')").first.click()
        time.sleep(1)

        # Subject
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
        row0_select = page.locator("tbody tr").nth(0).locator("select").first
        row0_select.select_option(value="15")
        time.sleep(0.5)
        # Ensure unit price is 2500
        page.locator("tbody tr").nth(0).locator("input[type='number']").first.fill("2500")

        # Row 1: Click "+ Add Service"
        page.locator("button:has-text('+ Add Service')").first.click()
        time.sleep(0.5)
        row1_select = page.locator("tbody tr").nth(1).locator("select").first
        row1_select.select_option(value="19") # Air Conditioning Service
        time.sleep(0.5)
        page.locator("tbody tr").nth(1).locator("input[type='number']").first.fill("6500")

        time.sleep(1)
        page.screenshot(path="scratch/step6_invoice2_before_submit.png", full_page=True)

        print("Submitting Direct Invoice form...")
        page.locator("button[type='submit']:has-text('Create Invoice')").first.click()
        time.sleep(3)

        page.screenshot(path="scratch/step6_invoice2_saved.png", full_page=True)
        print("Captured scratch/step6_invoice2_saved.png")

        # Create Invoice 3 (Quotation Conversion)
        print("--- Creating Invoice 3 (Convert Quotation 12 to Invoice) ---")
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
        time.sleep(3)

        page.screenshot(path="scratch/step6_invoice3_converted.png", full_page=True)
        print("Captured scratch/step6_invoice3_converted.png")

        print("--- Step 6 execution finished successfully! ---")

if __name__ == "__main__":
    run()
