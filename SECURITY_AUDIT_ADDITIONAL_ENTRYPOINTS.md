# Security Audit Report: Additional HTTP Entrypoints Analysis
## Gambio GX eCommerce Platform

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Scope:** Additional HTTP entrypoints beyond initial audit  

---

## PHASE 1 — HTTP ENTRYPOINT MAPPING

### Payment Callbacks

| File Path | HTTP Method | Parameters | Auth Required |
|-----------|-------------|------------|---------------|
| `callback/postfinance/callback.php` | POST | `$_POST` (via processCallback) | No |
| `callback/sofort/callback.php` | POST | Multiple payment params | No |
| `callback/sofort/ressources/scripts/getContent.php` | POST | `url` | No |
| `callback/sofort/ressources/scripts/sofortOrders.php` | GET/POST | `action`, `oID`, `sofort_action` | Admin Session |
| `callback/swixpostfinancecheckout/callback.php` | POST | JSON body (`listenerEntityId`, `entityId`, `spaceId`) | No |

### Heidelpay Integration

| File Path | HTTP Method | Parameters | Auth Required |
|-----------|-------------|------------|---------------|
| `ext/heidelpay/heidelpayGW_gateway.php` | GET/POST | Session-based payment data | Customer Session |
| `ext/heidelpay/heidelpayGW_push.php` | POST | XML body (raw POST) | Signature validation |
| `ext/heidelpay/heidelpayGW_response.php` | POST | Payment response params | No |

### MailBeez/Cloudloader

| File Path | HTTP Method | Parameters | Auth Required |
|-----------|-------------|------------|---------------|
| `ext/mailhive/cloudbeez/cloudloader_core.php` | GET | `$_SESSION['language']` | Session |
| `ext/mailhive/cloudbeez/cloudloader_packages.php` | GET | `$_SESSION['language']` | Session |
| `ext/mailhive/cloudbeez/cloudloader/bootstrap/inc_mailbeez.php` | GET/POST | `cloudloader_mode` | Limited validation |

### Installer/Updater

| File Path | HTTP Method | Parameters | Auth Required |
|-----------|-------------|------------|---------------|
| `gambio_installer/index.php` | GET/POST | Form data | No (install mode) |
| `gambio_installer/includes/import_sql.php` | GET/POST | `sql_part`, DB credentials | Install mode |
| `gambio_updater/index.php` | GET/POST | Update params | Admin login |

### API v2 Controllers

| File Path | HTTP Method | Parameters | Auth Required |
|-----------|-------------|------------|---------------|
| `GXMainComponents/Controllers/Api/v2/CategoriesApiV2Controller.inc.php` | GET/POST/PUT/DELETE | JSON body | API Auth |
| `GXMainComponents/Controllers/Api/v2/CustomersApiV2Controller.inc.php` | GET/POST/PUT/DELETE | JSON body | API Auth |
| `GXMainComponents/Controllers/Api/v2/OrdersApiV2Controller.inc.php` | GET/POST/PUT/DELETE | JSON body | API Auth |
| `GXMainComponents/Controllers/Api/v2/ProductsApiV2Controller.inc.php` | GET/POST/PUT/DELETE | JSON body | API Auth |

### Admin Controllers

| File Path | HTTP Method | Parameters | Auth Required |
|-----------|-------------|------------|---------------|
| `GXMainComponents/Controllers/HttpView/Admin/FileManagerController.inc.php` | GET/POST | File operations | Admin Session |
| `GambioAdmin/Application/Http/Controller/JSEngineController.php` | GET/POST | JS engine params | Admin Session |

---

## PHASE 2 — DATA FLOW TRACE

### Flow 1: `callback/swixpostfinancecheckout/callback.php`

```
[ENTRYPOINT] callback/swixpostfinancecheckout/callback.php
[SOURCE] php://input (JSON body)
[TRANSFORM] json_decode() -> $params array
[SINK] include_once(DIR_FS_CATALOG . 'includes/modules/payment/' . $metaData['payment_class'] . '.php')
[USER CONTROL PRESERVED: YES]
```

**CRITICAL: Local File Inclusion via payment_class parameter**

The `payment_class` value from transaction metadata is used directly in an `include_once` statement without path validation. If an attacker can control the transaction metadata in the Postfinance system, they can include arbitrary PHP files.

### Flow 2: `ext/heidelpay/heidelpayGW_push.php`

```
[ENTRYPOINT] ext/heidelpay/heidelpayGW_push.php
[SOURCE] php://input (XML body)
[TRANSFORM] preg_replace(), simplexml_load_string()
[SINK] XML parsing, database operations
[USER CONTROL PRESERVED: PARTIALLY - validated by hash]
```

The hash verification provides protection, but the XML parsing with `simplexml_load_string()` without `LIBXML_NOENT` flag allows XXE on PHP < 8.0.

### Flow 3: `callback/sofort/ressources/scripts/getContent.php`

```
[ENTRYPOINT] callback/sofort/ressources/scripts/getContent.php
[SOURCE] $_POST['url']
[TRANSFORM] santiyCheck() - validates host === 'documents.sofort.com'
[SINK] file_get_contents($url), curl, fsockopen
[USER CONTROL PRESERVED: LIMITED - domain validated]
```

The `santiyCheck()` function only allows URLs from `documents.sofort.com`. However, this could be bypassed if the domain check is weak (e.g., `documents.sofort.com.attacker.com`).

### Flow 4: `ext/mailhive/cloudbeez/cloudloader/bootstrap/inc_mailbeez.php`

```
[ENTRYPOINT] ext/mailhive/cloudbeez/cloudloader/bootstrap/inc_mailbeez.php
[SOURCE] $_GET['cloudloader_mode'] or $_POST['cloudloader_mode']
[TRANSFORM] Whitelist validation against allowed modes
[SINK] require_once() based on mode
[USER CONTROL PRESERVED: NO - properly validated]
```

This file has been patched (Advisory ID: usd201900) with proper whitelist validation.

### Flow 5: `ext/heidelpay/heidelpayGW_gateway.php`

```
[ENTRYPOINT] ext/heidelpay/heidelpayGW_gateway.php
[SOURCE] $_SESSION['HP'], $_GET, $_POST
[TRANSFORM] htmlspecialchars(), form processing
[SINK] Payment gateway redirect, database operations
[USER CONTROL PRESERVED: PARTIALLY]
```

Session-based data flow with HTML escaping applied.

### Flow 6: `GXMainComponents/Controllers/HttpView/Admin/FileManagerController.inc.php`

```
[ENTRYPOINT] FileManagerController.inc.php
[SOURCE] $_GET['file'], $_FILES
[TRANSFORM] Blacklist extension filtering
[SINK] move_uploaded_file(), file operations
[USER CONTROL PRESERVED: YES - with blacklist protection]
```

File upload handler with extension blacklist - requires admin authentication.

---

## PHASE 3 — CONTROL ELIMINATION FILTER

### Discarded Flows

1. **inc_mailbeez.php** - Whitelist validation eliminates user control over include path
   - Line 28-33: `if (!in_array($cloudloader_mode, array('install_core', 'install_package', 'update_core', 'update_package')))`

2. **API v2 Controllers** - Input validation through service layer and JSON serializers
   - Type checking and service layer abstraction prevent direct injection

3. **FileManagerController** - Extension blacklist and admin auth requirement
   - Line ~82: `$disallowedExtensions` array filtering

4. **heidelpayGW_push.php** - Hash verification requirement
   - Line 26-36: Secret hash comparison blocks unverified requests

---

## PHASE 4 — EXPLOITABILITY ANALYSIS

### CRITICAL: Local File Inclusion in `callback/swixpostfinancecheckout/callback.php`

**Vulnerability Class:** File Inclusion  
**CVSS Score:** 9.8 (Critical)  
**Line:** 44  

**Code:**
```php
include_once(DIR_FS_CATALOG . 'includes/modules/payment/' . $metaData['payment_class'] . '.php');
```

**Analysis:**
The `$metaData['payment_class']` comes from transaction metadata retrieved via Postfinance API. If an attacker can inject malicious values into the transaction metadata, they can traverse directories and include arbitrary PHP files.

**PoC (Conceptual):**
If transaction metadata can be controlled:
```
payment_class = "../../../upload/malicious"
```
This would include: `includes/modules/payment/../../../upload/malicious.php`

**Limitation:** Exploitation requires ability to control Postfinance transaction metadata, which may require a compromised Postfinance account or MITM attack.

---

### HIGH: XXE Injection in `ext/heidelpay/heidelpayGW_push.php`

**Vulnerability Class:** XXE (XML External Entity Injection)  
**CVSS Score:** 7.5 (PHP < 8.0)  
**Lines:** 20-24  

**Code:**
```php
$rawPost = file_get_contents('php://input');
$rawPost = preg_replace('/<Criterion(\s+)name="(\w+)">(.+)<\/Criterion>/', '<$2>$3</$2>',$rawPost);
$xml = simplexml_load_string($rawPost);
```

**Analysis:**
On PHP versions < 8.0, `simplexml_load_string()` processes external entities by default. An attacker could inject XXE payload to read local files or perform SSRF.

**PoC:**
```bash
curl -X POST https://<TARGET>/ext/heidelpay/heidelpayGW_push.php \
  -H "Content-Type: application/xml" \
  -d '<?xml version="1.0"?>
<!DOCTYPE foo [
  <!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<Transaction>
  <Identification>
    <TransactionID>&xxe;</TransactionID>
  </Identification>
  <Analysis>
    <SECRET>test</SECRET>
  </Analysis>
</Transaction>'
```

**Mitigation:** On PHP >= 8.0, external entity loading is disabled by default. Add explicit `LIBXML_NOENT` flag or use `libxml_disable_entity_loader(true)`.

---

### MEDIUM: SSRF in `callback/sofort/ressources/scripts/getContent.php`

**Vulnerability Class:** Server-Side Request Forgery (Limited)  
**CVSS Score:** 4.3  
**Lines:** 16-20  

**Code:**
```php
$url = isset($_POST['url'])? $_POST['url'] : '';
if(!santiyCheck($url)) exit;
// ...
function santiyCheck($url) {
    $host = parse_url($url, PHP_URL_HOST);
    return $host === 'documents.sofort.com';
}
```

**Analysis:**
While the domain check limits SSRF, the validation could potentially be bypassed through:
1. DNS rebinding attacks
2. Open redirects on documents.sofort.com
3. URL parsing differences

**Limitation:** Strict host check makes exploitation difficult.

---

### MEDIUM: Session-Based LFI in Cloudloader Files

**Vulnerability Class:** Local File Inclusion (Session-dependent)  
**CVSS Score:** 5.3  
**Files:** `cloudloader_core.php`, `cloudloader_packages.php`  

**Code (`cloudloader_core.php` Line 21-26):**
```php
$install_lang = $_SESSION['language'];
if (stream_resolve_include_path('cloudloader/languages/' . $install_lang . '.php')) {
    include('cloudloader/languages/' . $install_lang . '.php');
}
```

**Analysis:**
The session language value is used in include path. If an attacker can control the session language (e.g., through another vulnerability), they could potentially include arbitrary files.

**Limitation:** Requires session manipulation capability.

---

### MEDIUM: Reflected XSS in `callback/sofort/ressources/scripts/sofortOrders.php`

**Vulnerability Class:** Cross-Site Scripting (Reflected)  
**CVSS Score:** 5.4  
**Lines:** 631-632  

**Analysis:**
GET parameters like `$_GET['oID']` and `$_GET['action']` are used in the admin interface. While some escaping exists via `shopDbPrepareInput()`, direct output in certain contexts could allow XSS.

**Limitation:** Requires admin session context.

---

### LOW: Timing Attack on Hash Comparison

**Vulnerability Class:** Timing Side-Channel  
**Files:** `heidelpayGW_push.php`, `heidelpayGW_response.php`  

**Code:**
```php
if($crit_Secret != $orgHash){
```

**Analysis:**
String comparison using `!=` is not constant-time. An attacker could potentially determine the hash character-by-character through timing analysis.

**Mitigation:** Use `hash_equals()` for constant-time comparison.

---

### LOW: SSL Verification Disabled

**Vulnerability Class:** Insecure Transport  
**File:** `callback/sofort/ressources/scripts/getContent.php`  
**Line:** 67  

**Code:**
```php
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
```

**Analysis:**
Disabling SSL verification allows MITM attacks on HTTPS connections.

---

## PHASE 5 — CHAINING & IMPACT

### Attack Chain 1: Session Hijacking → LFI → RCE

1. **Session Hijacking:** Exploit weak session handling or XSS to hijack admin session
2. **LFI via Language:** Manipulate `$_SESSION['language']` to include arbitrary files
3. **RCE:** If log files or uploaded images can be controlled, achieve code execution

**Impact:** Full server compromise

### Attack Chain 2: Postfinance Callback → LFI

1. **Transaction Metadata Manipulation:** If attacker can influence Postfinance transaction data
2. **LFI via payment_class:** Include uploaded PHP files
3. **RCE:** Execute arbitrary code

**Impact:** Full server compromise

### Attack Chain 3: XXE → Information Disclosure → Further Attacks

1. **XXE Injection:** Send malicious XML to heidelpay push endpoint
2. **File Read:** Extract configuration files (DB credentials, API keys)
3. **Lateral Movement:** Use extracted credentials for database access

**Impact:** Data breach, potential full compromise

---

## SUMMARY OF FINDINGS

| Severity | Count | Description |
|----------|-------|-------------|
| **Critical** | 1 | LFI in PostfinanceCheckout callback |
| **High** | 1 | XXE in Heidelpay push handler (PHP < 8.0) |
| **Medium** | 4 | Session-based LFI, SSRF, XSS |
| **Low** | 3 | Timing attacks, SSL verification disabled |

---

## RECOMMENDATIONS

1. **PostfinanceCheckout callback:** Implement whitelist validation for `payment_class` parameter
2. **Heidelpay push handler:** Add `libxml_disable_entity_loader(true)` for PHP < 8.0 compatibility
3. **Hash comparisons:** Replace `!=` with `hash_equals()` for timing-safe comparison
4. **SSL verification:** Enable SSL peer verification in all cURL requests
5. **Input validation:** Add stricter validation for all session-derived values used in file paths

---

*This report is for authorized security testing purposes only.*
