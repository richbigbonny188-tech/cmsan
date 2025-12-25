<?php
/*--------------------------------------------------------------
   GetDisallowedShippingMethodsAction.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App\Actions;

use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsReadService;
use Gambio\Admin\Modules\ShippingModule\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class GetDisallowedShippingMethodsAction
 *
 * @package Gambio\Admin\Modules\ShippingModule\App\Actions
 */
class GetDisallowedShippingMethodsAction extends AbstractAction
{
    private CustomerDisallowedShippingMethodsReadService $service;
    
    
    /**
     * @param CustomerDisallowedShippingMethodsReadService $service
     */
    public function __construct(CustomerDisallowedShippingMethodsReadService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $customerId = (int)$request->getAttribute('customerId');
            $methods    = $this->service->getCustomersDisallowedShippingMethods($customerId);
            
            return $response->withJson($methods->toArray());
        } catch (CustomerDoesNotExistException $e) {
            
            return $response->withStatus(404);
        }
    }
}