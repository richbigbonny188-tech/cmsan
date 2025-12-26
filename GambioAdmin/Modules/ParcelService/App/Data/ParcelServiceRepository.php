<?php
/* --------------------------------------------------------------
   ParcelServiceRepository.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceDescriptions;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceIds;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;
use Gambio\Admin\Modules\ParcelService\Model\Events\ParcelServiceCreated;
use Gambio\Admin\Modules\ParcelService\Model\Events\ParcelServiceDeleted;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceRepository as ParcelServiceRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class ParcelServiceRepository
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data
 */
class ParcelServiceRepository extends AbstractEventDispatchingRepository implements ParcelServiceRepositoryInterface
{
    /**
     * @var ParcelServiceMapper
     */
    private $mapper;
    
    /**
     * @var ParcelServiceReader
     */
    private $reader;
    
    /**
     * @var ParcelServiceWriter
     */
    private $writer;
    
    
    /**
     * ParcelServiceRepository constructor.
     *
     * @param ParcelServiceMapper      $mapper
     * @param ParcelServiceReader      $reader
     * @param ParcelServiceWriter      $writer
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ParcelServiceMapper      $mapper,
        ParcelServiceReader      $reader,
        ParcelServiceWriter      $writer,
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
    public function filterParcelServices(Filters $filters, Sorting $sorting, Pagination $pagination): ParcelServices
    {
        $parcelServicesData = $this->reader->getFilteredParcelServicesData($filters, $sorting, $pagination);
        
        return $this->mapper->mapParcelServices($parcelServicesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getParcelServicesTotalCount(Filters $filters): int
    {
        return $this->reader->getParcelServicesTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAllParcelServices(): ParcelServices
    {
        $parcelServicesData = $this->reader->getAllParcelServicesData();
        
        return $this->mapper->mapParcelServices($parcelServicesData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getParcelServiceById(ParcelServiceId $id): ParcelService
    {
        $parcelServiceData = $this->reader->getParcelServiceDataById($id);
        
        return $this->mapper->mapParcelService($parcelServiceData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createParcelService(
        string                    $name,
        ParcelServiceDescriptions $descriptions,
        bool                      $isDefault = false,
        string                    $shipmentType = ''
    ): ParcelServiceId {
        $idValue = $this->writer->createParcelService($name, $descriptions, $isDefault, $shipmentType);
        $id      = $this->mapper->mapParcelServiceId($idValue);
        
        $this->dispatchEvent(ParcelServiceCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleParcelServices(array ...$creationArguments): ParcelServiceIds
    {
        Assert::allIsList($creationArguments, 'Provided arguments need to be a list.');
        Assert::allMinCount($creationArguments, 2, 'At least two arguments needed per creation.');
        
        foreach ($creationArguments as $index => $creationArgument) {
            Assert::string($creationArgument[0], 'Name must be string. Index: ' . $index);
            Assert::isInstanceOf($creationArgument[1],
                                 ParcelServiceDescriptions::class,
                                 'Descriptions need to implement "' . ParcelServiceDescriptions::class
                                 . '" interface. Index: ' . $index);
            Assert::boolean($creationArgument[2] ?? true, 'Is default flag must be boolean. Index: ' . $index);
            Assert::string($creationArgument[3] ?? '', 'Shipment type must be string. Index: ' . $index);
            
            $creationArguments[$index][2] = $creationArgument[2] ?? false;
            $creationArguments[$index][3] = $creationArgument[3] ?? '';
        }
        
        $idValues = $this->writer->createMultipleParcelServices(...$creationArguments);
        $ids      = $this->mapper->mapParcelServiceIds(...$idValues);
        foreach ($ids as $id) {
            $this->dispatchEvent(ParcelServiceCreated::create($id));
        }
        
        return $ids;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeParcelServices(ParcelService ...$parcelServices): void
    {
        $this->writer->updateParcelServices(...$parcelServices);
        foreach ($parcelServices as $parcelService) {
            $this->dispatchEntityEvents($parcelService);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteParcelServices(ParcelServiceId ...$ids): void
    {
        $this->writer->deleteParcelServices(...$ids);
        foreach ($ids as $id) {
            $this->dispatchEvent(ParcelServiceDeleted::create($id));
        }
    }
}