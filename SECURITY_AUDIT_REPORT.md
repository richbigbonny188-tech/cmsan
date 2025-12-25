# Security Audit Report: Gambio GX4 E-Commerce Platform

**Date:** 2025-12-25  
**Target:** Gambio GX4 E-Commerce Platform (v4.9.x)  
**Stack:** PHP, MySQL, Smarty Template Engine, JavaScript  
**Auditor:** Security Analysis Bot  

---

## Executive Summary

This white-box security audit examined the Gambio GX4 e-commerce platform codebase for exploitable security vulnerabilities. The analysis focused on externally reachable entry points, data flow tracing, and exploitability assessment.

**Overall Assessment:** The codebase implements several security mechanisms including:
- GProtector input filtering framework
- Parameterized queries via mysqli prepared statements and escape functions  
- Token-based authentication for sensitive operations
- HMAC/secret hash verification for payment callbacks

However, the audit identified security issues that require attention.

---

## PHASE 1 — ENTRYPOINT MAPPING

### A) HTTP Endpoints (Frontend)

| File Path | Handler | Transport | Method(s) | Authentication |
|-----------|---------|-----------|-----------|----------------|
| `/index.php` | MainApplication | HTTP | GET/POST | None (public) |
| `/shop.php` | MainApplication | HTTP | GET/POST | None (public) |
| `/api.php` | REST API v2 | HTTP | GET/POST/PUT/DELETE | API Token |
| `/api_v3.php` | REST API v3 | HTTP | GET/POST/PUT/DELETE | API Token |
| `/request_port.php` | AJAX Router | HTTP | GET/POST | Session-based |
| `/login_admin.php` | Admin Login | HTTP | GET/POST | Pre-auth |
| `/findologic_export.php` | Product Export | HTTP | GET | Shop Key |

### B) Payment Callback Handlers

| File Path | Handler | Transport | Method | Authentication |
|-----------|---------|-----------|--------|----------------|
| `/ext/heidelpay/heidelpayGW_push.php` | Heidelpay Push | HTTP POST (XML) | POST | HMAC Secret |
| `/ext/heidelpay/heidelpayGW_response.php` | Heidelpay Response | HTTP | POST | HMAC Secret |
| `/callback/sofort/callback.php` | Sofort Callback | HTTP | POST | Payment Secret |
| `/callback/postfinance/` | PostFinance | HTTP | POST | SHA Hash |
| `/gambio_hub_callback.php` | Hub Callback | HTTP | POST | Hub Key |
| `/api-it-recht-kanzlei.php` | IT-Recht API | HTTP | POST | Auth Token |

### C) Admin/Installer Endpoints

| File Path | Handler | Transport | Method | Authentication |
|-----------|---------|-----------|--------|----------------|
| `/gambio_updater/` | Auto Updater | HTTP | GET/POST | Admin Cookie |
| `/gambio_installer/` | Installer | HTTP | GET/POST | None (setup) |
| `/gambio_store.php` | Store Update | HTTP | GET | Admin Cookie |
| `/magnaCallback.php` | Magnalister | HTTP | GET/POST | Passphrase |

---

## PHASE 2 — DATA FLOW TRACE & SECURITY FINDINGS

### FINDING 1: Unsafe Deserialization in magnaCallback.php

**Severity:** HIGH (Authentication Protected)

**Location:** `/magnaCallback.php` lines 859-863

**Data Flow:**
```
[ENTRYPOINT] POST request to /magnaCallback.php
[SOURCE] $_POST['arguments'], $_POST['includes']
[TRANSFORMATIONS] None - raw user input
[SINK] unserialize() function
[USER CONTROL PRESERVED] YES
```

**Code Analysis:**
```php
if ((MAGNA_CALLBACK_MODE == 'STANDALONE') &&
    array_key_exists('passphrase', $_POST) &&
    ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) &&
    array_key_exists('function', $_POST)
) {
    $arguments = array_key_exists('arguments', $_POST) ? unserialize($_POST['arguments']) : array();
    $includes = array_key_exists('includes', $_POST) ? unserialize($_POST['includes']) : array();
    // ...
    echo magnaEncodeResult(magnaExecute($_POST['function'], $arguments, $includes));
}
```

**Exploitability Analysis:**
- The vulnerability requires knowledge of the `general.passphrase` stored in the database
- If an attacker obtains this passphrase (via other vulnerabilities, credential theft, or insider access), they can craft malicious serialized PHP objects
- PHP Object Injection can lead to Remote Code Execution (RCE) depending on available gadget chains

**Mitigation Status:** Authentication-protected, but passphrase compromise enables exploitation

**Proof Evidence Required:**
- Valid passphrase value
- Serialized payload with gadget chain for the specific application

---

### FINDING 2: Session Cache Deserialization in api.php

**Severity:** LOW (File system access required)

**Location:** `/api.php` line 226

**Data Flow:**
```
[ENTRYPOINT] API request to /api.php
[SOURCE] File: cache/gxapi_v2_sessions_{SECURITY_TOKEN}
[TRANSFORMATIONS] file_get_contents() → unserialize()
[SINK] Session array operations
[USER CONTROL PRESERVED] NO (file controlled by app)
```

**Code Analysis:**
```php
$cacheFilePath = DIR_FS_CATALOG . 'cache/gxapi_v2_sessions_' . FileLog::get_secure_token();
if (!file_exists($cacheFilePath)) {
    touch($cacheFilePath);
    $sessions = [];
} else {
    $sessions = unserialize(file_get_contents($cacheFilePath));
}
```

**Exploitability Analysis:**
- The cache file path includes `APP_SECURITY_TOKEN` from environment configuration
- Token is generated with `bin2hex(random_bytes(16))` - cryptographically secure 32-char hex
- Attacker would need to write to the cache file (requires file system access)
- Not exploitable from external network access alone

**Control Elimination:**
- User control eliminated by secure token in filename (line 221)
- File write requires prior file system compromise

---

### FINDING 3: XML External Entity (XXE) Risk in Payment Callbacks

**Severity:** MITIGATED

**Location:** `/ext/heidelpay/heidelpayGW_push.php` line 24

**Data Flow:**
```
[ENTRYPOINT] POST request with XML body
[SOURCE] php://input (raw POST body)
[TRANSFORMATIONS] preg_replace() → simplexml_load_string()
[SINK] XML parsing for transaction data
[USER CONTROL PRESERVED] YES (XML structure)
```

**Code Analysis:**
```php
$rawPost = file_get_contents('php://input');
$rawPost = preg_replace('/<Criterion(\s+)name="(\w+)">(.+)<\/Criterion>/', '<$2>$3</$2>',$rawPost);
$xml = simplexml_load_string($rawPost);
```

**Exploitability Analysis:**
- Modern PHP (7.x+) has `LIBXML_NOENT` disabled by default
- simplexml_load_string() does not expand external entities unless explicitly enabled
- Hash verification at line 31 provides additional protection against tampering

**Control Elimination:**
- Hash verification (`$crit_Secret != $orgHash`) blocks tampered payloads
- PHP's default XXE protection since 7.x

---

### FINDING 4: Open Redirect via Database URLs

**Severity:** LOW (Admin-controlled data)

**Location:** `/system/classes/url_handling/RedirectProcess.inc.php`

**Data Flow:**
```
[ENTRYPOINT] GET /redirect.php?action=banner&goto={id}
[SOURCE] Database lookup of banners_url, products_url, manufacturers_url
[TRANSFORMATIONS] Integer cast for lookup ID → URL from DB
[SINK] HTTP redirect via set_redirect_url()
[USER CONTROL PRESERVED] NO (database controlled)
```

**Exploitability Analysis:**
- Redirect URLs are stored in database by administrators
- User cannot inject arbitrary redirect URLs
- Risk only exists if admin stores malicious URL in database

**Control Elimination:**
- URL source is database, not user input
- Integer casting on lookup ID prevents SQL injection

---

### FINDING 5: ec_proxy.php - Limited SSRF Risk

**Severity:** LOW (Partial control)

**Location:** `/ec_proxy.php` lines 35-63

**Data Flow:**
```
[ENTRYPOINT] GET /ec_proxy.php?prx={google_path}
[SOURCE] $_GET['prx']
[TRANSFORMATIONS] parse_url() → path extraction → prepend fixed host
[SINK] curl_exec() to https://www.google-analytics.com
[USER CONTROL PRESERVED] PARTIAL (path only)
```

**Code Analysis:**
```php
$gPath = $query['prx'];
$parsedGPath = parse_url($gPath);
$gUrl = 'https://www.google-analytics.com' . $parsedGPath['path'];
$finalUrl = $gUrl . '?' . http_build_query($query);
$gCurl = curl_init($finalUrl);
```

**Exploitability Analysis:**
- Host is hardcoded to `www.google-analytics.com`
- Attacker can only control path portion
- No actual SSRF as destination is fixed

**Control Elimination:**
- Fixed host prevents arbitrary SSRF
- Path manipulation limited to Google Analytics API endpoints

---

## PHASE 3 — CONTROL ELIMINATION SUMMARY

| Finding | Control Point | Reason for Elimination/Risk |
|---------|--------------|----------------------------|
| magnaCallback unserialize | Passphrase check (line 856) | Auth required - NOT eliminated |
| api.php unserialize | Secure token filename | Control eliminated - requires file access |
| Heidelpay XXE | PHP defaults + hash check | Control eliminated - no XXE possible |
| redirect.php | Database source | Control eliminated - admin data only |
| ec_proxy.php | Fixed host | Control eliminated - no SSRF |

---

## PHASE 4 — EXPLOITABILITY ASSESSMENT

### Confirmed Vulnerability

**magnaCallback.php PHP Object Injection**

| Aspect | Details |
|--------|---------|
| Vulnerability Class | CWE-502: Deserialization of Untrusted Data |
| Precondition | Knowledge of `general.passphrase` database value |
| Attack Vector | POST request with malicious serialized objects |
| Observable Impact | RCE if gadget chain exists, information disclosure |
| Evidence Required | Successful code execution, file creation, or network callback |

### Conditions for Exploitation:
1. Attacker must obtain the magnalister passphrase (e.g., via SQL injection elsewhere, insider access, or configuration leak)
2. A usable PHP gadget chain must exist in the autoloaded classes
3. Attacker sends POST to `/magnaCallback.php` with:
   - `passphrase`: valid passphrase
   - `function`: any value
   - `arguments` or `includes`: malicious serialized payload

---

## PHASE 5 — CHAINING ANALYSIS

No provable exploitation chain was identified that doesn't require prior authentication or credential access.

**Theoretical Chain (requires prior compromise):**
```
[SQL Injection or Config Leak] → [Passphrase Obtained] → [magnaCallback.php] → [RCE via Object Injection]
```

This chain cannot be proven without first demonstrating the credential leak.

---

## RECOMMENDATIONS

### Critical Priority

1. **Replace unserialize() with JSON in magnaCallback.php**
   - Convert serialized data format to JSON
   - Use `json_decode()` instead of `unserialize()`
   - Alternatively, use PHP 7+ allowed_classes option: `unserialize($data, ['allowed_classes' => false])`

### High Priority

2. **Add XML Entity Expansion Protection**
   - Explicitly set `libxml_disable_entity_loader(true)` before XML parsing (for PHP < 8.0)
   - Use `LIBXML_NONET` flag in simplexml_load_string()

### Medium Priority

3. **Review Session Cache Serialization**
   - Consider migrating to JSON format for session cache
   - Add integrity verification (HMAC) for cached data

---

## ADDITIONAL FINDINGS FROM CODE REVIEW

### FINDING 6: Input Validation Gap in yatego.php

**Severity:** LOW

**Location:** `/yatego.php` lines 15-18, 26-27

**Issue:** The `$_GET['mode']` parameter is used without validation or sanitization before being passed to the CYExportYatego constructor and execution.

**Recommendation:** Validate `mode` against a whitelist of allowed values before processing.

---

### FINDING 7: Type Juggling Risk in trusted_shops_cron.php

**Severity:** LOW

**Location:** `/trusted_shops_cron.php` lines 25-27

**Issue:** Using loose comparison (`==`) instead of strict comparison (`===`) for security token validation could potentially lead to type juggling vulnerabilities.

**Recommendation:** Use strict comparison (`===`) for all security-sensitive token comparisons.

---

## FINAL CONCLUSION

**No exploitable vulnerabilities were proven** that can be exploited from an external attacker without prior credential access or system compromise.

The PHP Object Injection in `magnaCallback.php` is the most significant finding, but it requires authentication (passphrase knowledge) to exploit. This makes it a defense-in-depth concern rather than an immediately exploitable vulnerability.

The Gambio GX4 platform employs reasonable security controls including:
- Input filtering via GProtector
- SQL parameterization and escaping
- Token-based authentication for APIs
- HMAC verification for payment callbacks
- Secure token generation for sensitive file paths

---

*This report is suitable for submission to the system owner for responsible disclosure.*
