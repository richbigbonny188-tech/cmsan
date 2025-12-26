<?php
/* --------------------------------------------------------------
   ActionOrderData.php 2021-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\App\Actions;

use Gambio\Admin\Modules\DHLReturns\App\Data\OrderDataFacade;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

class ActionOrderData extends AbstractAction
{
    /**
     * @var OrderDataFacade
     */
    private $orderDataFacade;
    
    
    public function __construct(OrderDataFacade $orderDataFacade)
    {
    
        $this->orderDataFacade = $orderDataFacade;
    }
    
    public function handle(Request $request, Response $response): Response
    {
        $orderId = (int)$request->getAttribute('orderid');
        $orderData = $this->orderDataFacade->getOrderData($orderId);
        
        return $response->withJson($orderData);
    }
}