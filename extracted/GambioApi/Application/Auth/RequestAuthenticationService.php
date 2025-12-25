<?php
/* --------------------------------------------------------------
   RequestAuthenticationService.php 2021-12-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Auth;

use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\PermissionAction;
use Gambio\Api\Application\Auth\Interfaces\WebRequestAuthenticationService;
use Gambio\Api\Application\Auth\Interfaces\WebRequestAuthenticator;
use Gambio\Api\Application\Auth\Interfaces\WebRequestUserIdentificationService;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Auth\Exceptions\AuthenticationException;
use Gambio\Core\Permission\Services\PermissionService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestAuthenticationService
 *
 * @package Gambio\Core\Auth
 */
class RequestAuthenticationService implements WebRequestAuthenticationService, WebRequestUserIdentificationService
{
    /**
     * @var PermissionService
     */
    private $permissionService;
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var WebRequestAuthenticator[]
     */
    private $authenticators;
    
    
    /**
     * RequestAuthenticationService constructor.
     *
     * @param PermissionService       $permissionService
     * @param Url                     $url
     * @param WebRequestAuthenticator ...$authenticators
     */
    public function __construct(
        PermissionService $permissionService,
        Url $url,
        WebRequestAuthenticator ...$authenticators
    ) {
        $this->permissionService = $permissionService;
        $this->url               = $url;
        $this->authenticators    = $authenticators;
    }
    
    
    /**
     * @inheritDoc
     */
    public function authenticateWebRequest(ServerRequestInterface $request): bool
    {
        foreach ($this->authenticators as $authenticator) {
            try {
                $userId = $authenticator->authenticateWebRequest($request);
                
                return $this->checkAdminPermission($userId->userId(), $request);
            } catch (AuthenticationException $exception) {
                // try next authenticator
            }
        }
        
        return false;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addAuthenticator(WebRequestAuthenticator $authenticator): WebRequestAuthenticationService
    {
        $this->authenticators[] = $authenticator;
        
        return $this;
    }
    
    
    /**
     * @inheritDoc
     */
    public function identifyUser(ServerRequestInterface $request): ?int
    {
        $userId = null;
        
        foreach ($this->authenticators as $authenticator) {
            try {
    
                $userId = $authenticator->authenticateWebRequest($request)->userId();
            } catch (AuthenticationException $exception) {
                // try next authenticator
            }
        }
        
        return $userId;
    }
    
    
    /**
     * @param int                    $userId
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function checkAdminPermission(int $userId, ServerRequestInterface $request): bool
    {
        $action              = PermissionAction::READ;
        $groupItemType       = AccessGroupItem::ROUTE_TYPE;
        $groupItemDescriptor = substr($request->getUri()->getPath(), strlen($this->url->path()));
        $groupItemDescriptor = rtrim($groupItemDescriptor, '/');
        switch (strtolower($request->getMethod())) {
            case 'post':
            case 'patch':
            case 'put':
                $action = PermissionAction::WRITE;
                break;
            case 'delete':
                $action = PermissionAction::DELETE;
                break;
        }
        
        return $this->permissionService->checkAdminPermission($userId, $action, $groupItemType, $groupItemDescriptor);
    }
}