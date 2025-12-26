<?php
/* --------------------------------------------------------------
   SentryErrorHandler.inc.php 2023-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\ErrorHandling\App\Data\SentryBeforeSendCallback;

$configPath = DIR_FS_CATALOG . 'GXModules/Gambio/ErrorReporting/configuration.json';

if(!file_exists($configPath)) {
	return;
}

$sentryConfig = json_decode(file_get_contents($configPath), true);

if (!$sentryConfig['active']) {
	return;
}

include(DIR_FS_CATALOG . 'release_info.php');

$shopDirectory = dirname(__DIR__, 3);
$isDev         = file_exists($shopDirectory . '/.dev-environment');
$isCloud       = file_exists($shopDirectory . '/version_info/cloud.php');

$environmentName = $isDev || str_contains($gx_version, 'develop') ? 'development' : 'production';
$sentryOptions   = ['dsn' => $sentryConfig['dsn'], 'before_send' => new SentryBeforeSendCallback];

if ($isCloud) {
    
    $sentryOptions = array_merge($sentryOptions , [
        'environment'   => $environmentName,
        'release'       => $gx_version,
        'prefixes'      => [DIR_FS_CATALOG, DIR_FS_DOCUMENT_ROOT],
        'error_types'   => E_ALL & ~E_NOTICE & ~E_USER_NOTICE & ~E_CORE_ERROR & ~E_CORE_WARNING & ~E_STRICT
                           & ~E_DEPRECATED,
        'send_attempts' => 1,
    ]);
} else {
    //  turn of automatic reporting
    $sentryOptions = array_merge($sentryOptions , ['default_integrations' => false]);
}

Sentry\init($sentryOptions);