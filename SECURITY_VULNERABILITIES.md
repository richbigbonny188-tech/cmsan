# Security Vulnerabilities Found in Gambio GX4

## Critical Vulnerabilities

### 1. Code Injection via eval() - CRITICAL
**File:** `inc/xtc_address_format.inc.php:101`
```php
eval("\$address = \"$fmt\";");
```
The `$fmt` variable comes from database (`address_format['format']`) but could be manipulated if an attacker gains database access or through SQL injection elsewhere.

### 2. Remote Code Execution via eval() in CSV Import - CRITICAL  
**File:** `system/classes/csv/CSVImportFunctionLibrary.inc.php:658`
```php
eval('$this->' . trim($t_function) . '($t_params);');
```
Function name comes from import mapping configuration. Dangerous if import configuration can be manipulated.

### 3. PHP Object Injection via unserialize() - CRITICAL
**File:** `magnaCallback.php:859-862`
```php
$arguments = array_key_exists('arguments', $_POST) ? unserialize($_POST['arguments']) : array();
$includes = array_key_exists('includes', $_POST) ? unserialize($_POST['includes']) : array();
```
Direct deserialization of user input from POST data - can lead to Remote Code Execution.

## High Severity Vulnerabilities

### 4. Multiple eval() in SOAP Libraries
**Files:**
- `gm/classes/lib/class.soap_server.php:615`
- `gm/classes/lib/nusoap.php:4073`
- `gm/classes/lib/nusoap.php:7867-7869`
- `gm/classes/lib/class.soapclient.php:710-712`

These eval() calls process data from SOAP requests.

### 5. eval() in ZIP Library
**File:** `gambio_updater/classes/zip_creator/pclzip.lib.php`
Multiple eval() calls at lines 2817, 3030, 4068, 4342, 4392, 4468.

### 6. eval() in Smarty Template Engine
**File:** `GXMainComponents/View/GXSmarty.inc.php:206-208`
```php
eval($content);
eval($content . ';');
```

### 7. eval() in MainFactory
**File:** `system/core/MainFactory.inc.php:306`
```php
eval($evalCache[$classWithNamespace]['code']);
```

## Medium Severity Vulnerabilities

### 8. Disabled SSL Certificate Verification
**Files:**
- `callback/sofort/library/sofortLib_http.inc.php:122`
- `callback/sofort/library/helper/class.invoice.inc.php:990`
- `system/classes/external/protected_shops/ProtectedShops.inc.php:146`

All set `CURLOPT_SSL_VERIFYPEER` to `false`, enabling MITM attacks.

### 9. Weak Random Token Generation
**Files:**
- `GProtector/classes/GProtector.inc.php:451`: `md5(time() . rand())`
- `system/classes/security/PageToken.inc.php:35`: `md5(time() . rand() ...)`
- `includes/classes/class.heidelpaygw.php:56`: `sha1(mt_rand(...))`

Uses predictable rand()/mt_rand() for security tokens.

### 10. Potential Open Redirect
**Files:**
- `styleedit/index.php:12`: Uses `$_SERVER['QUERY_STRING']` in redirect
- `system/overloads/PostUpdateShopExtenderComponent/StyleEdit3To4ThemeConverter.inc.php:346`: Uses `$_SERVER['REQUEST_URI']` in redirect

### 11. File Upload Vulnerabilities
**Files:**
- `gm/classes/GMLogoManager.php:215`
- `gm/classes/GMGPrintFileManager.php:63`
- `system/classes/csv/CSVSource.php:1966`

File uploads that may not properly validate file types or content.

## Low Severity Vulnerabilities

### 12. Hardcoded Salt in Coupon Generation
**File:** `inc/create_coupon_code.inc.php:33`
```php
function create_coupon_code($salt="secret", ...)
```
Default salt value is "secret".

### 13. XML External Entity (XXE) Risk
Multiple XML parsing functions without explicit external entity handling:
- `gm/classes/lib/class.wsdl.php`
- `gm/classes/lib/class.xmlschema.php`
- `gm/classes/lib/class.soap_parser.php`
- `gm/classes/lib/nusoap.php`

---

## Summary

| Severity | Count |
|----------|-------|
| Critical | 3     |
| High     | 4     |
| Medium   | 4     |
| Low      | 2     |

**Total: 13 vulnerability categories identified**

The most critical vulnerabilities are the unserialize() call in magnaCallback.php and the eval() calls that process external data.
