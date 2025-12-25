# Security Audit Part 6: SQL Injection Focus

## Date: 2024-12-25
## Target: Gambio GX eCommerce Platform
## Focus: SQL Injection Vulnerabilities

---

## Executive Summary

This audit focuses specifically on SQL Injection (SQLi) vulnerabilities in the Gambio GX codebase. While the application uses the `xtc_db_input()` function for sanitization in most places, several areas were identified with potential SQL injection vectors.

---

## SQL Injection Findings

### SQLi-001: Second-Order Code Injection via `xtc_address_format.inc.php`

**Severity:** Medium (Second-Order)  
**File:** `inc/xtc_address_format.inc.php`  
**Lines:** 26-101

**Vulnerability:**
The function uses `eval()` to format addresses based on a format string from the database:

```php
$address_format_query = xtc_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT
                                     . " where address_format_id = '" . $address_format_id . "'");
$address_format = xtc_db_fetch_array($address_format_query);
// ...
$fmt = $address_format['format'];
eval("\$address = \"$fmt\";");
```

**Attack Vector:**
1. Attacker gains admin access or exploits SQL injection to modify `address_format` table
2. Insert malicious format string: `${system($_GET[c])}`
3. When address is formatted, code execution occurs

**Mitigating Factors:**
- User data is escaped with `addslashes()`
- Format string comes from database (requires prior compromise)

**Remediation:**
Replace `eval()` with template variable replacement using `strtr()` or `preg_replace_callback()`.

---

### SQLi-002: Unsafe Query Builder Usage

**Severity:** Low-Medium  
**Files:** Multiple Controllers using Query Builder

**Example locations:**
- `GXModules/Gambio/Hub/Admin/Overloads/OrderExtenderComponent/KlarnaHubOrderExtender.inc.php:55`
- `GXModules/Gambio/Hub/Admin/Overloads/OrderExtenderComponent/EasyCreditHubOrderExtender.inc.php:51`

```php
$this->order = $this->queryBuilder->get_where('orders', ['orders_id' => $_GET['oID']])->row_array();
```

**Analysis:**
The query builder may not properly escape values in all cases. While CodeIgniter's query builder typically parameterizes queries, direct array passing with GET values should be validated.

**Remediation:**
Always cast numeric IDs: `(int)$_GET['oID']`

---

### SQLi-003: LIKE Clause Injection Risks

**Severity:** Low  
**File:** `GXMainComponents/Services/Core/Order/OrderListGenerator.inc.php`  
**Lines:** 347-363

```php
$match = $this->db->escape_like_str($keyword->asString());
// Used in multiple LIKE clauses:
'orders.orders_id LIKE "%' . $match . '%"'
'OR orders.customers_id LIKE "%' . $match . '%"'
// ...
```

**Analysis:**
While `escape_like_str()` is used, the surrounding quotes and wildcards are string-concatenated. This could potentially allow wildcard injection (`%`, `_`) that could cause performance issues or information disclosure.

**Remediation:**
Use parameterized queries with proper LIKE handling.

---

### SQLi-004: Session-Based Queries Without Type Casting

**Severity:** Low  
**Files:** Multiple files

**Example in `inc/xtc_update_whos_online.inc.php:32`:**
```php
$customer_query = xtc_db_query("select customers_firstname, customers_lastname from " 
    . TABLE_CUSTOMERS . " where customers_id = '" . $_SESSION['customer_id'] . "'");
```

**Analysis:**
Session data (`$_SESSION['customer_id']`) is used directly in SQL queries without type casting. While session values are typically server-controlled, session hijacking or manipulation could lead to SQL injection.

**Remediation:**
Always use `(int)$_SESSION['customer_id']` for numeric IDs.

---

### SQLi-005: Sofort Payment Module SQL Queries

**Severity:** Low-Medium (Admin Context)  
**File:** `callback/sofort/ressources/scripts/sofortOrders.php`  
**Lines:** 172-175

```php
$query_product = shopDbQuery('SELECT products_quantity, products_price, products_model, products_tax, products_name 
    FROM '.TABLE_ORDERS_PRODUCTS.' 
    WHERE orders_products_id = "'.HelperFunctions::escapeSql($_POST['opid_product'][$i]).'"');

$query_attributes = shopDbQuery("SELECT products_options, products_options_values, options_values_price, price_prefix 
    FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." 
    WHERE orders_id = '".shopDbInput($_GET['oID'])."' 
    AND orders_products_id = '".HelperFunctions::escapeSql($_POST['opid_product'][$i])."'");
```

**Analysis:**
Uses `HelperFunctions::escapeSql()` which uses `mysqli_real_escape_string()`, but mixed with `shopDbInput()`. Inconsistent escaping methods could lead to edge cases.

**Remediation:**
Use consistent sanitization method throughout.

---

### SQLi-006: `xtc_db_prepare_input` SQL Injection Filter Bypass

**Severity:** Medium  
**File:** `inc/xtc_db_prepare_input.inc.php`  
**Line:** 25

```php
$string = preg_replace('/union.*select.*from/i', '', $string);
```

**Analysis:**
This is a blacklist-based SQL injection filter that can be bypassed:
- Using comments: `un/**/ion sel/**/ect fr/**/om`
- Using case variations not caught
- Using other SQL injection techniques (CASE, subqueries, etc.)

**Bypass Examples:**
```
un%69on sel%65ct fr%6fm  (URL encoding)
un/**/ion/**/sel/**/ect/**/fr/**/om  (Comments)
uNiOn SeLeCt FrOm  (Case mixing)
```

**Remediation:**
Remove blacklist filter and rely on proper parameterized queries.

---

### SQLi-007: Dynamic Table/Column Names

**Severity:** Low  
**File:** `gm/inc/gm_get_conf.inc.php:33`

```php
$result = xtc_db_query("SELECT `key`, `value` FROM `gx_configurations` WHERE `key` LIKE '{$prefix}%'", ...);
```

**Analysis:**
The `$prefix` variable is used directly in the LIKE pattern. If this prefix comes from user-controllable sources, it could lead to query manipulation.

---

## Previously Identified SQL-Related Issues

### From Previous Audit Parts:

1. **Object Injection → SQL via Gadget Chains** (Critical)
   - `magnaCallback.php` unserialize could lead to arbitrary database operations

2. **SSRF → Database Access** (High)
   - `ec_proxy.php` could be used to access internal database endpoints

---

## Analysis of SQL Sanitization Functions

### `xtc_db_input()` - Primary Sanitization
```php
function xtc_db_input($string, $link = 'db_link') {
    if (function_exists('mysqli_real_escape_string')) {
        return mysqli_real_escape_string($$link, (string)$string);
    }
    return addslashes($string);
}
```

**Assessment:** Properly uses `mysqli_real_escape_string()` - SECURE for string values within quotes.

### `HelperFunctions::escapeSql()` - Sofort Module
```php
public static function escapeSql($string) {
    return mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string);
}
```

**Assessment:** Uses `mysqli_real_escape_string()` - SECURE but inconsistent with global function.

---

## SQL Injection Summary Table

| ID | Vulnerability | File | Severity | Auth Required | Exploitable |
|----|---------------|------|----------|---------------|-------------|
| SQLi-001 | Second-Order Code Injection | xtc_address_format.inc.php | Medium | Admin + Prior DB compromise | Low |
| SQLi-002 | Query Builder GET Parameter | Hub Extenders | Low-Medium | Admin | Medium |
| SQLi-003 | LIKE Wildcard Injection | OrderListGenerator.inc.php | Low | Admin | Low |
| SQLi-004 | Session-Based Queries | Multiple files | Low | Session Hijack | Low |
| SQLi-005 | Mixed Escaping Methods | sofortOrders.php | Low-Medium | Admin | Low |
| SQLi-006 | Filter Bypass | xtc_db_prepare_input.inc.php | Medium | None | Medium |
| SQLi-007 | Dynamic LIKE Pattern | gm_get_conf.inc.php | Low | Depends | Low |

---

## Exploitation Chains Involving SQL

### Chain A: SSRF → Config Leak → SQL Access
```
ec_proxy.php (SSRF, No Auth)
    → Access internal database management interface
    → Execute arbitrary SQL
```

### Chain B: SQLi Filter Bypass → Data Extraction
```
Input with bypass: "test' un/**/ion sel/**/ect * fr/**/om customers--"
    → Filter doesn't catch
    → SQL injection executed
```

### Chain C: Object Injection → Database Manipulation
```
magnaCallback.php (Object Injection)
    → Doctrine DBAL gadget chain
    → Execute arbitrary SQL
```

---

## Recommendations

### Immediate Actions
1. **Replace all `eval()` usage** with safe alternatives
2. **Remove blacklist-based SQL filters** (SQLi-006)
3. **Add type casting** to all numeric parameters in SQL queries

### Short-term Actions
1. Implement prepared statements throughout the codebase
2. Standardize SQL escaping functions
3. Add SQL injection detection in WAF

### Long-term Actions
1. Migrate to an ORM with built-in SQL injection protection
2. Implement Content Security Policy
3. Add automated SQL injection testing in CI/CD

---

## Conclusion

While Gambio GX uses `mysqli_real_escape_string()` for SQL sanitization in most places, several areas have potential vulnerabilities:

1. The blacklist-based SQL injection filter in `xtc_db_prepare_input.inc.php` can be bypassed
2. Second-order code injection via `eval()` in address formatting
3. Inconsistent escaping methods across different modules
4. Session values used without type casting

The most critical SQL-related risk is the combination of these issues with other vulnerabilities (Object Injection, SSRF) to create more sophisticated attack chains.

---

## Technical Details

### SQL Injection Bypass Payloads

For `xtc_db_prepare_input` bypass:
```sql
-- Original blocked pattern
union select from

-- Bypasses
un/**/ion sel/**/ect fr/**/om
uNiOn%20SeLeCt%20FrOm
union(select)from
union all select from
```

### Testing Commands

```bash
# Test SQLi filter bypass
curl "https://target/search.php?q=test' un/**/ion sel/**/ect password fr/**/om customers--"

# Test session-based SQLi (requires session manipulation)
# Manipulate PHPSESSID cookie to contain SQL
```

---

*Report generated as part of authorized security audit*
*For responsible disclosure only*
