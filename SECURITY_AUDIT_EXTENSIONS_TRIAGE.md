# Security Audit - STEP 3: Triage (TOP-10 Selection) for GXMainComponents/Extensions

**Target**: Gambio GX - GXMainComponents/Extensions  
**Date**: 2025-12-26  
**Audit Type**: Authorized white-box security assessment  
**Phase**: STEP 3 - TRIAGE

---

## Selection Criteria for TOP-10 Entrypoints

Based on STEP 2 Entrypoint Mapping, selecting entrypoints for deep trace analysis using these criteria:

1. **Risk Level**: CRITICAL > HIGH > MEDIUM
2. **Attack Surface**: Public > Customer > Admin (but all are admin, so consider authentication bypass potential)
3. **Sink Severity**: File operations > SQL > External connections > Response manipulation
4. **Input Complexity**: Multiple params > Complex objects > Simple params
5. **Legacy Integration**: Modern → Legacy sinks (higher risk of control gaps)
6. **Observable Impact**: Data exfiltration > System compromise > DoS > Information disclosure

---

## TOP-10 Entrypoints Selected for Deep Analysis

### #1: AutoUpdaterAjaxController::actionUninstallTheme()
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterAjaxController.inc.php:209-245`

**Selection Rationale**: 
- **Risk**: CRITICAL - Direct filesystem operations with user input
- **Sink**: File/directory deletion with recursive traversal
- **Input**: POST parameter `themeName` concatenated to filesystem path
- **Control Gap**: Line 221 `is_dir(DIR_FS_CATALOG . 'themes/' . $themeName)` - path concatenation before validation
- **Impact**: Arbitrary file deletion if path traversal successful
- **Auth**: Admin-only but filesystem operations critical
- **Priority**: Path traversal + file deletion = highest severity

**Key Code Locations**:
- Input: Line 211 `$themeName = $this->_getPostData('themeName');`
- Validation: Lines 213-219 (empty check only, no sanitization)
- Sink: Line 221 `is_dir(DIR_FS_CATALOG . 'themes/' . $themeName)`
- Sink: Line 242 `$this->deleteDirectory(DIR_FS_CATALOG . 'themes/' . $themeName)`
- Recursive deletion: Lines 255-289 `deleteDirectory()` method

---

### #2: AutoUpdaterAjaxController::actionCheckPermission()
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterAjaxController.inc.php:77-107`

**Selection Rationale**:
- **Risk**: CRITICAL - External data deserialization without validation
- **Sink**: JSON deserialization → Object creation → File list processing
- **Input**: POST parameter `gambioStoreData` from external Gambio Store
- **Control Gap**: Line 82 manual string replacement before decode, no schema validation
- **Impact**: Malicious JSON could manipulate file lists for permission checks
- **Auth**: Admin-only but processes external untrusted data
- **Priority**: Deserialization from external source = high attack vector

**Key Code Locations**:
- Input: Line 81 `$gambioStoreData = $this->_getPostData('gambioStoreData');`
- Transform: Line 82 `$gambioStoreData = str_replace('\"', '"', $gambioStoreData);`
- Deserialize: Line 83 `$gambioStoreData = json_decode($gambioStoreData, true);`
- Object creation: Line 84 `$gambioStorePackage = AutoUpdaterUpdate::createByGambioStoreData($gambioStoreData);`
- File list loop: Lines 87-89 building file paths from deserialized data
- Sink: Line 94 `$this->autoUpdater->checkFilesPermissionsWithFileList($fileList)`

---

### #3: DataTableHelper::getOrderByClause()
**Location**: `/GXMainComponents/Extensions/Helpers/DataTableHelper/DataTableHelper.inc.php:41-73`

**Selection Rationale**:
- **Risk**: MEDIUM-HIGH - SQL ORDER BY construction from REQUEST
- **Sink**: ORDER BY clause in SQL queries via QuickEdit controllers
- **Input**: `$_REQUEST['order']` and `$_REQUEST['columns']` arrays
- **Control Gap**: Field from column definition, but column lookup by user-controlled index
- **Impact**: SQL injection if column definitions manipulable or ORDER BY not parameterized
- **Auth**: Admin-only via QuickEditOverviewAjaxController
- **Priority**: Classic SQL injection vector in sorting functionality

**Key Code Locations**:
- Input: Line 43 `$orderBy = $_REQUEST['order'];`
- Column lookup: Line 53 `$column = $_REQUEST['columns'][$order['column']];`
- Field extraction: Line 59 `$field = $columns->findByName(new StringType($column['name']))->getField();`
- Direction: Line 52 `$direction = strtoupper($order['dir']);`
- Clause construction: Line 68 `$orderByClause[] = $section . ' ' . $direction;`
- Return: Line 72 `return implode(', ', $orderByClause);`

**Usage Context**: Called from QuickEditOverviewAjaxController::actionDataTable() (line 140)

---

### #4: AutoUpdaterAjaxController::actionCheckFtpConnection()
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterAjaxController.inc.php:169-201`

**Selection Rationale**:
- **Risk**: CRITICAL - SSRF via FTP connection to user-controlled server
- **Sink**: FTP connection attempt to arbitrary server
- **Input**: Multiple POST parameters (protocol, server, login, password, port, passive)
- **Control Gap**: Empty checks only, no hostname/IP validation
- **Impact**: SSRF to internal services, credential stuffing, network reconnaissance
- **Auth**: Admin-only but network operations critical
- **Priority**: SSRF + credential handling = serious attack vector

**Key Code Locations**:
- Input: Lines 171-176 (6 FTP parameters from POST)
- Validation: Lines 178-184 (empty checks only on protocol, server, login)
- Sink: Line 188 `$this->autoUpdaterFactory->createFtpManager($protocol, $server, $login, $password, $port, $passive);`

---

### #5: StyleEdit4AuthenticationController (redirect flow)
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/StyleEdit4AuthenticationController.inc.php:54-158`

**Selection Rationale**:
- **Risk**: MEDIUM - Open redirect via GET parameter in URL
- **Sink**: Header Location redirect with user-controlled parameter
- **Input**: GET parameter `startPageUrl` added to redirect URL
- **Control Gap**: No URL validation, parameter directly assigned to urlParameters array
- **Impact**: Phishing via open redirect, potential header injection
- **Auth**: Admin-only but open redirect still exploitable
- **Priority**: Classic open redirect vulnerability

**Key Code Locations**:
- Input: Lines 58-60 `$this->urlParameters['startPageUrl'] = $_GET['startPageUrl'];`
- URL building: Lines 144-155 `get_href_link()` with `http_build_query($this->urlParameters)`
- Sink: Line 157 `header('Location: ' . $redirectUrl);`

---

### #6: AutoUpdaterAjaxController::actionDeleteBackup()
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterAjaxController.inc.php:144-161`

**Selection Rationale**:
- **Risk**: HIGH - Backup file deletion with user-controlled ID
- **Sink**: File/directory deletion via backup mechanism
- **Input**: POST parameter `backupId`
- **Control Gap**: Only null check, no validation of ID format or path
- **Impact**: Arbitrary backup deletion, potential path traversal if ID not validated
- **Auth**: Admin-only
- **Priority**: File deletion operations always critical

**Key Code Locations**:
- Input: Line 146 `$backupId = $this->_getPostData('backupId');`
- Validation: Lines 147-153 (null check only)
- Sink: Line 155 `$this->autoUpdater->deleteBackup($backupId);`

---

### #7: QuickEditOverviewAjaxController::actionUpdate()
**Location**: `/GXMainComponents/Controllers/HttpView/AdminAjax/QuickEditOverviewAjaxController.inc.php:246-270`

**Selection Rationale**:
- **Risk**: MEDIUM - Mass product data updates via REQUEST array
- **Sink**: Database updates for multiple products
- **Input**: `$_REQUEST['data']` array with product IDs and changes
- **Control Gap**: Loop over user-provided data structure
- **Impact**: Mass data manipulation, potential SQL injection in update logic
- **Auth**: Admin-only
- **Priority**: Mass update operations with complex input structure

**Key Code Locations**:
- Input: Line 250 `foreach ($_REQUEST['data'] as $productId => $changes)`
- Processing: Product update logic (implementation needs trace)

---

### #8: DataTableHelper::getFilterParameters()
**Location**: `/GXMainComponents/Extensions/Helpers/DataTableHelper/DataTableHelper.inc.php:87-118`

**Selection Rationale**:
- **Risk**: MEDIUM - SQL WHERE clause parameters from REQUEST
- **Sink**: Filter parameters for SQL queries
- **Input**: `$_REQUEST['columns']` with search values
- **Control Gap**: Type-based filtering but values passed through with minimal sanitization
- **Impact**: SQL injection via WHERE clause if not properly parameterized downstream
- **Auth**: Admin-only via QuickEdit controllers
- **Priority**: Complements ORDER BY injection risk

**Key Code Locations**:
- Input: Line 91 `foreach ($_REQUEST['columns'] as $index => $column)`
- Value extraction: Line 93 `$columnValue = $column['search']['value'] ?? null;`
- Regex filtering: Line 106 `preg_match('/^([\d\.]+)\s*-\s*([\d\.]+)$/', ...)`
- Return: Line 117 `return array_map([$this, '_trimArray'], $filterParameters);`

---

### #9: GambioStoreController::actionConfiguration()
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/GambioStoreController.inc.php:139-150`

**Selection Rationale**:
- **Risk**: MEDIUM - URL configuration update with basic validation
- **Sink**: Configuration storage via `gm_set_conf()`
- **Input**: POST parameter `url`
- **Control Gap**: FILTER_VALIDATE_URL validation but may allow unintended protocols
- **Impact**: Malicious URL in configuration, potential SSRF in future API calls
- **Auth**: Admin-only
- **Priority**: Configuration tampering with external URL

**Key Code Locations**:
- Input: Line 141 `if (isset($_POST) && isset($_POST['url']))`
- Validation: Line 143 `if (filter_var($_POST['url'], FILTER_VALIDATE_URL) === $_POST['url'])`
- Sink: Line 144 `gm_set_conf('GAMBIO_STORE_URL', $_POST['url']);`

---

### #10: AutoUpdaterAjaxController::actionCheckPermissionForBackupRestore()
**Location**: `/GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterAjaxController.inc.php:115-134`

**Selection Rationale**:
- **Risk**: HIGH - Backup restore permission check with file operations
- **Sink**: File permission checking for backup files
- **Input**: POST parameter `backupId`
- **Control Gap**: Only null check, backup ID used for file operations
- **Impact**: Information disclosure about file permissions, potential path traversal
- **Auth**: Admin-only
- **Priority**: File operations with user-controlled identifier

**Key Code Locations**:
- Input: Line 117 `$backupId = $this->_getPostData('backupId');`
- Validation: Lines 118-126 (null check only)
- Sink: Line 131 `$this->autoUpdater->checkFilesPermissionsForBackup($backupId)`

---

## Ranking Summary

| Rank | Entrypoint | Risk | Vulnerability Type | Impact |
|------|-----------|------|-------------------|--------|
| 1 | AutoUpdater::actionUninstallTheme | CRITICAL | Path Traversal + File Deletion | Arbitrary file deletion |
| 2 | AutoUpdater::actionCheckPermission | CRITICAL | JSON Deserialization | Malicious object creation |
| 3 | DataTableHelper::getOrderByClause | MEDIUM-HIGH | SQL Injection (ORDER BY) | Database manipulation |
| 4 | AutoUpdater::actionCheckFtpConnection | CRITICAL | SSRF + Credential Handling | Network attacks |
| 5 | StyleEdit4::redirect flow | MEDIUM | Open Redirect | Phishing attacks |
| 6 | AutoUpdater::actionDeleteBackup | HIGH | File Deletion | Data loss |
| 7 | QuickEdit::actionUpdate | MEDIUM | Mass Update Manipulation | Data integrity |
| 8 | DataTableHelper::getFilterParameters | MEDIUM | SQL Injection (WHERE) | Database manipulation |
| 9 | GambioStore::actionConfiguration | MEDIUM | Configuration Tampering | Future SSRF |
| 10 | AutoUpdater::actionCheckPermissionForBackupRestore | HIGH | Path Traversal Info Leak | Information disclosure |

---

## Rationale by Risk Category

### CRITICAL Risk (4 selections - ranks #1, #2, #4)
- **AutoUpdater operations**: File deletion, JSON deserialization, FTP SSRF
- **Justification**: These involve filesystem operations, external data processing, and network connections with minimal validation
- **Priority**: Highest impact potential despite admin-only access

### HIGH Risk (2 selections - ranks #6, #10)
- **Backup operations**: Deletion and permission checks
- **Justification**: File operations with user-controlled identifiers, potential for data loss or info disclosure
- **Priority**: Significant but more limited scope than CRITICAL

### MEDIUM-HIGH Risk (1 selection - rank #3)
- **SQL ORDER BY injection**
- **Justification**: Classic SQL injection vector, admin context provides some mitigation
- **Priority**: Well-known vulnerability pattern requiring verification

### MEDIUM Risk (3 selections - ranks #5, #7, #8, #9)
- **Open redirect, mass updates, SQL filtering, configuration tampering**
- **Justification**: Lower immediate impact but still exploitable in admin context
- **Priority**: Complete the TOP-10 with diverse vulnerability patterns

---

## Excluded from TOP-10 (with reasons)

### QuickEditOverviewAjaxController::actionDataTable()
- **Reason**: Essentially same as DataTableHelper::getOrderByClause() (#3) - would be duplicate analysis
- **Alternative**: Analyzing DataTableHelper methods directly covers this

### QuickEditOverviewAjaxController::actionTooltips()
- **Reason**: Similar to actionDataTable(), uses same DataTableHelper
- **Alternative**: DataTableHelper analysis covers this usage

### QuickEditOverviewAjaxController::actionCreateInventoryFile()
- **Reason**: PDF generation with admin data, lower risk than file deletion
- **Impact**: Limited compared to TOP-10 selections

### QuickEditOverviewAjaxController::actionDownloadInventoryFile()
- **Reason**: Downloads generated file, path not user-controlled
- **Impact**: LOW risk, no direct user input in file path

### GambioStoreController::actionDefault()
- **Reason**: Token reset operation, simple GET parameter
- **Impact**: LOW risk, just resets configuration value

---

## Next Steps for STEP 4-6 (Deep Trace Analysis)

For each TOP-10 entrypoint:

1. **INPUT LIST**: Document all external variables with exact line numbers
2. **TAINT TRACE**: Follow data flow from source to sink with transformations
3. **CONTROL-ELIMINATION FILTER**: 
   - Identify controls that eliminate vulnerability
   - Or prove vulnerability survives to sink
4. **FINDINGS**: Document proven vulnerabilities with PoC requirements
5. **PoC DEVELOPMENT**: Create Python 3 proof-of-concept (max 2 requests) if exploitable

**Analysis Order**: Follow ranking order (#1 → #10) for systematic coverage.

---

*Triage phase complete. Ready for STEP 4 - Deep Trace Analysis (starting with entrypoint #1).*
