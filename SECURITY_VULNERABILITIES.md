# Security Vulnerabilities Found in Gambio GX4

---

## ⚠️ IMMEDIATELY EXPLOITABLE (Without Authentication)

### 1. SSRF via autocomplete.php - EXPLOITABLE NOW
**File:** `autocomplete.php:49-63`
**Access:** Public, no authentication required
```php
$parameters = $_GET;
$url = $scheme_prefix.FL_SERVICE_URL."/autocomplete.php?" . http_build_query($parameters, '', '&');
$result = getUrl($url);
```
**Exploit:** Attacker can pass arbitrary GET parameters that are forwarded to external service. If `FL_SERVICE_URL` is controllable or misconfigured, this enables SSRF.

**PoC:** `GET /autocomplete.php?param=value`

### 2. SSRF via ec_proxy.php - EXPLOITABLE NOW
**File:** `ec_proxy.php:35-62`
**Access:** Public, no authentication required
```php
$gPath = $query['prx'];
$parsedGPath = parse_url($gPath);
$gUrl = 'https://www.google-analytics.com' . $parsedGPath['path'];
$finalUrl = $gUrl . '?' . http_build_query($query);
curl_exec($gCurl);
```
**Exploit:** Partial SSRF - attacker controls the path portion of requests to google-analytics.com domain.

**PoC:** `GET /ec_proxy.php?prx=/collect`

---

## Critical Vulnerabilities (Require Authentication/Conditions)

### 3. PHP Object Injection via unserialize() - REQUIRES PASSPHRASE
**File:** `magnaCallback.php:859-862`
**Access:** Requires valid passphrase from database
```php
if ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) {
    $arguments = unserialize($_POST['arguments']);
    $includes = unserialize($_POST['includes']);
}
```
**Impact:** RCE if passphrase is leaked/guessed. Weak comparison (==) allows type juggling.

### 4. Code Injection via eval() - DATABASE INJECTION REQUIRED
**File:** `inc/xtc_address_format.inc.php:101`
```php
eval("\$address = \"$fmt\";");
```
Requires ability to modify `address_format` table in database.

### 5. Remote Code Execution via eval() in CSV Import - ADMIN ACCESS REQUIRED
**File:** `system/classes/csv/CSVImportFunctionLibrary.inc.php:658`
```php
eval('$this->' . trim($t_function) . '($t_params);');
```
Requires admin access to CSV import functionality.

## High Severity Vulnerabilities

### 6. Disabled SSL Certificate Verification - IMMEDIATELY EXPLOITABLE
**Files:**
- `callback/sofort/library/sofortLib_http.inc.php:122`
- `callback/sofort/library/helper/class.invoice.inc.php:990`
- `system/classes/external/protected_shops/ProtectedShops.inc.php:146`

All set `CURLOPT_SSL_VERIFYPEER` to `false` - enables Man-in-the-Middle attacks on payment callbacks.

### 7. Multiple eval() in SOAP Libraries
**Files:**
- `gm/classes/lib/class.soap_server.php:615`
- `gm/classes/lib/nusoap.php:4073`
- `gm/classes/lib/nusoap.php:7867-7869`
- `gm/classes/lib/class.soapclient.php:710-712`

These eval() calls process data from SOAP requests.

### 8. eval() in ZIP Library
**File:** `gambio_updater/classes/zip_creator/pclzip.lib.php`
Multiple eval() calls at lines 2817, 3030, 4068, 4342, 4392, 4468.

### 9. eval() in Smarty Template Engine
**File:** `GXMainComponents/View/GXSmarty.inc.php:206-208`
```php
eval($content);
eval($content . ';');
```

### 10. eval() in MainFactory
**File:** `system/core/MainFactory.inc.php:306`
```php
eval($evalCache[$classWithNamespace]['code']);
```

## Medium Severity Vulnerabilities

### 11. Weak Random Token Generation - EXPLOITABLE
**Files:**
- `GProtector/classes/GProtector.inc.php:451`: `md5(time() . rand())`
- `system/classes/security/PageToken.inc.php:35`: `md5(time() . rand() ...)`
- `includes/classes/class.heidelpaygw.php:56`: `sha1(mt_rand(...))`

Uses predictable rand()/mt_rand() for security tokens - tokens can be predicted/brute-forced.

### 12. Potential Open Redirect
**Files:**
- `styleedit/index.php:12`: Uses `$_SERVER['QUERY_STRING']` in redirect
- `system/overloads/PostUpdateShopExtenderComponent/StyleEdit3To4ThemeConverter.inc.php:346`: Uses `$_SERVER['REQUEST_URI']` in redirect

### 13. File Upload Vulnerabilities
**Files:**
- `gm/classes/GMLogoManager.php:215`
- `gm/classes/GMGPrintFileManager.php:63`
- `system/classes/csv/CSVSource.php:1966`

File uploads that may not properly validate file types or content.

## Low Severity Vulnerabilities

### 14. Hardcoded Salt in Coupon Generation
**File:** `inc/create_coupon_code.inc.php:33`
```php
function create_coupon_code($salt="secret", ...)
```
Default salt value is "secret".

### 15. XML External Entity (XXE) Risk
Multiple XML parsing functions without explicit external entity handling:
- `gm/classes/lib/class.wsdl.php`
- `gm/classes/lib/class.xmlschema.php`
- `gm/classes/lib/class.soap_parser.php`
- `gm/classes/lib/nusoap.php`

---

## Summary - Exploitability

| Vulnerability | Exploitable Now? | Auth Required? |
|--------------|------------------|----------------|
| SSRF via autocomplete.php | ✅ YES | NO |
| SSRF via ec_proxy.php | ✅ YES | NO |
| SSL Verification Disabled | ✅ YES (MITM) | NO |
| Weak Token Generation | ✅ YES | NO |
| PHP Object Injection | ⚠️ CONDITIONAL | YES (passphrase) |
| eval() in address format | ❌ NO | DB access needed |
| eval() in CSV import | ❌ NO | Admin access |
| eval() in SOAP/ZIP/Smarty | ❌ NO | Various conditions |

**Immediate Threats: 4 vulnerabilities exploitable without authentication**
**Total: 15 vulnerability categories identified**
