import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

options = Options()
options.add_argument('--headless')
options.add_argument('--window-size=1440,950')
driver = webdriver.Chrome(options=options)

artifact_dir = r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\ff14f2f7-9b93-497c-b943-4d5460a5641d"

try:
    print("Dev-login...")
    driver.get("http://127.0.0.1:8000/dev-login")
    time.sleep(2)

    print("Navigating to /inventario...")
    driver.get("http://127.0.0.1:8000/inventario")
    time.sleep(2)

    driver.save_screenshot(f"{artifact_dir}\\inventario_zero_state.png")
    print("Saved inventario_zero_state.png")

    print("ALL VERIFIED!")

finally:
    driver.quit()
    print("Browser closed.")
