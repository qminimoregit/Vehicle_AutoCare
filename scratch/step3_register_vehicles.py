import time
from playwright.sync_api import sync_playwright
import pymysql

vehicles_data = [
    {
        "vehicle_number": "WP CAK-4512",
        "customer_name": "Ruwan Silva",
        "phone": "0771122334",
        "email": "ruwan.silva@example.local",
        "type_name": "Car / Sedan",
        "current_mileage": "65000",
        "next_mileage": "75000",
        "make_model": "Toyota Aqua 2015"
    },
    {
        "vehicle_number": "WP CAA-8834",
        "customer_name": "Dinesh Perera",
        "phone": "0772233445",
        "email": "dinesh.perera@example.local",
        "type_name": "Car / Sedan",
        "current_mileage": "82000",
        "next_mileage": "92000",
        "make_model": "Toyota Prius 2016"
    },
    {
        "vehicle_number": "WP CAB-1902",
        "customer_name": "Chamara Wijesinghe",
        "phone": "0773344556",
        "email": "chamara.w@example.local",
        "type_name": "Car / Sedan",
        "current_mileage": "94000",
        "next_mileage": "104000",
        "make_model": "Toyota Axio 2014"
    },
    {
        "vehicle_number": "WP CBF-6721",
        "customer_name": "Kasun Jayawardena",
        "phone": "0774455667",
        "email": "kasun.j@example.local",
        "type_name": "Car / Sedan",
        "current_mileage": "48000",
        "next_mileage": "58000",
        "make_model": "Toyota Vitz 2018"
    },
    {
        "vehicle_number": "WP CBA-9045",
        "customer_name": "Sunil Fernando",
        "phone": "0775566778",
        "email": "sunil.f@example.local",
        "type_name": "SUV",
        "current_mileage": "55000",
        "next_mileage": "65000",
        "make_model": "Toyota Hilux 2020"
    }
]

def run():
    print("Connecting to visible Chrome on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        jobs_created = []

        for i, item in enumerate(vehicles_data):
            print(f"\n--- Registering Vehicle {i+1}: {item['vehicle_number']} ({item['make_model']}) for {item['customer_name']} ---")
            page.goto("http://localhost:8000/operations/vehicles", wait_until="networkidle")
            time.sleep(0.8)

            print(f"Entering vehicle number {item['vehicle_number']}...")
            v_input = page.locator("input[name='vehicle_number']")
            v_input.fill(item['vehicle_number'])
            time.sleep(0.5)

            # Trigger check button or tab out
            check_btn = page.locator("button:has-text('Check Vehicle')")
            if check_btn.count() > 0:
                check_btn.click()
                time.sleep(0.8)

            print("Filling customer details...")
            page.locator("input[name='customer_name']").fill(item['customer_name'])
            time.sleep(0.3)
            page.locator("input[name='phone']").fill(item['phone'])
            time.sleep(0.3)
            page.locator("input[name='email']").fill(item['email'])
            time.sleep(0.3)

            print(f"Selecting Vehicle Type: {item['type_name']}...")
            type_btn = page.locator(f"button:has-text('{item['type_name']}')").first
            type_btn.click()
            time.sleep(0.4)

            print("Entering mileage...")
            page.locator("input[name='current_mileage']").fill(item['current_mileage'])
            time.sleep(0.3)
            page.locator("input[name='next_service_mileage']").fill(item['next_mileage'])
            time.sleep(0.3)

            # Open more details to set make_model
            more_btn = page.locator("button:has-text('Show More Details')")
            if more_btn.count() > 0:
                more_btn.click()
                time.sleep(0.4)
                page.locator("input[name='make_model']").fill(item['make_model'])
                time.sleep(0.3)

            page.screenshot(path=f"scratch/step3_vehicle_{i+1}_filled.png")

            print("Clicking 'Register & Start Job'...")
            submit_btn = page.locator("button[type='submit']:has-text('Start Job')").first
            submit_btn.click()
            page.wait_for_load_state("networkidle")
            time.sleep(1.5)

            print("Current URL:", page.url)
            page.screenshot(path=f"scratch/step3_vehicle_{i+1}_saved.png")
            jobs_created.append((item['vehicle_number'], page.url))

    # Verify MySQL
    conn = pymysql.connect(host='127.0.0.1', port=3307, user='root', password='', database='autocare_system')
    cursor = conn.cursor(pymysql.cursors.DictCursor)

    cursor.execute("SELECT id, vehicle_number, customer_id, make_model, current_mileage FROM vehicles")
    vehicles = cursor.fetchall()
    print("\n=== VERIFIED VEHICLES IN MYSQL ===")
    for v in vehicles:
        print(v)
    assert len(vehicles) >= 5, f"Expected at least 5 vehicles, found {len(vehicles)}"

    cursor.execute("SELECT id, job_number, vehicle_id, customer_id, status FROM job_cards")
    job_cards = cursor.fetchall()
    print("\n=== VERIFIED JOB CARDS IN MYSQL ===")
    for j in job_cards:
        print(j)
    assert len(job_cards) >= 5, f"Expected at least 5 job cards, found {len(job_cards)}"

if __name__ == "__main__":
    run()
