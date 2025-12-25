<?php
/* --------------------------------------------------------------
 AdminErrorHandlerRegistration.php 2021-07-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Admin\Application\ErrorHandling\AdminExceptionHandler;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\ErrorHandling\Services\DefaultErrorHandler;
use Slim\App as SlimApp;

/**
 * Class AdminErrorHandlerRegistration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class AdminErrorHandlerRegistration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $this->registerErrorHandler($application);
        $this->registerExceptionHandler($application);
    }
    
    
    private function registerErrorHandler(Application $application): void
    {
        $handler = $application->get(DefaultErrorHandler::class);
        
        set_error_handler([$handler, 'handleError'], E_ALL);
    }
    
    
    private function registerExceptionHandler(Application $application): void
    {
        /** @var SlimApp $slim */
        $slim = $application->get(SlimApp::class);
        
        $debugMode       = $this->isDev($application);
        $errorMiddleware = $slim->addErrorMiddleware($debugMode, false, false);
        $errorMiddleware->setDefaultErrorHandler(AdminExceptionHandler::class);
    }
    
    
    private function isDev(Application $application): bool
    {
        $environment = $this->getEnvironment($application);
        
        return $environment && $environment->isDev();
    }
    
    
    private function getEnvironment(Application $application): ?Environment
    {
        return $application->has(Environment::class) ? $application->get(Environment::class) : null;
    }
}