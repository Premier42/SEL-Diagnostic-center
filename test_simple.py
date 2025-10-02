#!/usr/bin/env python3
import requests
import re

def test_login_flow():
    session = requests.Session()
    base_url = "http://localhost:8000"

    print("Testing SEL Diagnostic Center...")

    # Step 1: Get login page
    print("1. Getting login page...")
    response = session.get(base_url)
    if response.status_code != 200:
        print(f"❌ Login page failed: {response.status_code}")
        return False
    print("✅ Login page loaded")

    # Step 2: Extract CSRF token
    print("2. Extracting CSRF token...")
    csrf_match = re.search(r'name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']', response.text)
    if not csrf_match:
        print("❌ CSRF token not found")
        return False
    csrf_token = csrf_match.group(1)
    print(f"✅ CSRF token extracted: {csrf_token[:10]}...")

    # Step 3: Attempt login
    print("3. Attempting login...")
    login_data = {
        'username': 'admin',
        'password': 'password',
        'csrf_token': csrf_token
    }
    response = session.post(base_url, data=login_data, allow_redirects=False)
    if response.status_code not in [200, 302]:
        print(f"❌ Login failed: {response.status_code}")
        return False
    print("✅ Login successful")

    # Step 4: Test dashboard access
    print("4. Testing dashboard access...")
    response = session.get(f"{base_url}/dashboard")
    if response.status_code != 200:
        print(f"❌ Dashboard access failed: {response.status_code}")
        print(f"Response headers: {dict(response.headers)}")
        return False
    print("✅ Dashboard accessible")

    # Check if dashboard contains user info
    if 'user_id' in response.text or 'Dashboard' in response.text:
        print("✅ Dashboard shows authenticated content")
    else:
        print("⚠️  Dashboard might not be properly authenticated")

    # Step 5: Test API endpoint
    print("5. Testing API endpoint...")
    response = session.get(f"{base_url}/api/dashboard-stats")
    if response.status_code != 200:
        print(f"❌ API endpoint failed: {response.status_code}")
        return False
    print("✅ API endpoint working")

    # Step 6: Verify API response
    try:
        data = response.json()
        print(f"✅ API data: {list(data.keys())}")
        return True
    except:
        print("❌ API response not valid JSON")
        return False

if __name__ == "__main__":
    if test_login_flow():
        print("\n🎉 All tests passed!")
    else:
        print("\n💥 Tests failed!")