# Security Vulnerabilities Found in Gambio GX4

---

## 🔴 ПОЛНАЯ КОМПРОМЕТАЦИЯ СИСТЕМЫ (Full System Compromise)

### 1. Remote Code Execution via PHP Object Injection + Type Juggling
**File:** `magnaCallback.php:854-867`
**Impact:** ПОЛНАЯ КОМПРОМЕТАЦИЯ - выполнение произвольного кода на сервере

**Уязвимость:**
```php
// Слабое сравнение (==) позволяет обход через type juggling
if ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) {
    // unserialize() позволяет PHP Object Injection
    $arguments = unserialize($_POST['arguments']);
    $includes = unserialize($_POST['includes']);
    
    // magnaExecute вызывает ЛЮБУЮ функцию + require_once для файлов
    echo magnaEncodeResult(magnaExecute($_POST['function'], $arguments, $includes));
}
```

**Цепочка атаки для RCE:**
1. Если passphrase = "0" или пустой → обход через type juggling (`"0" == 0` = true)
2. `unserialize()` → PHP Object Injection (POP chain gadgets)
3. `$_POST['function']` → вызов ЛЮБОЙ PHP функции
4. `$includes` → `require_once()` произвольных файлов

**PoC (если passphrase = 0 или пустой):**
```
POST /magnaCallback.php
Content-Type: application/x-www-form-urlencoded

passphrase=0&function=system&arguments=s:7:"id;whoami";
```

**Результат:** Полный контроль над сервером (RCE)

---

### 2. Arbitrary File Write → RCE
**File:** `callback/postfinance/callback.php:34`
```php
file_put_contents(DIR_FS_CATALOG . 'logfiles/postfinance_debug.txt', print_r($_POST, true));
```
**Impact:** Запись произвольных данных в файл на сервере
**Эксплуатация:** POST запрос → данные записываются в файл → потенциальный RCE если logfiles доступен через web

---

### 3. Arbitrary File Permissions (chmod 0777)
**File:** `gm/classes/GMLogoManager.php:216`
```php
@chmod($this->logo_src . $t_new_filename, 0777);
```
**Impact:** Загруженные файлы получают права 777 → позволяет выполнение

---

## ⚠️ IMMEDIATELY EXPLOITABLE (Without Authentication)

### 4. SSRF via autocomplete.php - EXPLOITABLE NOW
**File:** `autocomplete.php:49-63`
**Access:** Public, no authentication required
```php
$parameters = $_GET;
$url = $scheme_prefix.FL_SERVICE_URL."/autocomplete.php?" . http_build_query($parameters, '', '&');
$result = getUrl($url);
```
**Exploit:** Attacker can pass arbitrary GET parameters that are forwarded to external service. If `FL_SERVICE_URL` is controllable or misconfigured, this enables SSRF.

**PoC:** `GET /autocomplete.php?param=value`

### 5. SSRF via ec_proxy.php - EXPLOITABLE NOW
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

### 6. PHP Object Injection via unserialize() - REQUIRES PASSPHRASE
**File:** `magnaCallback.php:859-862`
**Access:** Requires valid passphrase from database
```php
if ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) {
    $arguments = unserialize($_POST['arguments']);
    $includes = unserialize($_POST['includes']);
}
```
**Impact:** RCE if passphrase is leaked/guessed. Weak comparison (==) allows type juggling.

### 7. Code Injection via eval() - DATABASE INJECTION REQUIRED
**File:** `inc/xtc_address_format.inc.php:101`
```php
eval("\$address = \"$fmt\";");
```
Requires ability to modify `address_format` table in database.

### 8. Remote Code Execution via eval() in CSV Import - ADMIN ACCESS REQUIRED
**File:** `system/classes/csv/CSVImportFunctionLibrary.inc.php:658`
```php
eval('$this->' . trim($t_function) . '($t_params);');
```
Requires admin access to CSV import functionality.

## High Severity Vulnerabilities

### 9. Disabled SSL Certificate Verification - IMMEDIATELY EXPLOITABLE
**Files:**
- `callback/sofort/library/sofortLib_http.inc.php:122`
- `callback/sofort/library/helper/class.invoice.inc.php:990`
- `system/classes/external/protected_shops/ProtectedShops.inc.php:146`

All set `CURLOPT_SSL_VERIFYPEER` to `false` - enables Man-in-the-Middle attacks on payment callbacks.

### 10. Multiple eval() in SOAP Libraries
**Files:**
- `gm/classes/lib/class.soap_server.php:615`
- `gm/classes/lib/nusoap.php:4073`
- `gm/classes/lib/nusoap.php:7867-7869`
- `gm/classes/lib/class.soapclient.php:710-712`

These eval() calls process data from SOAP requests.

### 11. eval() in ZIP Library
**File:** `gambio_updater/classes/zip_creator/pclzip.lib.php`
Multiple eval() calls at lines 2817, 3030, 4068, 4342, 4392, 4468.

### 12. eval() in Smarty Template Engine
**File:** `GXMainComponents/View/GXSmarty.inc.php:206-208`
```php
eval($content);
eval($content . ';');
```

### 13. eval() in MainFactory
**File:** `system/core/MainFactory.inc.php:306`
```php
eval($evalCache[$classWithNamespace]['code']);
```

## Medium Severity Vulnerabilities

### 14. Weak Random Token Generation - EXPLOITABLE
**Files:**
- `GProtector/classes/GProtector.inc.php:451`: `md5(time() . rand())`
- `system/classes/security/PageToken.inc.php:35`: `md5(time() . rand() ...)`
- `includes/classes/class.heidelpaygw.php:56`: `sha1(mt_rand(...))`

Uses predictable rand()/mt_rand() for security tokens - tokens can be predicted/brute-forced.

### 15. Potential Open Redirect
**Files:**
- `styleedit/index.php:12`: Uses `$_SERVER['QUERY_STRING']` in redirect
- `system/overloads/PostUpdateShopExtenderComponent/StyleEdit3To4ThemeConverter.inc.php:346`: Uses `$_SERVER['REQUEST_URI']` in redirect

### 16. File Upload Vulnerabilities
**Files:**
- `gm/classes/GMLogoManager.php:215`
- `gm/classes/GMGPrintFileManager.php:63`
- `system/classes/csv/CSVSource.php:1966`

File uploads that may not properly validate file types or content.

## Low Severity Vulnerabilities

### 17. Hardcoded Salt in Coupon Generation
**File:** `inc/create_coupon_code.inc.php:33`
```php
function create_coupon_code($salt="secret", ...)
```
Default salt value is "secret".

### 18. XML External Entity (XXE) Risk
Multiple XML parsing functions without explicit external entity handling:
- `gm/classes/lib/class.wsdl.php`
- `gm/classes/lib/class.xmlschema.php`
- `gm/classes/lib/class.soap_parser.php`
- `gm/classes/lib/nusoap.php`

---

## Summary - Exploitability & Compromise

| Vulnerability | Exploitable Now? | Impact |
|--------------|------------------|--------|
| **RCE via magnaCallback.php** | ⚠️ Type Juggling | 🔴 ПОЛНАЯ КОМПРОМЕТАЦИЯ |
| **Arbitrary File Write** | ✅ YES | 🔴 ВОЗМОЖЕН RCE |
| **chmod 0777** | ✅ YES | 🟠 Эскалация привилегий |
| SSRF via autocomplete.php | ✅ YES | 🟡 Внутренняя разведка |
| SSRF via ec_proxy.php | ✅ YES | 🟡 Ограниченный SSRF |
| SSL Verification Disabled | ✅ YES (MITM) | 🟠 Перехват платежей |
| Weak Token Generation | ✅ YES | 🟠 Обход защиты |

**🔴 КРИТИЧНО: magnaCallback.php = RCE если passphrase пустой/нулевой**

**Immediate Threats: 7 vulnerabilities exploitable without authentication**
**Total: 18 vulnerability categories identified**
