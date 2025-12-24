# Detailed Analysis: RCE via eval() in pclzip.lib.php
## Gambio GX eCommerce Platform - Updater Context

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Scope:** Deep-dive analysis of RCE via eval() in pclzip.lib.php  

---

## EXECUTIVE SUMMARY

The `pclzip.lib.php` library used in the Gambio GX updater contains multiple `eval()` calls that execute callback function names provided via options. While not directly exploitable from unauthenticated HTTP requests, this represents a significant security risk in the updater context.

---

## VULNERABILITY DETAILS

### Location

**Files Affected:**
- `gambio_updater/classes/zip_creator/pclzip.lib.php` (6 instances)
- `ext/mailhive/cloudbeez/cloudloader/php/pclzip.lib.php` (1 instance, commented out)
- `vendor/pclzip/pclzip/pclzip.lib.php` (similar pattern)

### Vulnerable Code

**Lines with eval():**

```php
// Line 2817 - PCLZIP_CB_PRE_ADD callback
eval('$v_result = '.$p_options[PCLZIP_CB_PRE_ADD].'(PCLZIP_CB_PRE_ADD, $v_local_header);');

// Line 3030 - PCLZIP_CB_POST_ADD callback
eval('$v_result = '.$p_options[PCLZIP_CB_POST_ADD].'(PCLZIP_CB_POST_ADD, $v_local_header);');

// Line 4068 - PCLZIP_CB_PRE_EXTRACT callback
eval('$v_result = '.$p_options[PCLZIP_CB_PRE_EXTRACT].'(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');

// Line 4342 - PCLZIP_CB_POST_EXTRACT callback
eval('$v_result = '.$p_options[PCLZIP_CB_POST_EXTRACT].'(PCLZIP_CB_POST_EXTRACT, $v_local_header);');

// Line 4392 - PCLZIP_CB_PRE_EXTRACT callback (duplicate)
eval('$v_result = '.$p_options[PCLZIP_CB_PRE_EXTRACT].'(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');

// Line 4468 - PCLZIP_CB_POST_EXTRACT callback
eval('$v_result = '.$p_options[PCLZIP_CB_POST_EXTRACT].'(PCLZIP_CB_POST_EXTRACT, $v_local_header);');
```

### Code Context

```php
// ----- Look for pre-add callback
if (isset($p_options[PCLZIP_CB_PRE_ADD])) {
    // ----- Generate a local information
    $v_local_header = array();
    $this->privConvertHeader2FileInfo($p_header, $v_local_header);

    // ----- Call the callback
    // Here I do not use call_user_func() because I need to send a reference to the
    // header.
    eval('$v_result = '.$p_options[PCLZIP_CB_PRE_ADD].'(PCLZIP_CB_PRE_ADD, $v_local_header);');
    
    if ($v_result == 0) {
        // ----- Change the file status
        $p_header['status'] = "skipped";
        $v_result = 1;
    }
    // ...
}
```

---

## EXPLOITATION ANALYSIS

### Attack Vector

The vulnerability exists in callback handling. The callback function name comes from `$p_options[PCLZIP_CB_*]` which is set during PclZip method calls:

```php
// Example of how callbacks are set
$archive = new PclZip('archive.zip');
$archive->extract(PCLZIP_CB_PRE_EXTRACT, 'user_callback_function');
```

### Exploit Scenario

If an attacker can control the callback function name, they can execute arbitrary PHP code:

```php
// Malicious callback name
$callback = 'system("id");//';

// This would result in:
eval('$v_result = system("id");//(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');

// Which executes: system("id")
```

### Current Usage in Gambio

The `ZipCreator.inc.php` wrapper class uses PclZip but **does not pass any callbacks**:

```php
// From ZipCreator.inc.php
public function createZip(array $p_fileList, $pathToRemove = null)
{
    $this->fileList = $p_fileList;
    $filelistString = implode(',', $this->fileList);
    
    // No callbacks passed - only PCLZIP_OPT_REMOVE_PATH
    $result = $this->pclZip->add($filelistString, PCLZIP_OPT_REMOVE_PATH, $pathToRemove);
    
    return $result;
}
```

---

## ATTACK SCENARIOS

### Scenario 1: Direct HTTP Exploitation (NOT POSSIBLE)

The current Gambio implementation does not expose a direct HTTP path to control PclZip callbacks. The updater uses ZipCreator which doesn't accept user-controlled callbacks.

**Status:** NOT EXPLOITABLE via HTTP

### Scenario 2: Code Injection via File Manipulation

If an attacker can modify PHP files in the codebase (via another vulnerability like file upload or SQL injection leading to file write), they could modify the ZipCreator class to pass malicious callbacks.

**Status:** REQUIRES PRIOR ACCESS

### Scenario 3: Supply Chain Attack

If a malicious update package is delivered via the Gambio update system, it could include modified PclZip code or wrapper code that uses malicious callbacks.

**Status:** THEORETICAL - Requires compromised update server

### Scenario 4: Third-Party Module Exploitation

Third-party modules that use PclZip directly could be vulnerable if they accept user-controlled options:

```php
// Hypothetical vulnerable third-party code
$archive = new PclZip($_POST['archive']);
$archive->extract(
    PCLZIP_OPT_PATH, $_POST['path'],
    PCLZIP_CB_PRE_EXTRACT, $_POST['callback']  // VULNERABLE!
);
```

**Status:** DEPENDS ON THIRD-PARTY MODULES

---

## PROOF OF CONCEPT

### Local Exploitation Test

```php
<?php
// PoC to demonstrate eval() vulnerability
// This would need to be executed in a context where PclZip is loaded

require_once 'pclzip.lib.php';

// Create a test archive
$archive = new PclZip('/tmp/test.zip');
$archive->create('/tmp/test.txt');

// Malicious callback name
$evil_callback = 'system($_GET["cmd"]);//';

// This would execute arbitrary commands
$archive->extract(
    PCLZIP_OPT_PATH, '/tmp/extract/',
    PCLZIP_CB_PRE_EXTRACT, $evil_callback
);
// Results in: eval('$v_result = system($_GET["cmd"]);//(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');
?>
```

### Request to Trigger (Hypothetical)

```bash
# If a vulnerable endpoint existed:
curl "https://<TARGET>/vulnerable_endpoint.php?archive=/tmp/test.zip&callback=system('id');//"
```

---

## SEVERITY ASSESSMENT

| Factor | Assessment |
|--------|------------|
| **Vulnerability Type** | Remote Code Execution via eval() |
| **Attack Complexity** | High - No direct HTTP path |
| **Privileges Required** | Depends on attack scenario |
| **User Interaction** | None |
| **Impact** | Critical if exploited - Full server compromise |
| **Scope** | Unchanged |

### CVSS 3.1 Score

**Base Score:** 7.2 (High) - In context of updater with auth  
**Adjusted Score:** 4.0 (Medium) - Due to lack of direct HTTP attack vector

---

## AFFECTED PCLZIP VERSIONS

This vulnerability exists in the PclZip library itself, affecting:
- PclZip 2.x (current in Gambio)
- All versions using callback-via-eval pattern

The issue is documented in the code comment:
```php
// Here I do not use call_user_func() because I need to send a reference to the header.
```

The comment explains why `call_user_func()` wasn't used, but the security implications of `eval()` were not considered.

---

## REMEDIATION RECOMMENDATIONS

### Immediate Actions

1. **Replace eval() with call_user_func_array():**

```php
// Current vulnerable code
eval('$v_result = '.$p_options[PCLZIP_CB_PRE_EXTRACT].'(PCLZIP_CB_PRE_EXTRACT, $v_local_header);');

// Safer alternative
if (is_callable($p_options[PCLZIP_CB_PRE_EXTRACT])) {
    $v_result = call_user_func_array(
        $p_options[PCLZIP_CB_PRE_EXTRACT],
        array(PCLZIP_CB_PRE_EXTRACT, &$v_local_header)
    );
}
```

2. **Validate callback names:**

```php
// Add validation before callback execution
$allowed_callbacks = ['my_pre_extract', 'my_post_extract'];
if (!in_array($p_options[PCLZIP_CB_PRE_EXTRACT], $allowed_callbacks)) {
    throw new Exception('Invalid callback function');
}
```

3. **Consider using PHP's ZipArchive instead:**

```php
// Modern PHP alternative
$zip = new ZipArchive();
$zip->open('archive.zip');
$zip->extractTo('/path/to/extract/');
$zip->close();
```

### Long-term Solutions

1. **Upgrade PclZip:** Check if newer versions have addressed this issue
2. **Code Audit:** Review all code paths that use PclZip to ensure no user-controlled callbacks
3. **Security Wrapper:** Create a secure wrapper class that prevents callback injection

---

## REFERENCES

- PclZip Library: https://www.phpconcept.net/pclzip
- PHP eval() Security: https://www.php.net/manual/en/function.eval.php
- CWE-95: Improper Neutralization of Directives in Dynamically Evaluated Code
- CWE-78: Improper Neutralization of Special Elements used in an OS Command

---

## CONCLUSION

The `eval()` vulnerability in `pclzip.lib.php` is a **design flaw** in the library itself. While the current Gambio implementation does not expose this vulnerability to direct HTTP exploitation, it represents a significant security risk:

1. **Latent Vulnerability:** Could be triggered by future code changes or third-party modules
2. **Supply Chain Risk:** Malicious update packages could exploit this
3. **Chained Exploitation:** Combined with file write vulnerabilities, could lead to RCE

**Recommendation:** Replace `eval()` with `call_user_func_array()` in all affected files as a preventive measure.

---

*This report is for authorized security testing purposes only.*
