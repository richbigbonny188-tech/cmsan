<?php
/* --------------------------------------------------------------
   MiddlewareRegistration.php 2021-07-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Admin\Application\Middleware\AdminActivityLogMiddleware;
use Gambio\Admin\Application\Middleware\AuthMiddleware;
use Gambio\Admin\Application\Middleware\MiddlewareServiceProvider;
use Gambio\Admin\Application\Middleware\SecurityHeadersMiddleware;
use Gambio\Admin\Application\Middleware\UpdaterRedirectMiddleware;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Slim\App as SlimApp;

/**
 * Class MiddlewareRegistration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class MiddlewareRegistration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $application->registerProvider(MiddlewareServiceProvider::class);
        
        /** @var SlimApp $slim */
        $slim = $application->get(SlimApp::class);
        $slim->add(AuthMiddleware::class);
        $slim->add(SecurityHeadersMiddleware::class);
        $slim->add(AdminActivityLogMiddleware::class);
        $slim->add(UpdaterRedirectMiddleware::class);
    }
}