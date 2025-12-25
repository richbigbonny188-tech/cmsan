<?php
/* --------------------------------------------------------------
   OrderXmlApiRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\Data\Sender\AfterbuyRequestSender;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyGlobalRepository;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderRepository;
use GXModules\Gambio\Afterbuy\OrderExport\Model\OrderIds;
use GXModules\Gambio\Afterbuy\OrderExport\Model\Request\Request;
use GXModules\Gambio\Afterbuy\OrderExport\Service\Data\AfterbuyOrderXmlApiRepository;

/**
 * Class OrderXmlApiRepository
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data
 */
class OrderXmlApiRepository implements AfterbuyOrderXmlApiRepository
{
    private const CALL_NAME = 'UpdateSoldItems';
    
    /**
     * @var AfterbuyGlobalRepository
     */
    private AfterbuyGlobalRepository $afterbuyGlobalRepository;
    
    
    /**
     * @var AfterbuyOrderRepository
     */
    private AfterbuyOrderRepository $afterbuyOrderRepository;
    
    
    /**
     * @var AfterbuyRequestSender
     */
    private AfterbuyRequestSender $orderSender;
    
    
    /**
     * AfterbuyOrderRepository constructor.
     *
     * @param AfterbuyGlobalRepository $afterbuyGlobalRepository
     * @param AfterbuyOrderRepository  $afterbuyOrderRepository
     * @param AfterbuyRequestSender    $orderSender
     */
    public function __construct(
        AfterbuyGlobalRepository $afterbuyGlobalRepository,
        AfterbuyOrderRepository  $afterbuyOrderRepository,
        AfterbuyRequestSender    $orderSender
    ) {
        $this->afterbuyGlobalRepository = $afterbuyGlobalRepository;
        $this->afterbuyOrderRepository  = $afterbuyOrderRepository;
        $this->orderSender              = $orderSender;
    }
    
    
    /**
     * @inheritDoc
     */
    public function send(Request $request, OrderId $orderId): void
    {
        $this->orderSender->send($request, $orderId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getRequest(OrderIds $orderIds): Request
    {
        $afterbuyGlobal = $this->afterbuyGlobalRepository->getAfterbuyGlobal(self::CALL_NAME);
        $orders         = $this->afterbuyOrderRepository->getOrders($orderIds);
        
        return new Request($afterbuyGlobal, $orders);
    }
}