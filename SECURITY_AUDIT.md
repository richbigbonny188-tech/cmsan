## Security Audit – Gambio CMS (cmsan)

### Phase 1 – Entrypoint Mapping
| Entrypoint | Transport / Method | Handler & File | Parameters | Authentication / Trust |
| --- | --- | --- | --- | --- |
| `/gambio_installer/request_port.php` | HTTP POST (Ajax endpoints used by installer) | Switch statement in `gambio_installer/request_port.php` dispatches to helper scripts such as `includes/create_account.php`, `includes/setup_shop.php`, `includes/write_config.php` based on the `action` POST value | `action` plus action‑specific POST fields (e.g., `FIRST_NAME`, `PASSWORD`, `DB_SERVER`, `DB_DATABASE`, etc.) | **No authentication** or session precondition; configure.php is auto‑loaded when `action` is in {`setup_shop`, `create_account`, `write_config`, `write_robots_file`, `clear_cache`, `get_countries`, `get_states`} via `gambio_installer/includes/application.php` lines 37‑47, 315‑317 |
| `/gambio_installer/index.php` | HTTP GET/POST | Installer UI controller in `gambio_installer/index.php` | Query params `language`, `precheck`, `chmod`, `ftp`, etc. | No authentication; front‑end renders installer pages and AJAX calls go to `request_port.php` |

### Phase 2 – Data Flow Traces
**Entrypoint:** `/gambio_installer/request_port.php` with `action=create_account`  
**Source:** User‑supplied POST fields (`GENDER`, `FIRST_NAME`, `LAST_NAME`, `EMAIL_ADRESS`, `PASSWORD`, `PASSWORD_CONFIRMATION`, etc.) received in `request_port.php` lines 205‑214 and passed into `includes/create_account.php`.  
**Transformations:** `create_account.php` (lines 56‑164) calls `xtc_db_prepare_input`, length checks, and basic email validation; no authentication or privilege checks are performed. Database connection uses credentials from `includes/configure.php` that is auto‑required in `includes/application.php` (lines 37‑47, 315‑317).  
**Sink:** Direct SQL inserts into `customers`, `customers_info`, `address_book`, and `admin_access_users` with `admin_access_role_id = 1` (lines 168‑229), meaning the created customer becomes an administrator. User control over credentials/password is preserved end‑to‑end.  
**User Control Preserved:** Yes – supplied email/password become the new admin account.

**Entrypoint:** `/gambio_installer/request_port.php` with `action=write_config`  
**Source:** User POST fields `DB_SERVER`, `DB_SERVER_USERNAME`, `DB_SERVER_PASSWORD`, `DB_DATABASE`, `DIR_WS_CATALOG`, etc. are read in `includes/write_config.php` lines 25‑52.  
**Transformations:** Values are lightly sanitized with `trim/stripslashes` and `gm_prepare_string`; no authentication or integrity checks. Script checks only filesystem writability (lines 19‑24).  
**Sink:** Attacker‑supplied values are written directly into `includes/configure.php` and `admin/includes/configure.php` (lines 19‑37, 118‑198 and following), redefining database credentials and server path constants used by the live shop.  
**User Control Preserved:** Yes – attacker controls the written configuration values if the files are writable (default permissions in this repo are `-rw-rw-r--`).

### Phase 3 – Control Elimination
No control‑elimination safeguards (authentication, authorization, or strong whitelisting) are present in either flow above. Input is only trimmed/escaped before being persisted.

### Phase 4 – Exploitability (Facts Only)
1) **Unauthenticated Admin Account Creation**  
* **Entrypoint:** `/gambio_installer/request_port.php` (`action=create_account`).  
* **Condition:** Installer directory is accessible (no auth), and `includes/configure.php` is readable (true in default deploy).  
* **Impact:** Remote attacker can create an arbitrary administrator by POSTing the required fields; inserts into `customers` and `admin_access_users` grant admin role `1`, yielding full storefront and backend control (code execution via admin features, data exfiltration, order manipulation).  
* **Evidence:** `request_port.php` lines 205‑218 dispatch to `includes/create_account.php`; `create_account.php` lines 168‑229 perform the inserts with no auth checks.  
* **Proof:** POST to `/gambio_installer/request_port.php` with `action=create_account` and valid form fields; verify new row in `customers`/`admin_access_users` and login with supplied credentials.

2) **Unauthenticated Configuration Overwrite**  
* **Entrypoint:** `/gambio_installer/request_port.php` (`action=write_config`).  
* **Condition:** Installer left on server with writable config files (default 664 in this repo). No authentication required.  
* **Impact:** Attacker overwrites `includes/configure.php` and `admin/includes/configure.php` with attacker‑chosen DB host/user/pass and path constants, enabling database credential theft, shop hijacking, or denial of service by pointing to an attacker‑controlled database.  
* **Evidence:** `write_config.php` lines 19‑52 read POSTed DB and path fields; lines 118‑198+ write them directly into the config PHP files without any auth or integrity check.  
* **Proof:** POST to `/gambio_installer/request_port.php` with `action=write_config` and attacker‑controlled DB parameters; observe modified `includes/configure.php` contents and shop DB connections using the injected credentials.

### Phase 5 – Chaining
Attacks are standalone; no additional chaining required. Example chain:  
`/gambio_installer/request_port.php?action=write_config` → overwrite DB credentials → `/gambio_installer/request_port.php?action=create_account` → create admin in attacker‑controlled DB → complete takeover.

### Conclusion
The exposed installer endpoints allow unauthenticated administrative compromise. No other exploitable vulnerabilities were proven in this audit.
