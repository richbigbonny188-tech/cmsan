<?php
/* --------------------------------------------------------------
 ApiErrorHandlerServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Api\Application\ErrorHandling;

use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Class ApiErrorHandlerServiceProvider
 *
 * @package Gambio\Api\Application\ErrorHandling
 */
class ApiErrorHandlerServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ApiErrorHandler::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(ApiErrorHandler::class)->addArgument(ResponseFactoryInterface::class);
    }
}