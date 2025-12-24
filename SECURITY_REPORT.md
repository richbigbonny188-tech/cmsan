## Gambio GX HTTP Security Audit

### Phase 1 — HTTP Entrypoint Mapping
- `/shop.php` → `HttpService::handle` (public storefront router). Methods: GET/POST. Parameters: routed query/body parameters consumed by controllers. Authentication: none for storefront pages.
- `/login.php` → `LoginContentControl` in `system/classes/accounts/LoginContentControl.inc.php`. Methods: GET/POST. Parameters: `action` (GET), `email_address`, `password`, optional `return_url`, `return_url_hash`, `checkout_started` (POST/GET). Authentication: none (customer login form).
- `/gambio_installer/index.php` → `gm_delete_installer` in `gambio_installer/includes/application.php`. Methods: GET. Parameters: `delete_installer`, `auth_token`, `return_url`. Authentication: guarded by installer security token.
- `/gambio_installer/request_port.php` → switch block handling `action=test_db_connection`. Methods: POST. Parameters: `DB_SERVER`, `DB_SERVER_USERNAME`, `DB_SERVER_PASSWORD`, `DB_DATABASE`. Authentication: none (installer precheck).

### Phase 2 — Data Flow Trace
- [ENTRYPOINT] `/login.php?action=process`  
  [SOURCE] `login.php` lines 44–47 pass `$_GET`/`$_POST` into `LoginContentControl`.  
  [TRANSFORM] `LoginContentControl::proceed` (system/classes/accounts/LoginContentControl.inc.php lines 48–58) trims `email_address`, wraps it in `NonEmptyStringType`, and runs `xtc_db_prepare_input` on `password`; query parameters for user lookup are escaped with `xtc_db_input` (lines 64–71). Optional `return_url` is accepted only when its `return_url_hash` equals `hash('sha256', return_url . LogControl::get_secure_token())` (lines 94–99).  
  [SINK] Credentials checked via `AuthService::authUser`, then SQL SELECT on `customers` table (lines 64–71).  
  [USER CONTROL PRESERVED: NO] Input is normalized and SQL-escaped; redirects require a strong HMAC-style hash before use.

- [ENTRYPOINT] `/gambio_installer/index.php?delete_installer=1&auth_token=...`  
  [SOURCE] `gambio_installer/index.php` lines 30–33 forward GET params to `gm_delete_installer`.  
  [TRANSFORM] `gm_delete_installer` in `gambio_installer/includes/application.php` verifies the supplied token against `APP_SECURITY_TOKEN` (lines 253–264) before proceeding.  
  [SINK] On success, recursively deletes the `gambio_installer` directory (lines 267–275) and redirects (line 277).  
  [USER CONTROL PRESERVED: NO] Execution stops unless the server-side token matches exactly.

- [ENTRYPOINT] `/gambio_installer/request_port.php` with `action=test_db_connection`  
  [SOURCE] POST parameters read in switch case (lines 25–34).  
  [TRANSFORM] Values trimmed/stripslashed; DB name validated against `^[^\\/?%*:|"<>.]{1,64}$` (lines 71–80). Connection attempts wrapped in try/catch, and permission checks are limited to creating/dropping a test table (lines 94–118).  
  [SINK] Temporary `gambio_test_db` table is created/updated/dropped on the provided database connection (lines 94–120).  
  [USER CONTROL PRESERVED: NO] Operations only run with attacker-supplied valid DB credentials; no application data or code paths are reached without authentication to that database.

### Phase 3 — Control Elimination Filter
- `/login.php` parameters lose dangerous influence once escaped via `xtc_db_input` and hashed token verification for redirects; no unchecked data reaches SQL or redirect sinks.
- `/gambio_installer/index.php` rejects requests lacking the correct `APP_SECURITY_TOKEN`, stopping before any file operations.
- `/gambio_installer/request_port.php` accepts input solely to attempt DB connectivity with provided credentials; strict pattern validation for database names and no persistence beyond a temporary test table eliminate attacker control over the application data.

### Phase 4 — Exploitability Analysis
All mapped flows terminate in vetted sinks (escaped SQL, token-checked redirects, or token-gated installer deletion). No path with preserved user control reaches a sensitive sink without strong validation. No SQL injection, file inclusion, command execution, or auth bypass could be reproduced, and no working HTTP PoC exists.

### Phase 5 — Chaining & Impact
Because no exploitable flows remain after control elimination, there is no viable chain for privilege escalation or data compromise.

**Result:** No real, provable HTTP-reachable vulnerabilities were identified in the analyzed entrypoints.
