<?php
/*--------------------------------------------------------------
   GetDisallowedPaymentMethodsAction.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule\App\Actions;

use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsReadService;
use Gambio\Admin\Modules\PaymentModule\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class GetDisallowedPaymentMethodsAction
 *
 * @package Gambio\Admin\Modules\PaymentModule\App\Actions
 */
class GetDisallowedPaymentMethodsAction extends AbstractAction
{
    private CustomerDisallowedPaymentMethodsReadService $service;
    
    
    /**
     * @param CustomerDisallowedPaymentMethodsReadService $service
     */
    public function __construct(CustomerDisallowedPaymentMethodsReadService $service)
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
            $methods    = $this->service->getCustomersDisallowedPaymentMethods($customerId);
            
            return $response->withJson($methods->toArray());
        } catch (CustomerDoesNotExistException $e) {
            
            return $response->withStatus(404);
        }
    }
}