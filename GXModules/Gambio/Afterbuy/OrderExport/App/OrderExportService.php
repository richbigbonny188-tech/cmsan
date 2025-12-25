<?php
/* --------------------------------------------------------------
   OrderExportService.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\OrderExportRepository;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderExportService;

/**
 * Class OrderExportService
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App
 */
class OrderExportService implements AfterbuyOrderExportService
{
    /**
     * @var OrderExportRepository
     */
    private OrderExportRepository $repository;
    
    
    /**
     * OrderExportService constructor.
     *
     * @param OrderExportRepository $repository
     */
    public function __construct(OrderExportRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getInitialExportTimestamp(OrderId $orderId): ?int
    {
        return $this->repository->getInitialExportTimestamp($orderId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function updateLastTrackingSyncTime(): void
    {
        $this->repository->updateLastTrackingSyncTime();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getLastTrackingSyncTime(): string
    {
        return $this->repository->getLastTrackingSyncTime();
    }
}
