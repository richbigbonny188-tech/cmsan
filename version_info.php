<?php
/* --------------------------------------------------------------
   version_info.php 2023-04-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/*
	Example JSON response structure:

	{
		"versionInfo": {
			"3_1_2_0_beta1": "version: 3.1.2.0 BETA1
							 date: 2016-07-22 18:01",
			"ku-3_1_2_0_beta1": "version: 3.1.2.0 BETA1
								date: 2016-07-22 18:01",
			"sp-3_1_2_0_beta1": "version: 3.1.2.0 BETA1
								date: 2016-07-22 18:01"
		},
		"precheck": {
			"shopUrl": "http://myshop.com",
			"shopVersion": "3.1_develop",
			"serverAddress": "192.168.2.101",
			"serverOs": "Ubuntu Linux",
			"phpVersion": "5.4.0",
			"mysqlVersion": "5.7",
			"isCurlEnabled": true,
			"activeTemplate": "Honeygrid",
			"additionalTemplates": ["EyeCandy"],
			"usermods": [
				"/usr/folder/foo-USERMOD.html",
				"/usr/another/folder/bar-USERMOD.css",
			],
			"isInstalledMobileCandy": false,
			"mobileCandyUsermods": [
				"/usr/folder/foo-USERMOD.html",
				"/usr/another/folder/bar-USERMOD.css",
			],
			"hasGlobalUsermodDir": false,
			"userComponents": [],
			"foreignPaymentModules": [],
			"foreignShippingModules": [],
			"foreignOrderTotalModules": [],
			"foreignModules": [],
			"foreignLanguages": [],
			"dangerousTools": [],
			"isPhpVersionAboveMinimum": true,
			"isPhpVersionBelowMaximum": true,
			"isUsingWindowsOs": false,
			"isMySqlVersionAboveMinimum": true,
			"isMySqlVersionBelowMaximum": true,
		}
	}

*/

include('includes/application_top.php');
include('release_info.php');

// Error message to display on missing/invalid shop key.
$errorMessage = 'SHOP_KEY_ERROR';

// Shop key check.
$hasShopKey = defined('GAMBIO_SHOP_KEY')
              && isset($_GET['shop_key'])
              && !empty($_GET['shop_key'])
              && GAMBIO_SHOP_KEY === $_GET['shop_key'];

// Installed module and languages files list directory and file paths.
$listsDirPath             = DIR_FS_CATALOG . 'version_info/lists';
$paymentModuleListFile    = $listsDirPath . '/paymentModules.txt';
$shippingModuleListFile   = $listsDirPath . '/shippingModules.txt';
$orderTotalModuleListFile = $listsDirPath . '/orderTotalModules.txt';

// Proceed if the shop key is valid and set.
// Or output the error message.
if($hasShopKey)
{
	// Retrieve lists of installed modules and languages to pass them to the Precheck.
	$installedPaymentModules       = file_exists($paymentModuleListFile) ? file($paymentModuleListFile) : array();
	$installedShippingModules      = file_exists($shippingModuleListFile) ? file($shippingModuleListFile) : array();
	$installedOrderTotalModules    = file_exists($orderTotalModuleListFile) ? file($orderTotalModuleListFile) : array();

	// Precheck settings array.
	$precheckSettings = MainFactory::create('KeyValueCollection', array(
		'shopVersion'    => $gx_version,
		'DIR_FS_CATALOG' => DIR_FS_CATALOG,
		'HTTP_SERVER'    => HTTP_SERVER,
		'DIR_WS_CATALOG' => DIR_WS_CATALOG
	));

	// Create Precheck object.
	$precheck = MainFactory::create('Precheck', StaticGXCoreLoader::getDatabaseQueryBuilder(), $precheckSettings);

	// Build array which contains the precheck data.
	$precheckArray = array(
		'shopUrl'                    => $precheck->getShopUrl(),
		'shopVersion'                => $precheck->getShopVersion(),
		'serverPath'                 => $precheck->getServerPath(),
		'serverAddress'              => $precheck->getServerAddress(),
		'serverOs'                   => $precheck->getServerOs(),
		'phpVersion'                 => $precheck->getPhpVersion(),
		'mysqlVersion'               => $precheck->getMysqlVersion(),
		'isCurlEnabled'              => $precheck->isCurlEnabled(),
		'activeTemplate'             => $precheck->getActiveTemplate(),
		'additionalTemplates'        => $precheck->getAdditionalTemplates(),
		'usermods'                   => $precheck->getUsermods(),
		'isInstalledMobileCandy'     => $precheck->isInstalledMobileCandy(),
		'mobileCandyUsermods'        => $precheck->getMobileCandyUsermods(),
		'hasGlobalUsermodDir'        => $precheck->hasGlobalUsermodDir(),
		'userComponents'             => $precheck->getUserComponents(),
		'foreignPaymentModules'      => $precheck->getForeignPaymentModules($installedPaymentModules),
		'foreignShippingModules'     => $precheck->getForeignShippingModules($installedShippingModules),
		'foreignOrderTotalModules'   => $precheck->getForeignOrderTotalModules($installedOrderTotalModules),
		'foreignModules'             => $precheck->getForeignModules(),
		'foreignLanguages'           => $precheck->getForeignLanguages(),
		'dangerousTools'             => $precheck->getDangerousTools(),
		'isPhpVersionAboveMinimum'   => $precheck->isPhpVersionAboveMinimum(),
		'isPhpVersionBelowMaximum'   => $precheck->isPhpVersionBelowMaximum(),
		'isUsingWindowsOs'           => $precheck->isUsingWindowsOs(),
		'isMySqlVersionAboveMinimum' => $precheck->isMySqlVersionAboveMinimum(),
		'isMySqlVersionBelowMaximum' => $precheck->isMySqlVersionBelowMaximum(),
        'isMagnalisterActive'        => $precheck->isMagnalisterActive(),
	);

	// Create VersionInfo object.
	$versionInfo = MainFactory::create_object('VersionInfo');

	// Retrieve version info data.
	$versionInfoArray = $versionInfo->get_shop_versioninfo();

	// Output JSON-encoded response array containing the data.
	$reponseArray = array(
		'versionInfo' => $versionInfoArray,
		'precheck'    => $precheckArray
	);

	echo json_encode($reponseArray);
}
else
{
	echo json_encode($errorMessage);
}