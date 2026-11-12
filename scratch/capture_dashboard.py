import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

options = Options()
options.add_argument('--headless')
options.add_argument('--window-size=1440,950')

driver = webdriver.Chrome(options=options)

try:
    print("Navigating to dev-login...")
    driver.get("http://127.0.0.1:8000/dev-login")
    time.sleep(2)
    print("Current URL:", driver.current_url)
    print("Page Title:", driver.title)

    # 1. Capture Full Expanded Desktop Dashboard
    driver.save_screenshot(r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\5bc2dafa-8e0a-4703-9c1f-cb74ef28eae6\scratch\dashboard_desktop.png")
    print("Saved dashboard_desktop.png")

    # 2. Toggle Collapse Sidebar
    toggle_btn = driver.find_element(By.ID, "btnSidebarCollapse")
    toggle_btn.click()
    time.sleep(1)
    driver.save_screenshot(r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\5bc2dafa-8e0a-4703-9c1f-cb74ef28eae6\scratch\dashboard_desktop_collapsed.png")
    print("Saved dashboard_desktop_collapsed.png")

    # 3. Mobile Viewport
    driver.set_window_size(412, 915)
    time.sleep(1)
    driver.save_screenshot(r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\5bc2dafa-8e0a-4703-9c1f-cb74ef28eae6\scratch\dashboard_mobile.png")
    print("Saved dashboard_mobile.png")

finally:
    driver.quit()
    print("Browser closed.")
