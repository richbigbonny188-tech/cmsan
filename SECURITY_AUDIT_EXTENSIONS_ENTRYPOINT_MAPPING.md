# Security Audit - STEP 2: Entrypoint Mapping for GXMainComponents/Extensions

**Target**: Gambio GX - GXMainComponents/Extensions  
**Date**: 2025-12-26  
**Audit Type**: Authorized white-box security assessment  
**Phase**: STEP 2 - ENTRYPOINT MAPPING

---

## HTTP Entrypoints Table

### High-Risk Area: AutoUpdater

| Entrypoint File | Handler | Methods | Input Params | Auth Zone | Risk Level |
|-----------------|---------|---------|--------------|-----------|------------|
| AutoUpdaterAjaxController.inc.php | AutoUpdaterAjaxController::actionCheckPermission() | POST | gambioStoreData (JSON) | ADMIN | CRITICAL |
| AutoUpdaterAjaxController.inc.php | AutoUpdaterAjaxController::actionCheckPermissionForBackupRestore() | POST | backupId | ADMIN | HIGH |
| AutoUpdaterAjaxController.inc.php | AutoUpdaterAjaxController::actionDeleteBackup() | POST | backupId | ADMIN | HIGH |
| AutoUpdaterAjaxController.inc.php | AutoUpdaterAjaxController::actionCheckFtpConnection() | POST | ftp-protocol, ftp-server, ftp-login, ftp-password, ftp-port, ftp-passive | ADMIN | CRITICAL |
| AutoUpdaterAjaxController.inc.php | AutoUpdaterAjaxController::actionUninstallTheme() | POST | themeName | ADMIN | CRITICAL |

**Location**: `/GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterAjaxController.inc.php`

**Input Sources:**
- Line 81: `$gambioStoreData = $this->_getPostData('gambioStoreData');` - JSON data from external store
- Line 82: `$gambioStoreData = str_replace('\"', '"', $gambioStoreData);` - String replacement
- Line 83: `$gambioStoreData = json_decode($gambioStoreData, true);` - JSON deserialization
- Line 84: `$gambioStorePackage = AutoUpdaterUpdate::createByGambioStoreData($gambioStoreData);` - Object creation from external data
- Line 117: `$backupId = $this->_getPostData('backupId');` - Backup identifier
- Line 146: `$backupId = $this->_getPostData('backupId');` - Backup identifier for deletion
- Line 171-176: FTP connection parameters (protocol, server, login, password, port, passive mode)
- Line 211: `$themeName = $this->_getPostData('themeName');` - Theme name for uninstallation
- Line 221: `is_dir(DIR_FS_CATALOG . 'themes/' . $themeName)` - Direct path concatenation

**Sinks Identified:**
- **File Operations**: Line 221, 242 - Directory checks and deletion with user-controlled `themeName`
- **FTP Operations**: Line 188 - FTP connection with user-provided credentials
- **File Permission Checks**: Line 94, 131 - File permission checks on user-provided file lists
- **Backup Operations**: Line 155 - Backup deletion with user-provided ID
- **JSON Deserialization**: Line 83 - Deserializing external Gambio Store data

---

### High-Risk Area: StyleEdit4

| Entrypoint File | Handler | Methods | Input Params | Auth Zone | Risk Level |
|-----------------|---------|---------|--------------|-----------|------------|
| StyleEdit4AuthenticationController.inc.php | StyleEdit4AuthenticationController::__construct() | GET | welcome, startPageUrl | ADMIN | MEDIUM |
| AbstractStyleEditAuthenticationController.php | (parent class) | Various | Various | ADMIN | MEDIUM |

**Location**: `/GXMainComponents/Controllers/HttpView/Admin/StyleEdit4AuthenticationController.inc.php`

**Input Sources:**
- Line 43: `$this->urlParameters['language'] = $_SESSION['language_code'];` - Session data
- Line 44: `$this->urlParameters['customer_id'] = $_SESSION['customer_id'];` - Session data
- Line 54: `isset($_GET['welcome'])` - GET parameter check
- Line 58-60: `$this->urlParameters['startPageUrl'] = $_GET['startPageUrl'];` - GET parameter without sanitization
- Line 157: `header('Location: ' . $redirectUrl);` - Header injection potential

**Sinks Identified:**
- **Header Injection**: Line 157 - Redirect with URL parameters from GET
- **Session JWT**: Line 122 - JWT encoding of session data
- **URL Manipulation**: Line 58-60 - Direct assignment of GET parameter to URL

---

### Medium-Risk Area: DataTableHelper (via QuickEdit Controllers)

| Entrypoint File | Handler | Methods | Input Params | Auth Zone | Risk Level |
|-----------------|---------|---------|--------------|-----------|------------|
| QuickEditOverviewAjaxController.inc.php | QuickEditOverviewAjaxController::actionDataTable() | REQUEST | draw, start, length, order, columns | ADMIN | MEDIUM |
| QuickEditOverviewAjaxController.inc.php | QuickEditOverviewAjaxController::actionTooltips() | REQUEST | start, length, order, columns | ADMIN | MEDIUM |
| QuickEditOverviewAjaxController.inc.php | QuickEditOverviewAjaxController::actionUpdate() | REQUEST | data[productId][changes] | ADMIN | MEDIUM |
| QuickEditOverviewAjaxController.inc.php | QuickEditOverviewAjaxController::actionCreateInventoryFile() | POST | inventoryList, products | ADMIN | MEDIUM |
| QuickEditOverviewAjaxController.inc.php | QuickEditOverviewAjaxController::actionDownloadInventoryFile() | GET | None (uses generated file) | ADMIN | LOW |

**Location**: `/GXMainComponents/Controllers/HttpView/AdminAjax/QuickEditOverviewAjaxController.inc.php`

**Input Sources:**
- Line 97: `$_REQUEST['draw']` - DataTables draw parameter
- Line 138: `$_REQUEST['start']` - Pagination start
- Line 139: `$_REQUEST['length']` - Pagination length
- Line 250: `$_REQUEST['data']` - Product update data array
- Line 354-355: `$_REQUEST['start']`, `$_REQUEST['length']` - Pagination parameters
- Line 442: `$_REQUEST['productId']` - Product identifier

**DataTableHelper Input Processing:**
- Line 43: `$orderBy = $_REQUEST['order'];` - ORDER BY parameter
- Line 53: `$_REQUEST['columns'][$order['column']]` - Column reference
- Line 91: `$_REQUEST['columns']` - All columns for filtering

**Sinks Identified:**
- **SQL ORDER BY**: DataTableHelper lines 42-72 - ORDER BY clause construction from REQUEST parameters
- **SQL WHERE**: DataTableHelper lines 87-118 - Filter parameters from REQUEST
- **File Download**: QuickEditOverviewAjaxController line 235 - `readfile($filePath)` with generated path

---

### Medium-Risk Area: GambioStore

| Entrypoint File | Handler | Methods | Input Params | Auth Zone | Risk Level |
|-----------------|---------|---------|--------------|-----------|------------|
| GambioStoreController.inc.php | GambioStoreController::actionDefault() | GET | reset-token | ADMIN | LOW |
| GambioStoreController.inc.php | GambioStoreController::actionConfiguration() | POST | url | ADMIN | MEDIUM |

**Location**: `/GXMainComponents/Controllers/HttpView/Admin/GambioStoreController.inc.php`

**Input Sources:**
- Line 47: `$this->_getQueryParameter('reset-token')` - GET parameter
- Line 141-146: `$_POST['url']` - Gambio Store URL configuration

**Sinks Identified:**
- **Configuration Write**: Line 48, 62, 144 - `gm_set_conf()` with user input
- **URL Validation**: Line 143 - `filter_var($_POST['url'], FILTER_VALIDATE_URL)` - Basic validation present
- **Token Generation**: Line 58-62 - Token generation and storage

---

### Supporting Components Analysis

#### DataTableHelper Methods

**File**: `/GXMainComponents/Extensions/Helpers/DataTableHelper/DataTableHelper.inc.php`

**Method**: `getOrderByClause()` (Lines 41-73)
- **Input**: `$_REQUEST['order']` - Array of order specifications
- **Input**: `$_REQUEST['columns']` - Column definitions
- **Processing**: 
  - Line 52: `strtoupper($order['dir'])` - Direction uppercase conversion
  - Line 53: `$_REQUEST['columns'][$order['column']]` - Column lookup by index
  - Line 59: `$columns->findByName(new StringType($column['name']))->getField()` - Field name lookup
  - Line 65: `explode(' ', $field)` - Field splitting
  - Line 68: `$section . ' ' . $direction` - ORDER BY clause construction
- **Control**: StringType validation on column name, field from column definition object
- **Risk**: Indirect SQL injection if field definitions are not properly sanitized

**Method**: `getFilterParameters()` (Lines 87-118)
- **Input**: `$_REQUEST['columns']` - Column filter values
- **Processing**:
  - Line 92-93: Column name and search value extraction
  - Line 106-108: Regex pattern matching for range filters: `/^([\d\.]+)\s*-\s*([\d\.]+)$/`
  - Line 112: String filter assignment
- **Control**: Type-based filtering (NUMBER, DATE, STRING), trim on values
- **Risk**: Filter values passed to query builders with limited sanitization

---

## Entrypoint Summary by Risk Level

### CRITICAL Risk (3 entrypoints)
1. **AutoUpdaterAjaxController::actionCheckPermission()** - JSON deserialization of external Gambio Store data
2. **AutoUpdaterAjaxController::actionCheckFtpConnection()** - FTP credential handling
3. **AutoUpdaterAjaxController::actionUninstallTheme()** - File system operations with user-controlled path component

### HIGH Risk (2 entrypoints)
1. **AutoUpdaterAjaxController::actionCheckPermissionForBackupRestore()** - Backup file operations
2. **AutoUpdaterAjaxController::actionDeleteBackup()** - Backup deletion with user ID

### MEDIUM Risk (7 entrypoints)
1. **StyleEdit4AuthenticationController** - URL parameter injection in redirects
2. **QuickEditOverviewAjaxController::actionDataTable()** - ORDER BY and WHERE clause construction
3. **QuickEditOverviewAjaxController::actionTooltips()** - ORDER BY clause construction
4. **QuickEditOverviewAjaxController::actionUpdate()** - Product data updates
5. **QuickEditOverviewAjaxController::actionCreateInventoryFile()** - PDF generation with user data
6. **GambioStoreController::actionConfiguration()** - URL configuration update
7. **DataTableHelper::getOrderByClause()** - SQL ORDER BY construction

### LOW Risk (2 entrypoints)
1. **GambioStoreController::actionDefault()** - Token reset via GET parameter
2. **QuickEditOverviewAjaxController::actionDownloadInventoryFile()** - File download with generated path

---

## Key Vulnerability Patterns Identified

### 1. Path Traversal in AutoUpdater
**Location**: AutoUpdaterAjaxController.inc.php, line 221
```php
if(!is_dir(DIR_FS_CATALOG . 'themes/' . $themeName))
```
- **Issue**: Direct concatenation of user input `$themeName` to filesystem path
- **Attack Vector**: `$themeName = '../../../etc'` could access parent directories
- **Control Present**: Line 221 checks if directory exists in themes folder
- **Validation**: Line 213-219 checks if themeName is empty but no sanitization

### 2. SQL Injection in DataTableHelper ORDER BY
**Location**: DataTableHelper.inc.php, lines 42-72
```php
$orderBy = $_REQUEST['order'];
...
$field = $columns->findByName(new StringType($column['name']))->getField();
...
$orderByClause[] = $section . ' ' . $direction;
```
- **Issue**: ORDER BY clause constructed from REQUEST parameters
- **Attack Vector**: Column name or direction manipulation
- **Control Present**: 
  - Column name validated via StringType and column collection lookup
  - Direction forced to uppercase (line 52)
  - Field value from column definition object, not direct user input
- **Validation**: Field comes from predefined column definitions, not raw user input

### 3. Open Redirect in StyleEdit4
**Location**: StyleEdit4AuthenticationController.inc.php, lines 58-60, 157
```php
if (!empty($_GET['startPageUrl'])) {
    $this->urlParameters['startPageUrl'] = $_GET['startPageUrl'];
}
...
header('Location: ' . $redirectUrl);
```
- **Issue**: GET parameter included in redirect URL without validation
- **Attack Vector**: `startPageUrl=http://evil.com` could redirect to external site
- **Control Present**: URL built with `http_build_query()` and `get_href_link()` function (line 144-155)
- **Validation**: Partial - URL built through framework function but GET parameter not validated

### 4. JSON Deserialization in AutoUpdater
**Location**: AutoUpdaterAjaxController.inc.php, lines 81-84
```php
$gambioStoreData = $this->_getPostData('gambioStoreData');
$gambioStoreData = str_replace('\"', '"', $gambioStoreData);
$gambioStoreData = json_decode($gambioStoreData, true);
$gambioStorePackage = AutoUpdaterUpdate::createByGambioStoreData($gambioStoreData);
```
- **Issue**: External JSON data deserialized without schema validation
- **Attack Vector**: Malicious JSON payload from compromised Gambio Store
- **Control Present**: Data passed to AutoUpdaterUpdate object for processing
- **Validation**: Unknown - depends on AutoUpdaterUpdate::createByGambioStoreData() implementation

### 5. FTP Credential Handling
**Location**: AutoUpdaterAjaxController.inc.php, lines 171-188
```php
$protocol = $this->_getPostData('ftp-protocol');
$server   = $this->_getPostData('ftp-server');
$login    = $this->_getPostData('ftp-login');
$password = $this->_getPostData('ftp-password');
...
$this->autoUpdaterFactory->createFtpManager($protocol, $server, $login, $password, $port, $passive);
```
- **Issue**: FTP credentials passed directly to FTP manager
- **Attack Vector**: SSRF via malicious FTP server, credential stuffing
- **Control Present**: Empty check on protocol, server, login (lines 178-184)
- **Validation**: Basic empty checks but no URL/hostname validation

---

## Authentication Context

All identified entrypoints in Extensions are **ADMIN-PROTECTED**:
- AutoUpdaterAjaxController extends `AdminHttpViewController`
- QuickEditOverviewAjaxController extends `AdminHttpViewController`
- StyleEdit4AuthenticationController checks `$_SESSION['customer_id']` (line 77)
- GambioStoreController extends `AdminHttpViewController`

**Admin Authentication Required** - All entrypoints require valid admin session.

---

## Controllers Not Analyzed (Low Priority)

The following controllers were found but not included in detailed mapping due to lower risk profiles:
- `AutoUpdaterShopExcludedAjaxController.inc.php` - Shop exclusion management
- `QuickEditSpecialPricesAjaxController.inc.php` - Special price editing
- `QuickEditProductPropertiesAjaxController.inc.php` - Product properties editing
- `InvoicesOverviewAjaxController.inc.php` - Invoice overview (uses DataTableHelper)
- `OrdersOverviewAjaxController.inc.php` - Orders overview (uses DataTableHelper)

These follow similar patterns to QuickEditOverviewAjaxController with DataTableHelper usage.

---

## Next Steps (STEP 3 - TRIAGE)

Based on entrypoint mapping, recommend TOP-10 for deep trace analysis:

1. **AutoUpdaterAjaxController::actionUninstallTheme()** - Path traversal in theme deletion
2. **AutoUpdaterAjaxController::actionCheckPermission()** - JSON deserialization from external source
3. **AutoUpdaterAjaxController::actionCheckFtpConnection()** - FTP SSRF potential
4. **DataTableHelper::getOrderByClause()** - SQL ORDER BY construction
5. **StyleEdit4AuthenticationController** - Open redirect via startPageUrl
6. **AutoUpdaterAjaxController::actionDeleteBackup()** - Backup file manipulation
7. **QuickEditOverviewAjaxController::actionUpdate()** - Mass product updates
8. **GambioStoreController::actionConfiguration()** - URL configuration tampering
9. **DataTableHelper::getFilterParameters()** - SQL WHERE filtering
10. **QuickEditOverviewAjaxController::actionCreateInventoryFile()** - PDF generation with user data

---

*Entrypoint mapping phase complete. Ready for STEP 3 - TRIAGE.*
