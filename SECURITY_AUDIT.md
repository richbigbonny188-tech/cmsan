# Security Audit Report

## Scope & Method
- White-box review of HTTP-accessible entrypoints in the extracted application.
- No speculative findings; only flows with demonstrable reachability were considered.
- Code references use repository paths and line numbers from the current tree.

## Phase 1 — Entrypoint Mapping
| Entrypoint | File | Transport / Method | Parameters | Auth / Trust |
| --- | --- | --- | --- | --- |
| Product download | `download.php` | HTTP GET | `id`, `order` | Requires logged-in customer session (`$_SESSION['customer_id']`). |
| GA tracking proxy | `ec_proxy.php` | HTTP GET | `prx` (GA path), plus derived query params | No session; intended for analytics beaconing. |
| Installer cleanup | `gambio_installer/index.php` | HTTP GET | `delete_installer`, `auth_token`, `return_url` | Protected by environment token `APP_SECURITY_TOKEN`. |
| Updater UI | `gambio_updater/index.php` | HTTP GET/POST | `content`, `language`, `email`, `password`, optional flags | Actions gated by `GambioUpdateControl::login`. |
| mPDF temp fetch | `vendor/mpdf/mpdf/data/out.php` | HTTP GET/POST | `filename`, `opname`, `dest` | Serves files only from `../tmp/`; expects prior internal generation. |

## Phase 2 — Data-Flow Traces
### `download.php`
- **Source:** `$_GET['id']`, `$_GET['order']` (lines 25-26).
- **Transformations:** Cast to int via `DownloadProcess::set_validation_rules` (`system/classes/downloads/DownloadProcess.inc.php:39-44`). Used in SQL with equality matches to the logged-in `customer_id` and download id (lines 60-79). Filename read from DB and checked for `../` and filesystem existence (lines 119-123).
- **Sink:** File read/stream (lines 179-220). User control over path eliminated by traversal check and DB binding.

### `ec_proxy.php`
- **Source:** `$_GET['prx']` (line 38).
- **Transformations:** `parse_url`, rebuilt query merged into `$query`, host fixed to `https://www.google-analytics.com` (lines 38-48). Adds client IP/UA (lines 50-56).
- **Sink:** `curl_exec` to GA endpoint and binary echo (lines 59-66). User input cannot change destination host or local resources.

### `gambio_installer/index.php`
- **Source:** `$_GET['delete_installer']`, `$_GET['auth_token']`, `$_GET['return_url']` (lines 28-33).
- **Transformations:** Forwarded to `gm_delete_installer` (`gambio_installer/includes/application.php:246-279`). Compares provided token to `APP_SECURITY_TOKEN` (lines 253-265); if missing or mismatched, aborts with HTTP redirect.
- **Sink:** Recursive delete of installer directory on token match (lines 267-275). Unauthorized callers cannot reach sink without valid token.

### `gambio_updater/index.php`
- **Source:** `$_GET['content']`, `$_GET['language']`, `$_POST['email']`, `$_POST['password']` (lines 47-57, 205-246).
- **Transformations:** `content` defaults to `language` page; login-required actions enumerated (lines 205-215). Credentials checked via `$coo_update_control->login(...)` (lines 217-239); on failure, view forced to login page.
- **Sink:** Database migrations/config shown only after successful login; unauthenticated input does not reach DB/file sinks.

### `vendor/mpdf/mpdf/data/out.php`
- **Source:** `$_REQUEST['filename']`, `$_REQUEST['opname']`, `$_REQUEST['dest']` (lines 5, 11-12).
- **Transformations:** Rejects names containing `/` or `\\` (lines 7-9); prepends fixed `../tmp/` path (lines 3-5). Requires existing file to proceed (line 14).
- **Sink:** Sends PDF via headers and `fpassthru` then deletes file (lines 31-66). User cannot escape the temp directory due to path checks.

## Phase 3 — Control Elimination
- `download.php`: Control constrained by integer casting and strict DB joins to current `customer_id`; filename validated against traversal (`../`) before file read.
- `ec_proxy.php`: Destination host hard-coded; only query string components remain user-influenced, affecting only outbound GA request.
- `gambio_installer/index.php`: Token comparison (`APP_SECURITY_TOKEN`) is a hard stop for unauthenticated requests.
- `gambio_updater/index.php`: Login gate prevents unauthenticated parameters from reaching update actions or DB changes.
- `vendor/mpdf/mpdf/data/out.php`: Directory traversal blocked; requires server-created temp file, preventing arbitrary file reads.

## Phase 4 — Exploitability
All traced flows above terminate with effective control elimination. No path preserved attacker control to a sensitive sink (file inclusion, command execution, arbitrary DB write/read, or privilege escalation) without prior authorization or fixed host constraints. No exploitable vulnerabilities were proven.

## Phase 5 — Chaining
No exploitable primitives identified; no chains possible.

## Conclusion
No exploitable vulnerabilities were proven across the reviewed entrypoints. If additional externally reachable code is exposed beyond the mapped files, it should be reviewed under the same tracing approach.
