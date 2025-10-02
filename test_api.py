#!/usr/bin/env python3
"""
Unit tests for SEL Diagnostic Center API endpoints
"""

import unittest
import requests
import json
from urllib.parse import urljoin


class TestDiagnosticCenterAPI(unittest.TestCase):
    """Test suite for the Diagnostic Center API"""

    def setUp(self):
        """Set up test environment"""
        self.base_url = "http://localhost:8000"
        self.session = requests.Session()
        self.csrf_token = None
        self.login_credentials = {
            'username': 'admin',
            'password': 'password'
        }

    def get_csrf_token(self):
        """Get CSRF token from login page"""
        response = self.session.get(urljoin(self.base_url, '/'))
        self.assertEqual(response.status_code, 200)

        # Extract CSRF token from response
        import re
        csrf_match = re.search(r'name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']', response.text)
        if csrf_match:
            self.csrf_token = csrf_match.group(1)
        else:
            self.fail("CSRF token not found")
        return self.csrf_token

    def authenticate(self):
        """Authenticate with the application"""
        self.get_csrf_token()
        login_data = {
            'username': self.login_credentials['username'],
            'password': self.login_credentials['password'],
            'csrf_token': self.csrf_token
        }

        response = self.session.post(urljoin(self.base_url, '/'), data=login_data)
        # Should redirect to dashboard on successful login
        self.assertIn(response.status_code, [200, 302])

    def test_login_page_loads(self):
        """Test that login page loads correctly"""
        response = self.session.get(urljoin(self.base_url, '/'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('Login', response.text)
        self.assertIn('csrf_token', response.text)

    def test_authentication_required(self):
        """Test that protected routes require authentication"""
        # Create a fresh session to ensure no authentication
        fresh_session = requests.Session()
        protected_routes = ['/dashboard', '/invoices', '/tests']

        for route in protected_routes:
            response = fresh_session.get(urljoin(self.base_url, route), allow_redirects=False)
            # Should redirect to login
            self.assertEqual(response.status_code, 302)

    def test_successful_login(self):
        """Test successful login with valid credentials"""
        self.authenticate()

        # Test access to dashboard after login
        response = self.session.get(urljoin(self.base_url, '/dashboard'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('Dashboard', response.text)

    def test_invalid_login(self):
        """Test login with invalid credentials"""
        self.get_csrf_token()
        login_data = {
            'username': 'invalid_user',
            'password': 'wrongpass',
            'csrf_token': self.csrf_token
        }

        response = self.session.post(urljoin(self.base_url, '/'), data=login_data)
        self.assertIn(response.status_code, [200, 302])

    def test_dashboard_access(self):
        """Test dashboard access and data loading"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/dashboard'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('Total Invoices', response.text)
        self.assertIn('Total Revenue', response.text)

    def test_invoices_listing(self):
        """Test invoices listing page"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/invoices'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('Invoices', response.text)

    def test_invoice_creation_page(self):
        """Test invoice creation page access"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/invoices/create'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('Create Invoice', response.text)

    def test_tests_listing(self):
        """Test tests listing page"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/tests'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('Tests', response.text)

    def test_api_endpoints_unauthorized(self):
        """Test API endpoints return 401 for unauthorized access"""
        api_endpoints = [
            '/api/dashboard-stats',
            '/api/dashboard/stats',
            '/api/invoices/recent'
        ]

        for endpoint in api_endpoints:
            response = self.session.get(urljoin(self.base_url, endpoint))
            self.assertEqual(response.status_code, 401)

    def test_api_dashboard_stats(self):
        """Test dashboard statistics API"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/api/dashboard-stats'))
        self.assertEqual(response.status_code, 200)

        data = response.json()
        self.assertIn('total_invoices', data)
        self.assertIn('total_tests', data)
        self.assertIn('total_revenue', data)

    def test_404_error_handling(self):
        """Test 404 error for non-existent routes"""
        response = self.session.get(urljoin(self.base_url, '/nonexistent-route'))
        self.assertEqual(response.status_code, 404)

    def test_csrf_protection(self):
        """Test CSRF protection on POST requests"""
        self.authenticate()

        # Try to create invoice without CSRF token
        invoice_data = {
            'patient_name': 'Test Patient',
            'patient_phone': '1234567890',
            'tests': ['CBC']
        }

        response = self.session.post(urljoin(self.base_url, '/invoices/store'), data=invoice_data, allow_redirects=False)
        # Should redirect due to missing/invalid CSRF
        self.assertEqual(response.status_code, 302)

    def test_responsive_design(self):
        """Test responsive design meta tags"""
        response = self.session.get(urljoin(self.base_url, '/'))
        self.assertEqual(response.status_code, 200)
        self.assertIn('viewport', response.text)
        self.assertIn('width=device-width', response.text)


class TestDatabaseConnectivity(unittest.TestCase):
    """Test database connectivity and data integrity"""

    def setUp(self):
        """Set up test environment"""
        self.base_url = "http://localhost:8000"
        self.session = requests.Session()

    def authenticate(self):
        """Authenticate for database tests"""
        # Get CSRF token
        response = self.session.get(urljoin(self.base_url, '/'))
        import re
        csrf_match = re.search(r'name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']', response.text)
        csrf_token = csrf_match.group(1) if csrf_match else ""

        # Login
        login_data = {
            'username': 'admin',
            'password': 'password',
            'csrf_token': csrf_token
        }
        self.session.post(urljoin(self.base_url, '/'), data=login_data)

    def test_database_connection(self):
        """Test that database connection is working"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/api/dashboard-stats'))
        self.assertEqual(response.status_code, 200)

        data = response.json()
        # If we get valid JSON response, database is connected
        self.assertIsInstance(data, dict)

    def test_data_integrity(self):
        """Test basic data integrity"""
        self.authenticate()

        response = self.session.get(urljoin(self.base_url, '/api/dashboard-stats'))
        data = response.json()

        # Check that numeric values are valid
        self.assertGreaterEqual(data.get('total_invoices', 0), 0)
        self.assertGreaterEqual(data.get('total_tests', 0), 0)
        self.assertGreaterEqual(data.get('total_revenue', 0), 0)


if __name__ == '__main__':
    print("Running SEL Diagnostic Center API Tests...")
    print("=" * 50)

    # Run tests
    unittest.main(verbosity=2)