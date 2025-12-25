<?php
/* --------------------------------------------------------------
 AdminActivityLogMiddleware.php 2020-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Middleware;

use Gambio\Admin\Modules\DSGVO\Services\DSGVOService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AdminActivityLogMiddleware
 * @package Gambio\Admin\Application\Middleware
 */
class AdminActivityLogMiddleware implements MiddlewareInterface
{
    /**
     * @var DSGVOService
     */
    private $service;
    
    
    /**
     * AdminActivityLogMiddleware constructor.
     *
     * @param DSGVOService $service
     */
    public function __construct(DSGVOService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->service->isAdminLoggingEnabled()) {
            $this->service->logAdminActivity();
        }
        
        return $handler->handle($request);
    }
}