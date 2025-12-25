<?php
/* --------------------------------------------------------------
   TrackingCodeService.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App;

use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodeIds;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFactory;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService as TrackingCodeWriteServiceInterface;

/**
 * Class TrackingCodeService
 *
 * @package Gambio\Admin\Modules\TrackingCode
 */
class TrackingCodeWriteService implements TrackingCodeWriteServiceInterface
{
    /**
     * @var TrackingCodeRepository
     */
    private $repository;
    
    /**
     * @var TrackingCodeFactory
     */
    private $factory;
    
    
    /**
     * TrackingCodeWriteService constructor.
     *
     * @param TrackingCodeRepository $repository
     * @param TrackingCodeFactory    $factory
     */
    public function __construct(TrackingCodeRepository $repository, TrackingCodeFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createTrackingCode(
        int                  $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        bool                 $isReturnDelivery
    ): TrackingCodeId {
        return $this->repository->createTrackingCode($this->factory->createOrderId($orderId),
                                                     $code,
                                                     $parcelServiceDetails,
                                                     $isReturnDelivery);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleTrackingCodes(array ...$creationArguments): TrackingCodeIds
    {
        return $this->repository->createMultipleTrackingCodes(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteTrackingCodes(int ...$ids): void
    {
        $ids = array_map([$this->factory, 'createTrackingCodeId'], $ids);
        
        $this->repository->deleteTrackingCodes(... $ids);
    }
}