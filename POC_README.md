# SQL Injection Proof of Concept (POC)

Python-based proof-of-concept script demonstrating session-based SQL injection vulnerabilities discovered in the Gambio e-commerce application security audit.

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

- **get_basename()** - URL path basename extraction utility
- Session fixation testing
- SQL injection payload demonstration
- Vulnerability verification
- Remediation code generation
- Color-coded output for readability

## Requirements

```bash
pip install requests
```

Or use Python 3.x with built-in libraries only (requests is optional).

## Usage

### Basic Usage

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

#### 1. Display Remediation Code

```bash
python3 poc_sql_injection.py --remediation
```

This will output secure coding examples showing how to fix the vulnerabilities using:
- Integer casting for session variables
- Prepared statements with parameter binding
- Session validation functions

#### 2. Test get_basename() Function

```python
from poc_sql_injection import get_basename

# Extract filename from URLs
print(get_basename("https://example.com/shop/product.php?id=123"))
# Output: product.php

print(get_basename("/includes/classes/order.php"))
# Output: order.php
```

#### 3. Authorized Security Testing

```bash
# Full vulnerability assessment
python3 poc_sql_injection.py -u https://target-shop.com
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
