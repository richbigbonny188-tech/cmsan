# Security Audit Part 5: Additional Vulnerability Classes

## Executive Summary

This report identifies **NEW** vulnerability classes not covered in previous audits, focusing on:
- Weak cryptography and random number generation
- IP spoofing vulnerabilities
- SSL/TLS verification bypasses
- Information disclosure
- Token predictability

---

## Critical & High Severity Findings

### CRYPT-001: Weak Random Password Generation (High)

**File:** `inc/xtc_create_password.inc.php`  
**Lines:** 16-30  
**Severity:** High  
**CVSS:** 7.5 (High)

**Vulnerable Code:**
```php
srand( (double) microtime()*1000000);  // Predictable seed!

for($i=0;$i<$length;$i++)
{
    $rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
}

function xtc_create_password($length) {
    $pass=xtc_RandomString($length);
    return md5($pass);  // MD5 is cryptographically broken!
}
```

**Issue:**
1. Uses deprecated `srand()` with predictable seed based on `microtime()`
2. Uses `rand()` instead of cryptographically secure random
3. Returns MD5 hash (broken, fast to brute force)

**Impact:**
- Password reset tokens are predictable
- Admin account takeover via predicted tokens
- Brute force attacks on MD5 hashes

**Exploitation:**
```python
import hashlib
import time

# Predict microtime-based seed
for seed in range(1000000):
    # Reconstruct random sequence
    chars = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ1234567890'
    # ... predict password
```

**Remediation:**
```php
function xtc_create_password($length) {
    return bin2hex(random_bytes($length / 2));  // Use CSPRNG
}
```

---

### CRYPT-002: Weak Page Token Generation (High)

**File:** `system/classes/security/PageToken.inc.php`  
**Lines:** 33-35  
**Severity:** High  
**CVSS:** 7.0 (High)

**Vulnerable Code:**
```php
public function generate_token()
{
    $t_key = md5(time() . rand() . LogControl::get_secure_token());
    // ...
}
```

**Issue:**
1. `time()` is predictable (attacker knows approximate server time)
2. `rand()` is not cryptographically secure
3. Even with LogControl::get_secure_token(), two weak components reduce entropy
4. MD5 is fast to compute = easy brute force

**Impact:**
- CSRF protection bypass
- Account takeover
- Administrative actions without authorization

**Attack Complexity:** Medium (requires knowing approximate server time)

**Remediation:**
```php
$t_key = bin2hex(random_bytes(16));  // 32 hex chars, cryptographically secure
```

---

### CRYPT-003: Predictable Security Token (High)

**File:** `GProtector/classes/GProtector.inc.php`  
**Lines:** 451  
**Severity:** High  
**CVSS:** 6.5 (Medium-High)

**Vulnerable Code:**
```php
$token = md5(time() . rand());
$tokenFile = GAMBIO_PROTECTOR_TOKEN_DIR . $this->getTokenPrefix() . $token;
```

**Issue:**
- Security token protecting the GProtector system is predictable
- Can be brute-forced by knowing approximate time of token creation

**Impact:**
- Bypass GProtector security filters
- Disable security protections

---

### IP-001: IP Address Spoofing (Medium-High)

**File:** `inc/xtc_get_ip_address.inc.php`  
**Lines:** 19-32  
**Severity:** Medium-High  
**CVSS:** 6.0

**Vulnerable Code:**
```php
function xtc_get_ip_address() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  // SPOOFABLE!
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];        // SPOOFABLE!
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
```

**Issue:**
- Trusts `X-Forwarded-For` and `X-Client-IP` headers without validation
- These headers are user-controlled and easily spoofed

**Impact:**
- Bypass IP-based rate limiting
- Bypass IP-based access controls
- Log poisoning/forensics evasion
- Bypass geo-blocking

**Exploitation:**
```bash
# Spoof IP to bypass rate limiting
curl -H "X-Forwarded-For: 1.2.3.4" https://target/login.php

# Bypass IP whitelist
curl -H "X-Forwarded-For: 192.168.1.1" https://target/admin/
```

**Remediation:**
```php
function xtc_get_ip_address() {
    // Only trust proxy headers if configured trusted proxies
    if (defined('TRUSTED_PROXIES') && in_array($_SERVER['REMOTE_ADDR'], TRUSTED_PROXIES)) {
        // Parse X-Forwarded-For correctly
        $xff = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim(end($xff));  // Get rightmost (client-provided) IP
    }
    return $_SERVER['REMOTE_ADDR'];
}
```

---

## Medium Severity Findings

### SSL-001: SSL Verification Disabled (Multiple Files)

**Affected Files:**
| File | Line | Context |
|------|------|---------|
| `magnaCallback.php` | 189, 342 | Magnalister callbacks |
| `gm/classes/lib/nusoap.php` | 2480 | SOAP client |
| `callback/sofort/library/sofortLib_http.inc.php` | 121-122 | Sofort payment |
| `system/classes/external/protected_shops/ProtectedShops.inc.php` | 146 | Protected Shops API |

**Vulnerable Pattern:**
```php
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
```

**Impact:**
- Man-in-the-Middle attacks on payment callbacks
- Credential interception
- Data manipulation in transit

**CVSS:** 5.9 (Medium)

---

### INFO-001: phpinfo() Exposure

**File:** `magnalister_compatibility_check.php`  
**Lines:** 142-149  
**Severity:** Medium  
**CVSS:** 5.3

**Vulnerable Code:**
```php
phpinfo(-1);
```

**Impact:**
- Full server configuration disclosure
- PHP version, modules, paths
- Helps attackers find other vulnerabilities

---

### SESS-001: Session Cookie Without Secure/HttpOnly Flags

**File:** `GXModules/Gambio/SingleSignOn/Shop/Lib/AmazonSingleSignonService.inc.php`  
**Lines:** 101-102

**Vulnerable Code:**
```php
setcookie('amazon_Login_accessToken', $tokensData['access_token'], 0, '/', '', true);
```

**Issue:** Missing `httponly` flag allows JavaScript access to token

---

## Low Severity Findings

### WEAK-001: Weak CSRF Token Storage

**File:** `system/classes/security/PageToken.inc.php`

Tokens stored in array, limited to 200 entries. Under heavy load, older tokens expire too quickly.

### WEAK-002: MD5 Used for Security Functions

Multiple files use MD5 for security-critical operations:
- Password hashing
- Token generation
- Session validation

**Files Affected:**
- `inc/xtc_create_password.inc.php`
- `GProtector/classes/GProtector.inc.php`
- `gm/classes/GMCounter.php`
- `system/classes/security/PageToken.inc.php`

---

## Exploitation Chains (NEW)

### CHAIN-A: IP Spoof → Rate Limit Bypass → Password Brute Force

```
1. Spoof IP via X-Forwarded-For header
2. Bypass rate limiting on login attempts
3. Brute force weak MD5-based password hashes
4. Full account takeover
```

**PoC:**
```bash
for i in {1..10000}; do
    curl -H "X-Forwarded-For: 192.168.1.$i" \
         -d "email=admin@target.com&password=password$i" \
         https://target/login.php
done
```

### CHAIN-B: Token Prediction → CSRF Bypass → Admin Actions

```
1. Observe server response times (estimate time())
2. Predict page token using time + rand() brute force
3. Submit forged requests with predicted token
4. Execute admin actions without authorization
```

---

## Summary Table

| ID | Vulnerability | Severity | CVSS | Auth Required | Exploitable |
|----|---------------|----------|------|---------------|-------------|
| CRYPT-001 | Weak Password RNG | High | 7.5 | No | Yes |
| CRYPT-002 | Weak Page Token | High | 7.0 | No | Yes |
| CRYPT-003 | Predictable GProtector Token | High | 6.5 | No | Medium |
| IP-001 | IP Spoofing | Medium-High | 6.0 | No | **Yes** |
| SSL-001 | SSL Verify Disabled | Medium | 5.9 | N/A | MITM |
| INFO-001 | phpinfo() Disclosure | Medium | 5.3 | No | **Yes** |
| SESS-001 | Cookie Flags Missing | Medium | 4.3 | No | XSS needed |
| WEAK-001 | Weak CSRF Storage | Low | 3.7 | No | Heavy load |
| WEAK-002 | MD5 Usage | Low | - | - | Depends |

---

## Cumulative Vulnerability Count (All Parts)

| Severity | Part 1 | Part 2 | Part 3 | Part 4 | Part 5 | **Total** |
|----------|--------|--------|--------|--------|--------|-----------|
| Critical | 1 | 1 | 1 | 0 | 0 | **3** |
| High | 1 | 1 | 2 | 1 | 3 | **8** |
| Medium | 4 | 2 | 2 | 5 | 4 | **17** |
| Low | 3 | 2 | 0 | 3 | 2 | **10** |
| **Total** | 9 | 6 | 5 | 9 | **9** | **38** |

---

## Recommendations

### Immediate Actions (Critical)

1. **Replace all random number generation:**
   ```php
   // Instead of rand() or mt_rand()
   $token = bin2hex(random_bytes(16));
   ```

2. **Replace MD5 with secure hashing:**
   ```php
   // For passwords
   $hash = password_hash($password, PASSWORD_ARGON2ID);
   
   // For tokens
   $token = hash('sha256', random_bytes(32));
   ```

3. **Fix IP address validation:**
   ```php
   // Only use REMOTE_ADDR or validate trusted proxy list
   ```

4. **Enable SSL verification:**
   ```php
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
   ```

### Long-term Improvements

1. Security code review for all cryptographic functions
2. Implement Content Security Policy
3. Add secure cookie flags globally
4. Remove phpinfo() from production code

---

*Report generated as part of authorized security audit*
