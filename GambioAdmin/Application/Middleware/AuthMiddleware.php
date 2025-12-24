<?php
/* --------------------------------------------------------------
 AuthMiddleware.php 2020-11-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Middleware;

use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\PermissionAction;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Permission\Services\PermissionService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Response;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Class AuthMiddleware
 *
 * @package Gambio\Admin\Application\Middleware
 */
class AuthMiddleware
{
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var PermissionService
     */
    private $permissionService;
    
    
    /**
     * AuthMiddleware constructor.
     *
     * @param UserPreferences   $userPreferences
     * @param Url               $url
     * @param PermissionService $permissionService
     */
    public function __construct(
        UserPreferences $userPreferences,
        Url $url,
        PermissionService $permissionService
    ) {
        $this->userPreferences   = $userPreferences;
        $this->url               = $url;
        $this->permissionService = $permissionService;
    }
    
    
    /**
     * Middleware callback ensuring that a admin is logged in.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $isNotAuthenticated = !$this->userPreferences->isAuthenticated();
        $userId             = $this->userPreferences->userId();
        
        if ($isNotAuthenticated || !$userId || $this->checkAdminPermission($userId, $request) === false) {
            return $this->redirectToLoginResponse($request);
        }
        
        return $handler->handle($request);
    }
    
    
    /**
     * Creates a response that is redirecting the the shop store's login.php page.
     *
     * @param ServerRequestInterface $request
     *
     * @return Response
     */
    private function redirectToLoginResponse(ServerRequestInterface $request): Response
    {
        $responseFactory = (new DecoratedResponseFactory(new ResponseFactory(), new StreamFactory()));
        $response        = $responseFactory->createResponse(401);
        $returnUrl       = '?return_url=' . rawurlencode((string)$request->getUri());
        
        return $response->withRedirect("{$this->url->base()}/login.php{$returnUrl}");
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