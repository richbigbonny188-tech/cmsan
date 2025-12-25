<?php
/* --------------------------------------------------------------
 LoadConfiguration.php 2022-08-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Server;
use Gambio\Core\Application\ValueObjects\Url;

if (!defined('HTTP_SERVER')) {
    require_once __DIR__ . '/../../../admin/includes/configure.php';
}

/**
 * Class LoadConfiguration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class LoadConfiguration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $host       = HTTP_SERVER;
        $webPath    = rtrim(DIR_WS_CATALOG, '/');
        $serverPath = rtrim(DIR_FS_CATALOG, '/');
        
        if (defined('ENABLE_SSL_CATALOG')) {
            $isSslEnabled = strtolower(ENABLE_SSL_CATALOG) === 'true';
        } else {
            $isSslEnabled = ENABLE_SSL;
        }
        
        $requestUri   = $_SERVER['REQUEST_URI'];
        
        $application->registerShared(Path::class)->addArgument($serverPath);
        $application->registerShared(Url::class)->addArgument($host)->addArgument($webPath);
        $application->registerShared(Server::class)->addArgument($isSslEnabled)->addArgument($requestUri);
    }
}