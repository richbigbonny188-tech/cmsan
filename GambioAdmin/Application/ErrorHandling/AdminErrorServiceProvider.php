<?php
/* --------------------------------------------------------------
 AdminErrorServiceProvider.php 2021-01-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\ErrorHandling;

use Gambio\Admin\Layout\Renderer\GambioAdminRenderer;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\TextManager\Services\TextManager;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;

/**
 * Class AdminErrorServiceProvider
 * @package Gambio\Admin\Application\ErrorHandling
 */
class AdminErrorServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AdminExceptionHandler::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AdminExceptionHandler::class)
            ->addArgument(CallableResolverInterface::class)
            ->addArgument(ResponseFactoryInterface::class)
            ->addArgument(GambioAdminRenderer::class)
            ->addArgument(TextManager::class)
            ->addArgument(UserPreferences::class);
    }
}