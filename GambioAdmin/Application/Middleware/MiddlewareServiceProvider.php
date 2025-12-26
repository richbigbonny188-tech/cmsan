<?php
/* --------------------------------------------------------------
 MiddlewareServiceProvider.php 2020-11-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Middleware;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\DSGVO\Services\DSGVOService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\App\Creation\ConfigurationFinderBuilder;
use Gambio\Core\Permission\Services\PermissionService;

/**
 * Class MiddlewareServiceProvider
 *
 * @package Gambio\Admin\Application\Middleware
 */
class MiddlewareServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AuthMiddleware::class,
            SecurityHeadersMiddleware::class,
            AdminActivityLogMiddleware::class,
            UpdaterRedirectMiddleware::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AuthMiddleware::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Url::class)
            ->addArgument(PermissionService::class);
        
        $this->application->registerShared(SecurityHeadersMiddleware::class)
            ->addArgument(ConfigurationFinderBuilder::class);
        
        $this->application->registerShared(AdminActivityLogMiddleware::class)->addArgument(DSGVOService::class);
        
        $this->registerUpdaterRedirectMiddleware();
    }
    
    
    private function registerUpdaterRedirectMiddleware(): void
    {
        $dependencies = [
            Connection::class,
            Url::class,
            Path::class,
        ];
        $this->application->registerShared(UpdaterRedirectMiddleware::class)->addArguments($dependencies);
    }
}