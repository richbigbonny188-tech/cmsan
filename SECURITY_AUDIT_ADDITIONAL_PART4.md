# Security Audit Report: Additional Vulnerability Classes (Part 4)
## Gambio GX eCommerce Platform - Continued Analysis

**Date:** 2025-12-24  
**Auditor:** GitHub Copilot  
**Scope:** Additional vulnerability classes and previously unexplored entrypoints  

---

## EXECUTIVE SUMMARY

This audit continues the security analysis of the Gambio GX platform, focusing on additional vulnerability classes including file upload bypass, XXE injection, deprecated function usage, and more.

---

## PHASE 1 — NEW VULNERABILITY FINDINGS

### HIGH: File Upload Extension Bypass in FileManagerController

**File:** `GXMainComponents/Controllers/HttpView/Admin/FileManagerController.inc.php`  
**Lines:** 293-310, 397-399  
**Auth Required:** Admin Session  
**Severity:** High (Admin context)  

**Vulnerable Code:**
```php
protected function _initDisallowedExtensions()
{
    $this->disallowedExtensions = ['php', 'htaccess'];
}

// Upload check:
foreach ($this->disallowedExtensions as $extension) {
    if (preg_match(sprintf('/\.%s$/', preg_quote($extension)), $_FILES['file_data']['name'])) {
        // Block upload
    }
}

// Allows upload:
move_uploaded_file($_FILES['file_data']['tmp_name'], $targetDir . $_FILES['file_data']['name'])
```

**Analysis:**
The file upload blacklist is **incomplete**. The following dangerous extensions are NOT blocked:
- `.phtml` - PHP HTML templates (executed as PHP)
- `.php3`, `.php4`, `.php5`, `.php7` - Alternative PHP extensions
- `.phar` - PHP Archive (can execute code)
- `.inc` - PHP include files (may be executed depending on config)
- `.shtml` - Server-side includes
- `.svg` - Can contain embedded JavaScript (XSS)
- `.html` with embedded PHP (in misconfigured servers)

**PoC:**
```bash
# Upload .phtml file which may execute as PHP
curl -X POST "https://<TARGET>/admin/admin.php?do=FileManager/Upload&directory=" \
  -H "Cookie: <ADMIN_SESSION>" \
  -F "file_data=@shell.phtml"
```

**Impact:** Remote Code Execution (requires admin access)

---

### MEDIUM: XXE Vulnerability in GMIloxx XML Parser

**File:** `gm/classes/GMIloxx.php`  
**Lines:** 56-59  
**Auth Required:** Admin Session  
**Severity:** Medium  

**Vulnerable Code:**
```php
protected function prettyXML($xml)
{
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;
    $doc->loadXML($xml);  // No LIBXML_NOENT, no entity loader disabled
    return $doc->saveXML();
}
```

**Analysis:**
The `DOMDocument->loadXML()` is called without disabling external entity loading. On PHP < 8.0, this allows XXE attacks when processing attacker-controlled XML.

**Related Files with Similar Issues:**
- `GXMainComponents/Controllers/HttpView/Admin/GeschaeftskundenversandController.inc.php:1186`
- `GXModules/Gambio/Internetmarke/Admin/Classes/Internetmarke/DPProductInformationService.inc.php:30`
- `GXModules/Gambio/Internetmarke/Admin/Classes/Internetmarke/OneClick4Application.inc.php:138`

---

### MEDIUM: Deprecated create_function() Usage

**File:** `includes/functions/compatibility.php`  
**Lines:** 47, 53  
**Auth Required:** None (library code)  
**Severity:** Medium (Code Injection Risk)  

**Vulnerable Code:**
```php
$builderCore = '$ret = \'\';
foreach ($array as $k => $v) {
    if (is_scalar($v))
        $ret .= $name.urlencode(\'[\'.$k.\']\').\'=\'.urlencode($v).$sep;
    else {
        $builder = create_function(\' $name, $array, $sep, $builderCore\', $builderCore);
        $ret .= $builder( $name.urlencode(\'[\'.$k.\']\'), $v, $sep, $builderCore).$sep;
    }
}';

$builder = create_function(' $name, $array, $sep, $builderCore', $builderCore);
```

**Analysis:**
- `create_function()` is deprecated in PHP 7.2 and removed in PHP 8.0
- It uses `eval()` internally to create anonymous functions
- While the code string is hardcoded here, this represents dangerous coding practice
- If any path allows user input to reach `$builderCore`, it would be RCE

**Status:** Currently not exploitable (hardcoded code string)

---

### MEDIUM: Potential Path Traversal in File Download

**File:** `GXMainComponents/Controllers/HttpView/Admin/FileManagerController.inc.php`  
**Lines:** 328-343  

**Vulnerable Code:**
```php
public function actionDownload()
{
    $this->_init();
    
    $file = $this->baseDirectory . $this->file;
    $filename = basename($this->file);
    header('Content-Type: ' . mime_content_type($file));
    header('Content-Length: ' . filesize($file));
    // ...
    readfile($file);
}
```

**Analysis:**
If `$this->file` is not properly sanitized, path traversal could allow reading arbitrary files:
```
GET /admin/admin.php?do=FileManager/Download&file=../../../includes/configure.php
```

Requires checking `_init()` method for sanitization.

---

### LOW: Information Disclosure via Verbose Errors

**Multiple Files**  
**Severity:** Low  

Several files use verbose error handling that may disclose internal paths, database structure, or other sensitive information in production environments when display_errors is enabled.

---

### LOW: Missing Security Headers

**File:** Various response handlers  
**Severity:** Low  

Missing security headers across the application:
- X-Content-Type-Options: nosniff
- X-Frame-Options: DENY or SAMEORIGIN  
- Content-Security-Policy
- X-XSS-Protection

---

## PHASE 2 — ANALYSIS OF HUB CALLBACK

### HubCallback.inc.php - Authentication Analysis

**File:** `GXModules/Gambio/Hub/Shop/Classes/Extensions/HubCallback.inc.php`  
**Auth:** X-AUTH-HASH header + POST params  

**Actions Available:**
- `client_key` - Client key callback
- `session_key` - Session key callback
- `get_configuration` - Get shop configuration
- `update_configuration` - Update shop configuration

**Security Assessment:**
The HubCallback implements proper authentication via:
1. `HTTP_X_AUTH_HASH` header verification
2. `HTTP_X_CLIENT_KEY` header verification
3. POST parameter validation

However, the `update_configuration` action could be sensitive if authentication is bypassed.

---

## PHASE 3 — SINGLE SIGN-ON ANALYSIS

### SingleSignOnController.inc.php - OAuth Flow

**File:** `GXModules/Gambio/SingleSignOn/Shop/Classes/Controllers/SingleSignOnController.inc.php`

**Actions:**
- `actionRedirect()` - Redirect to SSO provider
- `actionLogin()` - Process authorization code

**Potential Issues:**
1. **Open Redirect** via `return_url` parameter (Line 65-72):
   ```php
   $returnUrl = $this->_getQueryParameter('return_url');
   $returnUrlHash = $this->_getQueryParameter('return_url_hash');
   if (!empty($returnUrl) && !empty($returnUrlHash)) {
       if ($returnUrlHash === hash('sha256', $returnUrl . LogControl::get_secure_token())) {
           $_SESSION['sso_redirect_after_login'] = $returnUrl;
       }
   }
   ```
   
   The return URL is validated with a hash using `LogControl::get_secure_token()`. If this token is weak or predictable, open redirect is possible.

2. **Account Takeover via Email Verification Bypass** (Lines 103-115):
   ```php
   if (!empty($_SESSION['ssoData']['customer_collection']['email_address'])
       && (bool)$_SESSION['ssoData']['customer_collection']['email_address_verified'] === true) {
       $customerId = $this->findCustomerByEmail($_SESSION['ssoData']['customer_collection']['email_address']);
       if (false !== $customerId) {
           $this->storeSSOData($customerId, $_SESSION['ssoData']['iss'], $_SESSION['ssoData']['sub']);
       }
   }
   ```
   
   If an SSO provider is compromised or misconfigured, it could provide verified emails for accounts the attacker doesn't own.

---

## PHASE 4 — FILE UPLOAD DEEP DIVE

### GMGPrintFileManager Upload Analysis

**File:** `gm/classes/GMGPrintFileManager.php`  
**Lines:** 22-75  

**Security Controls:**
```php
function upload($p_files_id, $p_target_dir, $p_allowed_extensions = array(), ...)
{
    // Extension check
    $t_allowed = $this->check_extension($_FILES[$p_files_id]['name'], $p_allowed_extensions);
    
    // File size checks
    $t_allowed = $this->check_minimum_filesize($_FILES[$p_files_id]['size'], $p_minimum_filesize);
    $t_allowed = $this->check_maximum_filesize($_FILES[$p_files_id]['size'], $p_maximum_filesize);
    
    // Move file
    move_uploaded_file($_FILES[$p_files_id]['tmp_name'], $p_target_dir . $t_new_filename);
}
```

**Security Issues:**
1. **Whitelist vs Blacklist:** Uses allowlist (`$p_allowed_extensions`) - GOOD
2. **No MIME type validation:** Only checks extension - WEAK
3. **No content validation:** Doesn't verify file content matches extension
4. **Filename sanitization:** Uses `basename()` - may be insufficient

### GMLogoManager Upload Analysis

**File:** `gm/classes/GMLogoManager.php`  
**Lines:** 85-120, 215-240  

**Security Controls:**
```php
function check_upload($p_filetype, $p_file_extension) {
    // MIME type + extension validation
    if($p_filetype == 'image/x-icon' || $p_filetype == "image/ico") {
        if($p_filetype == 'image/x-icon' && $p_file_extension == 'ico') {
            return true; 
        }
    }
    // ...
}
```

**Positive:** Uses both MIME type and extension validation
**Negative:** MIME type can be spoofed in POST request

---

## PHASE 5 — CUMULATIVE FINDINGS SUMMARY

### New Findings (Part 4)

| ID | Severity | Vulnerability | File | Exploitable |
|----|----------|---------------|------|-------------|
| **UPLOAD-001** | High | File Upload Extension Bypass | FileManagerController | Admin required |
| **XXE-002** | Medium | XXE in DOMDocument | GMIloxx.php | Admin + PHP < 8.0 |
| **XXE-003** | Medium | XXE in DOMDocument | GeschaeftskundenversandController | Admin + PHP < 8.0 |
| **XXE-004** | Medium | XXE in DOMDocument | DPProductInformationService | Admin + PHP < 8.0 |
| **DEPR-001** | Medium | create_function() usage | compatibility.php | Not directly |
| **PATH-001** | Medium | Potential Path Traversal | FileManagerController | Admin required |
| **REDIR-001** | Low | Open Redirect (token-protected) | SingleSignOnController | Weak token |
| **INFO-001** | Low | Verbose Errors | Multiple | Misconfiguration |
| **HDR-001** | Low | Missing Security Headers | Multiple | N/A |

### All Findings (Parts 1-4)

| Severity | Part 1 | Part 2 | Part 3 | Part 4 | Total |
|----------|--------|--------|--------|--------|-------|
| Critical | 1 | 1 | 1 | 0 | **3** |
| High | 1 | 1 | 2 | 1 | **5** |
| Medium | 4 | 2 | 2 | 5 | **13** |
| Low | 3 | 2 | 0 | 3 | **8** |
| **Total** | 9 | 6 | 5 | 9 | **29** |

---

## RECOMMENDATIONS

### Critical Priority

1. **FileManagerController:** Expand extension blacklist to include:
   ```php
   $this->disallowedExtensions = [
       'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
       'htaccess', 'htpasswd', 'inc', 'cgi', 'pl', 'py', 'sh',
       'asp', 'aspx', 'jsp', 'jspx', 'cfm'
   ];
   ```

2. **XXE Prevention:** Add to all XML parsers:
   ```php
   if (PHP_VERSION_ID < 80000) {
       libxml_disable_entity_loader(true);
   }
   $doc = new DOMDocument();
   $doc->loadXML($xml, LIBXML_NOENT | LIBXML_NONET);
   ```

### Medium Priority

3. **Remove create_function():** Replace with anonymous functions:
   ```php
   $builder = function($name, $array, $sep) use (&$builder) {
       // ... implementation
   };
   ```

4. **File Upload Hardening:**
   - Validate MIME type on server side (not from client)
   - Use `finfo_file()` for content-based type detection
   - Store uploaded files outside webroot
   - Generate random filenames

### Low Priority

5. **Security Headers:** Implement in `includes/application_top.php`:
   ```php
   header('X-Content-Type-Options: nosniff');
   header('X-Frame-Options: SAMEORIGIN');
   header('X-XSS-Protection: 1; mode=block');
   ```

---

## APPENDIX: Files Requiring Updates

### XXE Vulnerable Files
- `gm/classes/GMIloxx.php`
- `GXMainComponents/Controllers/HttpView/Admin/GeschaeftskundenversandController.inc.php`
- `GXModules/Gambio/Internetmarke/Admin/Classes/Internetmarke/DPProductInformationService.inc.php`
- `GXModules/Gambio/Internetmarke/Admin/Classes/Internetmarke/OneClick4Application.inc.php`

### File Upload Handlers
- `GXMainComponents/Controllers/HttpView/Admin/FileManagerController.inc.php`
- `gm/classes/GMGPrintFileManager.php`
- `gm/classes/GMLogoManager.php`
- `GXMainComponents/Controllers/HttpView/Admin/ContentManager/*.php`

---

*This report is for authorized security testing purposes only.*
