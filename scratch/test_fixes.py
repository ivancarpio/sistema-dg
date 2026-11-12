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
    time.sleep(1.5)

    # 1. Capture funnel updated
    driver.save_screenshot(r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\5bc2dafa-8e0a-4703-9c1f-cb74ef28eae6\scratch\funnel_updated.png")
    print("Saved funnel_updated.png")

    # 2. Click tier 10
    tiers = driver.find_elements(By.CLASS_NAME, "funnel-tier")
    if tiers:
        tiers[-1].click()
        time.sleep(0.8)
        driver.save_screenshot(r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\5bc2dafa-8e0a-4703-9c1f-cb74ef28eae6\scratch\funnel_modal_open.png")
        print("Saved funnel_modal_open.png")

    # 3. Test reportes personalizado
    print("Navigating to reportes/ventas...")
    driver.get("http://127.0.0.1:8000/reportes/ventas")
    time.sleep(1.5)

    btnPersonalizado = driver.find_element(By.ID, "btnPersonalizado")
    btnPersonalizado.click()
    print("Clicked btnPersonalizado, waiting 2 seconds to verify it stays open...")
    time.sleep(2)

    rango = driver.find_element(By.ID, "rangoPersonalizado")
    is_displayed = rango.is_displayed()
    print("Is rangoPersonalizado displayed after 2s?", is_displayed)

    driver.save_screenshot(r"C:\Users\PC-REMAX IT\.gemini\antigravity\brain\5bc2dafa-8e0a-4703-9c1f-cb74ef28eae6\scratch\reporte_personalizado_open.png")
    print("Saved reporte_personalizado_open.png")

finally:
    driver.quit()
    print("Finished.")
