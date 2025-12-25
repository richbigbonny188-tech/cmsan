<?php
/* --------------------------------------------------------------
   TrackingCodeRepository.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App\Data;

use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodeIds;
use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Model\Events\TrackingCodeCreated;
use Gambio\Admin\Modules\TrackingCode\Model\Events\TrackingCodeDeleted;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository as TrackingCodeRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class TrackingCodeRepository
 *
 * @package Gambio\Admin\Modules\TrackingCode\App\Data
 */
class TrackingCodeRepository extends AbstractEventDispatchingRepository implements TrackingCodeRepositoryInterface
{
    /**
     * @var TrackingCodeMapper
     */
    private $mapper;
    
    /**
     * @var TrackingCodeReader
     */
    private $reader;
    
    /**
     * @var TrackingCodeWriter
     */
    private $writer;
    
    
    /**
     * TrackingCodeRepository constructor.
     *
     * @param TrackingCodeMapper       $mapper
     * @param TrackingCodeReader       $reader
     * @param TrackingCodeWriter       $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        TrackingCodeMapper       $mapper,
        TrackingCodeReader       $reader,
        TrackingCodeWriter       $writer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterTrackingCodes(Filters $filters, Sorting $sorting, Pagination $pagination): TrackingCodes
    {
        $trackingCodesData = $this->reader->getFilteredTrackingCodesData($filters, $sorting, $pagination);
        
        return $this->mapper->mapTrackingCodes($trackingCodesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTrackingCodesTotalCount(Filters $filters): int
    {
        return $this->reader->getTrackingCodesTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllTrackingCodes(): TrackingCodes
    {
        $trackingCodesData = $this->reader->getAllTrackingCodesData();
        
        return $this->mapper->mapTrackingCodes($trackingCodesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getTrackingCodeById(TrackingCodeId $id): TrackingCode
    {
        $trackingCodeData = $this->reader->getTrackingCodeDataById($id);
        
        return $this->mapper->mapTrackingCode($trackingCodeData);
    }
    
    
    /**
     * @inheritcDoc
     */
    public function getTrackingCodesByOrderId(OrderId $orderId): TrackingCodes
    {
        $trackingCodesData = $this->reader->getTrackingCodesDataByOrderId($orderId);
        
        return $this->mapper->mapTrackingCodes($trackingCodesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createTrackingCode(
        OrderId              $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        bool                 $isReturnDelivery
    ): TrackingCodeId {
        $idValue = $this->writer->createTrackingCode($orderId, $code, $parcelServiceDetails, $isReturnDelivery);
        $id      = $this->mapper->mapTrackingCodeId($idValue);
        
        $this->dispatchEvent(TrackingCodeCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleTrackingCodes(array ...$creationArguments): TrackingCodeIds
    {
        Assert::allIsList($creationArguments, 'Provided arguments need to be a list.');
        Assert::allCount($creationArguments, 4, 'Four arguments needed per creation.');
        
        foreach ($creationArguments as $index => $creationArgument) {
            Assert::integer($creationArgument[0], 'ID must be integer. Index: ' . $index);
            Assert::string($creationArgument[1], 'Date must be string or null. Index: ' . $index);
            Assert::isInstanceOf($creationArgument[2],
                                 ParcelServiceDetails::class,
                                 'Parcel service details need to implement "' . ParcelServiceDetails::class
                                 . '" interface. Index: ' . $index);
            Assert::boolean($creationArgument[3], 'Is-return-delivery state need to be a boolean. Index: ' . $index);
            
            $creationArguments[$index][0] = $this->mapper->mapOrderId($creationArgument[0]);
        }
        
        $idValues = $this->writer->createMultipleTrackingCodes(...$creationArguments);
        $ids      = $this->mapper->mapTrackingCodeIds(...$idValues);
        foreach ($ids as $id) {
            $this->dispatchEvent(TrackingCodeCreated::create($id));
        }
        
        return $ids;
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteTrackingCodes(TrackingCodeId ...$ids): void
    {
        $this->writer->deleteTrackingCodes(...$ids);
        foreach ($ids as $id) {
            $this->dispatchEvent(TrackingCodeDeleted::create($id));
        }
    }
}