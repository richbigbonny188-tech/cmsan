<?php
/*--------------------------------------------------------------
   SetDisallowedShippingMethodsAction.php 2022-10-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\App\Actions;

use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsWriteService;
use Gambio\Admin\Modules\ShippingModule\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class SetDisallowedShippingMethodsAction
 *
 * @package Gambio\Admin\Modules\ShippingModule\App\Actions
 */
class SetDisallowedShippingMethodsAction extends AbstractAction
{
    private CustomerDisallowedShippingMethodsWriteService $service;
    
    
    /**
     * @param CustomerDisallowedShippingMethodsWriteService $service
     */
    public function __construct(CustomerDisallowedShippingMethodsWriteService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $body             = $request->getParsedBody();
            $customerId       = (int)$body['customerId'];
            $shippingModuleIds = $body['shippingModuleIds'];
    
            if ($customerId <= 0) {
                return $response->withJson(['error' => 'Invalid customer ID given. Got: ' . $body['customerId']], 400);
            }
    
            $this->service->setDisallowedShippingMethods($customerId, ...$this->sanitizeValues($shippingModuleIds));
            
            return $response->withStatus(204);
        } catch (CustomerDoesNotExistException $exception) {
            
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