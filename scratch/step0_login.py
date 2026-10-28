import time
from playwright.sync_api import sync_playwright

def login():
    print("Connecting to visible Chrome on port 9333...")
    with sync_playwright() as p:
        browser = p.chromium.connect_over_cdp("http://127.0.0.1:9333")
        context = browser.contexts[0]
        page = context.pages[0] if context.pages else context.new_page()

        print("Navigating to login page...")
        page.goto("http://localhost:8000/login", wait_until="networkidle")
        time.sleep(1)

        # Check if already logged in (e.g. redirected to dashboard)
        if "dashboard" in page.url or page.locator("text=Sign Out").count() > 0 or page.locator("text=Dashboard").count() > 0:
            print("Already authenticated! URL:", page.url)
            page.screenshot(path="scratch/step0_authenticated.png")
            return

        print("Entering credentials...")
        page.fill("input[name='email']", "admin@gmail.com")
        time.sleep(0.5)
        page.fill("input[name='password']", "Ironman@123")
        time.sleep(0.5)
        
        print("Submitting login form...")
        page.click("button[type='submit']")
        page.wait_for_load_state("networkidle")
        time.sleep(1.5)

        print("Current URL after login:", page.url)
        page.screenshot(path="scratch/step0_authenticated.png")
        assert "login" not in page.url, "Login failed!"
        print("Login successful and verified on visible Chrome!")

if __name__ == "__main__":
    login()
