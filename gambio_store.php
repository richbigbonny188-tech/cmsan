<?php
/* --------------------------------------------------------------
   processUpdate.php 2019-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

header('Content-Type: application/json');

if(!isset($_GET['do']) || !isset($_COOKIE['auto_updater_admin_check']) || $_COOKIE['auto_updater_admin_check'] !== 'admin_logged_in')
{
	echo json_encode(['success' => 'false']);
	die();
}

# Set the local configuration parameters - mainly for developers - if exists else the mainconfigure
if(file_exists(__DIR__ . '/includes/local/configure.php'))
{
	require_once __DIR__ . '/includes/local/configure.php';
}
else
{
	require_once __DIR__ . '/includes/configure.php';
}

$GLOBALS['coo_debugger'] = null;

require_once DIR_FS_CATALOG . 'vendor/autoload.php';

require_once DIR_FS_CATALOG . 'vendor/gambio/codeigniter-db/CIDB.php';
require_once DIR_FS_CATALOG . 'system/core/caching/DataCache.inc.php';
require_once DIR_FS_CATALOG . 'system/core/logging/LogControl.inc.php';

require_once DIR_FS_CATALOG
             . 'GXMainComponents/Controllers/HttpView/Admin/auto_updater/AutoUpdaterShopExcludedAjaxController.inc.php';

require_once DIR_FS_CATALOG
             . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterBackupCreationFailedException.inc.php';
require_once DIR_FS_CATALOG
             . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterBackupRestoreFailedException.inc.php';
require_once DIR_FS_CATALOG
             . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterCouldNotExtractUpdateZipException.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterException.inc.php';
require_once DIR_FS_CATALOG
             . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterMissingPermissionException.inc.php';
require_once DIR_FS_CATALOG
             . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterMovingUpdateFilesFailedException.inc.php';
require_once DIR_FS_CATALOG
             . 'GXMainComponents/Extensions/AutoUpdater/Exceptions/AutoUpdaterUpdateDownloadFailedException.inc.php';

require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/FtpManager/AutoUpdaterFilesystem.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/FtpManager/AutoUpdaterFtpAdapter.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/FtpManager/AutoUpdaterFtpManager.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/FtpManager/AutoUpdaterSFtpAdapter.inc.php';

require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/Helper/AbstractAutoUpdaterHelper.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/Helper/AutoUpdaterBackupHelper.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/Helper/AutoUpdaterDownloadHelper.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/Helper/AutoUpdaterUpdatesHelper.inc.php';

require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/ValueObjects/AutoUpdaterBackup.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/ValueObjects/AutoUpdaterUpdate.inc.php';

require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/AutoUpdater.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/AutoUpdaterCurlClient.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/AutoUpdaterDataCache.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/AutoUpdaterFactory.inc.php';
require_once DIR_FS_CATALOG . 'GXMainComponents/Extensions/AutoUpdater/AutoUpdaterSettings.inc.php';

ob_start();
$controller = new AutoUpdaterShopExcludedAjaxController();
$dbError = ob_get_contents();
ob_end_clean();

if(!empty($dbError))
{
	
	echo json_encode([
		                 'success'  => false,
		                 'error'    => $dbError,
		                 'list'     => [],
		                 'state'    => 'ajax_error_unexpected_error',
		                 'done'     => false,
		                 'progress' => 1,
	                 ]);
	die();
}

switch($_GET['do'])
{
	case 'processUpdate':
		$controller->actionInstallUpdate();
		break;
	case 'restoreBackup':
		$controller->actionRestoreBackup();
		break;
	default:
		echo json_encode(['success' => 'false']);
}
