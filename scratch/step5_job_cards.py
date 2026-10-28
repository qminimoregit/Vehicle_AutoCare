import time
from playwright.sync_api import sync_playwright
import pymysql

def run():
    print("Connecting to visible Chrome on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        # Auto-accept confirmation dialogs if any
        page.on("dialog", lambda dialog: dialog.accept())

        # =======================================================
        # 1. CONFIGURE JOB CARD 1: Ruwan Silva / Toyota Aqua (ID: 46)
        # =======================================================
        print("\n=======================================================")
        print("Configuring Job Card 1 (ID: 46) - Ruwan Silva / Toyota Aqua")
        print("=======================================================")
        page.goto("http://localhost:8000/operations/job-cards/46", wait_until="networkidle")
        time.sleep(1)

        # 1.1 Assign Technician
        print("Assigning lead technician...")
        tech_btn = page.locator("button:has-text('Assign Tech'), button:has-text('Change Tech')").first
        if tech_btn.count() > 0:
            tech_btn.click()
            time.sleep(0.5)
            tech_radio = page.locator("input[name='assigned_to']").first
            if tech_radio.count() > 0:
                tech_radio.check()
                time.sleep(0.3)
                page.click("button:has-text('Save Assignment')")
                page.wait_for_load_state("networkidle")
                time.sleep(1)

        # 1.2 Add Services: Engine Oil Change (ID: 15), Oil Filter Replacement (ID: 16)
        print("Switching to Services chip...")
        page.locator("button:has-text('Services')").first.click()
        time.sleep(0.5)

        print("Adding Service: Engine Oil Change...")
        srv_card_1 = page.locator("article:has-text('Engine Oil Change')").first
        srv_card_1.locator("button:has-text('Add Service')").click()
        time.sleep(1.2)

        print("Adding Service: Oil Filter Replacement...")
        srv_card_2 = page.locator("article:has-text('Oil Filter Replacement')").first
        srv_card_2.locator("button:has-text('Add Service')").click()
        time.sleep(1.2)

        # 1.3 Switch to Spare Parts tab and Add Products
        print("Switching to Spare Parts chip...")
        page.locator("button:has-text('Spare Parts')").first.click()
        time.sleep(0.6)

        print("Adding Product: Toyota Engine Oil 0W-20...")
        prod_card_1 = page.locator("article:has-text('Toyota Engine Oil 0W-20')").first
        prod_card_1.locator("button:has-text('Issue Part')").click()
        time.sleep(1.5)

        print("Adding Product: Toyota Genuine Engine Oil Filter...")
        prod_card_2 = page.locator("article:has-text('Toyota Genuine Engine Oil Filter')").first
        prod_card_2.locator("button:has-text('Issue Part')").click()
        time.sleep(1.5)

        page.screenshot(path="scratch/step5_job1_configured.png")

        # 1.4 Test Print Preview
        print("Testing Job Card 1 Print Preview...")
        page.goto("http://localhost:8000/operations/job-cards/46/print", wait_until="networkidle")
        time.sleep(1)
        page.screenshot(path="scratch/step5_job1_print.png")

        # 1.5 Complete Job Card 1 (Generates Invoice 1)
        print("Navigating back to Job Card 1 and completing job...")
        page.goto("http://localhost:8000/operations/job-cards/46", wait_until="networkidle")
        time.sleep(1)

        complete_btn = page.locator("button:has-text('Complete Job')").first
        complete_btn.click()
        page.wait_for_load_state("networkidle")
        time.sleep(2)

        page.screenshot(path="scratch/step5_job1_completed.png")
        print("Job Card 1 completed! Current URL:", page.url)

        # =======================================================
        # 2. CONFIGURE JOB CARD 2: Dinesh Perera / Toyota Prius (ID: 47)
        # =======================================================
        print("\n=======================================================")
        print("Configuring Job Card 2 (ID: 47) - Dinesh Perera / Toyota Prius")
        print("=======================================================")
        page.goto("http://localhost:8000/operations/job-cards/47", wait_until="networkidle")
        time.sleep(1)

        print("Assigning lead technician...")
        tech_btn = page.locator("button:has-text('Assign Tech'), button:has-text('Change Tech')").first
        if tech_btn.count() > 0:
            tech_btn.click()
            time.sleep(0.5)
            tech_radio = page.locator("input[name='assigned_to']").first
            if tech_radio.count() > 0:
                tech_radio.check()
                time.sleep(0.3)
                page.click("button:has-text('Save Assignment')")
                page.wait_for_load_state("networkidle")
                time.sleep(1)

        print("Switching to Services chip...")
        page.locator("button:has-text('Services')").first.click()
        time.sleep(0.5)

        print("Adding Service: Engine Diagnostic Scan...")
        page.locator("article:has-text('Engine Diagnostic Scan')").first.locator("button:has-text('Add Service')").click()
        time.sleep(1.2)

        print("Adding Service: Suspension Inspection...")
        page.locator("article:has-text('Suspension Inspection')").first.locator("button:has-text('Add Service')").click()
        time.sleep(1.2)

        page.screenshot(path="scratch/step5_job2_configured.png")

        print("Testing Job Card 2 Print Preview...")
        page.goto("http://localhost:8000/operations/job-cards/47/print", wait_until="networkidle")
        time.sleep(1)
        page.screenshot(path="scratch/step5_job2_print.png")

        # =======================================================
        # 3. CONFIGURE JOB CARD 3: Chamara Wijesinghe / Toyota Axio (ID: 48)
        # =======================================================
        print("\n=======================================================")
        print("Configuring Job Card 3 (ID: 48) - Chamara Wijesinghe / Toyota Axio")
        print("=======================================================")
        page.goto("http://localhost:8000/operations/job-cards/48", wait_until="networkidle")
        time.sleep(1)

        print("Assigning lead technician...")
        tech_btn = page.locator("button:has-text('Assign Tech'), button:has-text('Change Tech')").first
        if tech_btn.count() > 0:
            tech_btn.click()
            time.sleep(0.5)
            tech_radio = page.locator("input[name='assigned_to']").first
            if tech_radio.count() > 0:
                tech_radio.check()
                time.sleep(0.3)
                page.click("button:has-text('Save Assignment')")
                page.wait_for_load_state("networkidle")
                time.sleep(1)

        print("Switching to Services chip...")
        page.locator("button:has-text('Services')").first.click()
        time.sleep(0.5)

        print("Adding Service: Brake Pad Replacement...")
        page.locator("article:has-text('Brake Pad Replacement')").first.locator("button:has-text('Add Service')").click()
        time.sleep(1.2)

        print("Switching to Spare Parts chip...")
        page.locator("button:has-text('Spare Parts')").first.click()
        time.sleep(0.6)

        print("Adding Product: Toyota Genuine Brake Pads...")
        prod_brake = page.locator("article:has-text('Toyota Genuine Brake Pads')").first
        prod_brake.locator("button:has-text('Issue Part')").click()
        time.sleep(1.5)

        page.screenshot(path="scratch/step5_job3_configured.png")

        print("Testing Job Card 3 Print Preview...")
        page.goto("http://localhost:8000/operations/job-cards/48/print", wait_until="networkidle")
        time.sleep(1)
        page.screenshot(path="scratch/step5_job3_print.png")

    # 4. Database Verification
    conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    print("\n=== VERIFYING JOB CARDS IN MYSQL ===")
    cursor.execute("""
        SELECT j.id, j.job_number, j.status, j.assigned_to, u.name AS technician_name, 
               j.service_total, j.product_total, j.grand_total, j.invoice_id
        FROM job_cards j
        LEFT JOIN users u ON j.assigned_to = u.id
        WHERE j.id IN (46, 47, 48)
        ORDER BY j.id ASC
    """)
    for job in cursor.fetchall():
        print(f"\nJob #{job['job_number']} (ID: {job['id']}): Status={job['status']}, Tech={job['technician_name']}, Services=LKR {job['service_total']}, Products=LKR {job['product_total']}, Grand Total=LKR {job['grand_total']}, Invoice ID={job['invoice_id']}")
        
        cursor.execute("SELECT id, service_name, quantity, unit_price, line_total FROM job_card_services WHERE job_card_id = %s", (job['id'],))
        srvs = cursor.fetchall()
        for s in srvs:
            print(f"  - [Service] {s['service_name']} x {s['quantity']} @ {s['unit_price']} = LKR {s['line_total']}")
            
        cursor.execute("SELECT id, product_name, quantity, unit_price, line_total FROM job_card_products WHERE job_card_id = %s", (job['id'],))
        prds = cursor.fetchall()
        for p in prds:
            print(f"  - [Product] {p['product_name']} x {p['quantity']} @ {p['unit_price']} = LKR {p['line_total']}")

    # Verify Invoice 1 generated from Job 46
    cursor.execute("SELECT * FROM invoices WHERE job_card_id = 46")
    inv = cursor.fetchone()
    print("\n=== VERIFYING GENERATED INVOICE FOR JOB 46 ===")
    print("Generated Invoice:", inv)
    assert inv is not None, "Invoice was not generated for completed Job 46!"
    assert inv['status'] == 'issued', f"Expected invoice status 'issued', got {inv['status']}"

    cursor.execute("SELECT COUNT(*) AS c FROM invoice_items WHERE invoice_id = %s", (inv['id'],))
    items_count = cursor.fetchone()['c']
    print(f"Invoice items count: {items_count}")
    assert items_count == 4, f"Expected 4 items on Invoice 1, found {items_count}"

    print("\nALL JOB CARD AND GENERATED INVOICE VERIFICATIONS PASSED WITH 100% SUCCESS!")

if __name__ == "__main__":
    run()
