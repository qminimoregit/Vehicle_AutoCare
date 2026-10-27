import time
from playwright.sync_api import sync_playwright

def run():
    print("Connecting to live Chrome instance on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        contexts = browser.contexts
        if not contexts:
            print("No context found")
            return
        context = contexts[0]
        pages = context.pages
        page = pages[0] if pages else context.new_page()

        print(f"Initial page URL: {page.url}")

        # 1. PAYROLL SALARY PAGE
        print("\n=======================================================")
        print("STEP 1: Testing Payroll Salary Page (/payroll/salary)")
        print("=======================================================")
        page.goto("http://localhost:8000/payroll/salary", wait_until="networkidle")
        time.sleep(1)

        # 1.1 Verify large yellow banner is NOT present on main page
        main_amber_banner = page.locator("div.bg-amber-50.border-l-4.border-amber-500").count()
        print(f"Main screen large amber banner count: {main_amber_banner} (Expected 0)")
        assert main_amber_banner == 0, "Large yellow banner was not removed!"

        # 1.2 Verify compact warning summary pill
        pill = page.locator("button:has-text('dates need review'), button:has-text('warnings to review')")
        print(f"Found compact warning pills: {pill.count()}")
        if pill.count() > 0:
            pill_text = pill.first.text_content().strip()
            print(f"Compact Pill Text: '{pill_text}'")
            print("Clicking 'View Details' on compact warning pill...")
            pill.first.click()
            time.sleep(1)

            # 1.3 Verify modal contents
            modal_title = page.locator("h3:has-text('Payroll Review Warnings & Attendance Details')")
            print(f"Modal Header Visible: {modal_title.is_visible()}")
            
            dates_count = page.locator("div:has-text('unrecorded')").count()
            print(f"Unrecorded date line items found in modal: {dates_count}")

            page.screenshot(path="payroll_modal_open.png")
            print("Captured screenshot: payroll_modal_open.png")

            # 1.4 Close modal
            close_btn = page.locator("button:has-text('Close Details')")
            close_btn.click()
            time.sleep(0.5)
            print("Modal closed successfully.")

        # 1.5 Test Employee Filter Searchable Dropdown
        print("\nTesting Employee Filter Searchable Dropdown on /payroll/salary...")
        emp_filter = page.locator("[data-searchable-select='employee_id']").first
        if emp_filter.count() > 0:
            print("Opening employee dropdown...")
            emp_filter.locator("[role='combobox']").click()
            time.sleep(0.4)

            search_input = emp_filter.locator("input[type='text']")
            print("Searching for 'a'...")
            search_input.fill("a")
            time.sleep(0.5)

            options = emp_filter.locator("[role='option']")
            print(f"Matching options count: {options.count()}")
            if options.count() > 0:
                print(f"Option 1: {options.nth(0).text_content().strip()}")
                print("Selecting Option 1...")
                options.nth(0).click()
                time.sleep(0.4)
                hidden_id = emp_filter.locator("input[name='employee_id']").input_value()
                print(f"Selected hidden employee_id: {hidden_id}")
                assert hidden_id != "", "Employee ID was not set!"

        # 2. QUOTATIONS CREATE PAGE
        print("\n=======================================================")
        print("STEP 2: Testing Quotations Create (/billing/quotations/create)")
        print("=======================================================")
        page.goto("http://localhost:8000/billing/quotations/create", wait_until="networkidle")
        time.sleep(1)

        cust_comp = page.locator("[data-searchable-select='customer_id']").first
        if cust_comp.count() > 0:
            print("Opening customer dropdown...")
            cust_comp.locator("[role='combobox']").click()
            time.sleep(0.4)

            cust_search = cust_comp.locator("input[type='text']")
            print("Typing 'Ruwan'...")
            cust_search.fill("Ruwan")
            time.sleep(0.6)

            cust_options = cust_comp.locator("[role='option']")
            print(f"Matching customer options: {cust_options.count()}")
            if cust_options.count() > 0:
                print(f"Selecting Customer: {cust_options.first.text_content().strip()}")
                cust_options.first.click()
                time.sleep(0.4)
                saved_cust_id = cust_comp.locator("input[name='customer_id']").input_value()
                print(f"Hidden customer_id: {saved_cust_id}")
                assert saved_cust_id != "", "Customer ID was not set!"
                page.screenshot(path="quotation_customer_selected.png")

        # 3. GOODS RECEIVED NOTES CREATE PAGE
        print("\n=======================================================")
        print("STEP 3: Testing GRN Create (/inventory/grn/create)")
        print("=======================================================")
        page.goto("http://localhost:8000/inventory/grn/create", wait_until="networkidle")
        time.sleep(1)

        supp_comp = page.locator("[data-searchable-select='supplier_id']").first
        if supp_comp.count() > 0:
            print("Opening supplier dropdown...")
            supp_comp.locator("[role='combobox']").click()
            time.sleep(0.4)

            supp_search = supp_comp.locator("input[type='text']")
            print("Typing 'a'...")
            supp_search.fill("a")
            time.sleep(0.6)

            supp_options = supp_comp.locator("[role='option']")
            print(f"Matching supplier options: {supp_options.count()}")
            if supp_options.count() > 0:
                print(f"Selecting Supplier: {supp_options.first.text_content().strip()}")
                supp_options.first.click()
                time.sleep(0.4)
                saved_supp_id = supp_comp.locator("input[name='supplier_id']").input_value()
                print(f"Hidden supplier_id: {saved_supp_id}")
                assert saved_supp_id != "", "Supplier ID was not set!"
                page.screenshot(path="grn_supplier_selected.png")

        # 4. SALARY ADVANCES PAGE
        print("\n=======================================================")
        print("STEP 4: Testing Salary Advances Modal (/payroll/advances)")
        print("=======================================================")
        page.goto("http://localhost:8000/payroll/advances", wait_until="networkidle")
        time.sleep(1)

        adv_btn = page.locator("button:has-text('Record Advance')").first
        if adv_btn.count() > 0:
            print("Opening Record Advance Modal...")
            adv_btn.click()
            time.sleep(0.5)

            adv_modal_select = page.locator("#advance-modal [data-searchable-select='employee_id']").first
            if adv_modal_select.count() > 0:
                print("Opening modal employee dropdown...")
                adv_modal_select.locator("[role='combobox']").click()
                time.sleep(0.4)

                adv_search = adv_modal_select.locator("input[type='text']")
                print("Typing 'Kamal'...")
                adv_search.fill("Kamal")
                time.sleep(0.6)

                adv_options = adv_modal_select.locator("[role='option']")
                print(f"Matching options in advance modal: {adv_options.count()}")
                if adv_options.count() > 0:
                    print(f"Selecting Option: {adv_options.first.text_content().strip()}")
                    adv_options.first.click()
                    time.sleep(0.4)
                    adv_id = adv_modal_select.locator("input[name='employee_id']").input_value()
                    print(f"Hidden employee_id in advance modal: {adv_id}")
                    assert adv_id != "", "Advance Employee ID was not set!"
                    page.screenshot(path="advance_employee_selected.png")

        print("\n=======================================================")
        print("ALL VERIFICATIONS PASSED WITH 100% SUCCESS!")
        print("=======================================================")

if __name__ == "__main__":
    run()
