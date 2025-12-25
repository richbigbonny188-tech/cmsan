<?php
/* --------------------------------------------------------------
 ApiMiddlewareServiceProvider.php 2020-11-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Api\Application\Middleware;

use Gambio\Api\Application\Auth\Interfaces\WebRequestAuthenticationService;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\ClearCacheService;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Class ApiMiddlewareServiceProvider
 *
 * @package Gambio\Api\Application\Middleware
 */
class ApiMiddlewareServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            VersionsMiddleware::class,
            AuthenticationMiddleware::class,
            RateLimitMiddleware::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(VersionsMiddleware::class);
        
        $this->application->registerShared(RateLimitMiddleware::class,
            function () {
                /** @var CacheFactory $cacheFactory */
                $cacheFactory = $this->application->get(CacheFactory::class);
                
                return new RateLimitMiddleware($this->application->get(ResponseFactoryInterface::class),
                                               $cacheFactory->createCacheFor('gxapi_v3_sessions'));
            });
        
        $this->application->registerShared(AuthenticationMiddleware::class)
            ->addArgument(ResponseFactoryInterface::class)
            ->addArgument(WebRequestAuthenticationService::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(ClearCacheService::class)
            ->invokeMethod('addNamespaceToSystemCaches', ['gxapi_v3_sessions']);
    }
}