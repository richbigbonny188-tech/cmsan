<?php
/* --------------------------------------------------------------
   CheckPaidStatusService.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderStatus\App;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Data\OrderStatusPaidRepository;
use GXModules\Gambio\Afterbuy\OrderStatus\Model\AfterbuyOrderStatus;
use GXModules\Gambio\Afterbuy\OrderStatus\Service\AfterbuyCheckPaidStatusService;

/**
 * Class CheckPaidStatusService
 *
 * @package GXModules\Gambio\Afterbuy\OrderStatus\App
 */
class CheckPaidStatusService implements AfterbuyCheckPaidStatusService
{
    /**
     * @var OrderStatusPaidRepository
     */
    private OrderStatusPaidRepository $statusPaidRepository;
    
    
    /**
     * CheckPaidStatusService constructor.
     *
     * @param OrderStatusPaidRepository $statusPaidRepository
     */
    public function __construct(OrderStatusPaidRepository $statusPaidRepository)
    {
        $this->statusPaidRepository = $statusPaidRepository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPaidStatus(OrderId $orderId): AfterbuyOrderStatus
    {
        return $this->statusPaidRepository->getAfterbuyPaidStatus($orderId);
    }
}