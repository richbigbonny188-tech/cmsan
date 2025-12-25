<?php
/* --------------------------------------------------------------
   ParcelServiceService.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceDescriptions;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceIds;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFactory;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceRepository;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceWriteService as ParcelServiceWriteServiceInterface;

/**
 * Class ParcelServiceService
 *
 * @package Gambio\Admin\Modules\ParcelService
 */
class ParcelServiceWriteService implements ParcelServiceWriteServiceInterface
{
    /**
     * @var ParcelServiceRepository
     */
    private $repository;
    
    /**
     * @var ParcelServiceFactory
     */
    private $factory;
    
    
    /**
     * ParcelServiceWriteService constructor.
     *
     * @param ParcelServiceRepository $repository
     * @param ParcelServiceFactory    $factory
     */
    public function __construct(ParcelServiceRepository $repository, ParcelServiceFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
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
        return $this->repository->createParcelService($name, $descriptions, $isDefault, $shipmentType);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleParcelServices(array ...$creationArguments): ParcelServiceIds
    {
        return $this->repository->createMultipleParcelServices(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeParcelServices(ParcelService ...$parcelServices): void
    {
        $this->repository->storeParcelServices(...$parcelServices);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteParcelServices(int ...$ids): void
    {
        $ids = array_map([$this->factory, 'createParcelServiceId'], $ids);
        
        $this->repository->deleteParcelServices(... $ids);
    }
}