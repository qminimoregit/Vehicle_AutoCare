import time
from playwright.sync_api import sync_playwright
import pymysql

def run():
    print("Connecting to visible Chrome on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        print("Navigating to Add Vehicle Type page...")
        page.goto("http://localhost:8000/master-data/vehicle-types/create", wait_until="networkidle")
        time.sleep(1)

        print("Entering vehicle type name 'Car / Sedan'...")
        page.fill("input[name='name']", "Car / Sedan")
        time.sleep(0.5)

        # Check enable carpets toggle
        enable_carpets_checkbox = page.locator("input[name='enable_carpets']")
        if enable_carpets_checkbox.count() > 0:
            print("Enabling carpets...")
            # Click parent label or toggle
            page.locator("label:has(input[name='enable_carpets'])").click()
            time.sleep(0.8)

            # Add carpet category Front Carpet if input visible
            cat_input = page.locator("input[placeholder*='Third Row Carpet']")
            if cat_input.count() > 0:
                print("Adding carpet category: Front & Rear Mats...")
                cat_input.fill("Front & Rear Mats")
                time.sleep(0.5)
                page.click("button:has-text('Add')")
                time.sleep(1)

        print("Submitting vehicle type form...")
        page.screenshot(path="scratch/step1_before_submit.png")
        page.click("button[type='submit']:has-text('Save Vehicle Type')")
        page.wait_for_load_state("networkidle")
        time.sleep(1.5)

        print("Current URL:", page.url)
        page.screenshot(path="scratch/step1_saved.png")

    # Verify MySQL
    conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    cursor.execute("SELECT * FROM vehicle_types WHERE name = 'Car / Sedan'")
    row = cursor.fetchone()
    print("Database verification for 'Car / Sedan':", row)
    assert row is not None, "Vehicle type 'Car / Sedan' was not saved!"
    print("Vehicle type verified in MySQL! ID:", row['id'])

if __name__ == "__main__":
    run()
