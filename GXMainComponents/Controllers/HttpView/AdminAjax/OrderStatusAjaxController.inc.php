<?php
/* --------------------------------------------------------------
 OrderStatusAjaxController.inc.php 2018-01-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

MainFactory::load_class('AdminHttpViewController');

/**
 * Class OrderStatusAjaxController
 */
class OrderStatusAjaxController extends AdminHttpViewController
{
    /**
     * @var \OrderStatusService
     */
    protected $orderStatusService;
    
    
    /**
     * Initialize the order status ajax controller.
     */
    public function init()
    {
        $this->orderStatusService = StaticGXCoreLoader::getService('OrderStatus');
    }
    
    
    /**
     * Returns a json with all order status resources, their background color and font color code.
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        $statuses = $this->orderStatusService->findAll();
        $data     = [];
        
        foreach ($statuses->getArray() as $status) {
            $data[] = [
                'id'              => $status->getId(),
                'backgroundColor' => $status->getColor(),
                'color'           => ColorHelper::getLuminance(new StringType($status->getColor()))
                                     > 143 ? '000000' : 'FFFFFF'
            ];
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $data);
    }
}