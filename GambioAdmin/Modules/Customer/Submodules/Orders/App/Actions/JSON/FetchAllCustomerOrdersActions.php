<?php
/*--------------------------------------------------------------
   FetchAllCustomerOrdersActions.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\App\Actions\JSON;

use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderReadService;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllCustomerOrdersActions
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\App\Actions\JSON
 * @codeCoverageIgnore
 */
class FetchAllCustomerOrdersActions extends AbstractAction
{
    
    private CustomerOrderReadService $service;
    
    
    /**
     * @param CustomerOrderReadService $service
     */
    public function __construct(CustomerOrderReadService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $customerId   = (int)$request->getAttribute('customerId');
        $responseData = $this->service->getCustomerOrders($customerId)->toArray();
    
        return $response->withJson($responseData);
    }
}