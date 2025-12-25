<?php
/* --------------------------------------------------------------
 AuthenticationMiddlewareServiceProvider.php 2021-12-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Api\Application\Auth;

use Gambio\Api\Application\Auth\Interfaces\WebRequestAuthenticationService;
use Gambio\Api\Application\Auth\Interfaces\WebRequestUserIdentificationService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Auth\JsonWebTokenAuthenticator;
use Gambio\Core\Auth\UserAuthenticator;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Permission\Services\PermissionService;

/**
 * Class RequestAuthenticationServiceServiceProvider
 *
 * @package Gambio\Api\Application\Auth
 */
class RequestAuthenticationServiceServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            WebRequestAuthenticationService::class,
            WebRequestUserIdentificationService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->register(BasicRequestAuthenticator::class)->addArgument(UserAuthenticator::class);
        $this->application->register(BearerRequestAuthenticator::class)->addArgument(JsonWebTokenAuthenticator::class);
        
        $this->application->register(WebRequestAuthenticationService::class, RequestAuthenticationService::class)
            ->addArgument(PermissionService::class)
            ->addArgument(Url::class)
            ->addArgument(BasicRequestAuthenticator::class)
            ->addArgument(BearerRequestAuthenticator::class);
    
        $this->application->register(WebRequestUserIdentificationService::class,
            function (): WebRequestUserIdentificationService {
                return $this->application->get(WebRequestAuthenticationService::class);
            });
    }
}