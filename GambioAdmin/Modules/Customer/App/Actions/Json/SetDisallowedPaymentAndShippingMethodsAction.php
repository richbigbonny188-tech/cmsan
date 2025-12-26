<?php
/*--------------------------------------------------------------
   SetDisallowedPaymentAndShippingMethodsAction.php 2022-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsWriteService as CustomerDisallowedPaymentMethodsWriteServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\Exceptions\CustomerDoesNotExistException as PaymentModuleDoesNotExistException;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsWriteService as CustomerDisallowedShippingMethodsWriteServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\Exceptions\CustomerDoesNotExistException as ShippingModuleCustomerDoesNotExistExceptionAlias;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class SetDisallowedPaymentAndShippingMethodsAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class SetDisallowedPaymentAndShippingMethodsAction
{
    private CustomerDisallowedPaymentMethodsWriteServiceInterface  $paymentService;
    private CustomerDisallowedShippingMethodsWriteServiceInterface $shippingService;
    
    
    /**
     * @param CustomerDisallowedPaymentMethodsWriteServiceInterface  $paymentService
     * @param CustomerDisallowedShippingMethodsWriteServiceInterface $shippingService
     */
    public function __construct(
        CustomerDisallowedPaymentMethodsWriteServiceInterface  $paymentService,
        CustomerDisallowedShippingMethodsWriteServiceInterface $shippingService
    ) {
        $this->paymentService  = $paymentService;
        $this->shippingService = $shippingService;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            [
                'customerId'        => $customerId,
                'shippingModuleIds' => $shippingIds,
                'paymentModuleIds'  => $paymentIds,
            ] = $request->getParsedBody();
            
            if (($customerId = (int)$customerId) <= 0) {
                
                return $response->withJson(['error' => 'Invalid customer ID given. Got: ' . $customerId], 400);
            }
            
            $this->shippingService->setDisallowedShippingMethods($customerId, ...$this->sanitizeValues($shippingIds));
            $this->paymentService->setDisallowedPaymentMethods($customerId, ...$this->sanitizeValues($paymentIds));
            
            return $response->withStatus(204);
        } catch (ShippingModuleCustomerDoesNotExistExceptionAlias|PaymentModuleDoesNotExistException $exception) {
            
            return $response->withStatus(404);
        }
    }
    
    
    /**
     * Trims values and removes empty and duplicate values from module array
     *
     * @param array $modules
     *
     * @return array
     */
    private function sanitizeValues(array $modules): array
    {
        foreach ($modules as $key => $module) {
            $modules[$key] = trim($module);
            if ($modules[$key] === '') {
                unset($modules[$key]);
            }
        }
        
        return array_unique($modules);
    }
}