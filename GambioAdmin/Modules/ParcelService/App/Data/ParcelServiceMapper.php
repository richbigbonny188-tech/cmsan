<?php
/* --------------------------------------------------------------
   ParcelServiceMapper.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceIds;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceDescription;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFactory;

/**
 * Class ParcelServiceMapper
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data
 */
class ParcelServiceMapper
{
    /**
     * @var ParcelServiceFactory
     */
    private $factory;
    
    
    /**
     * ParcelServiceMapper constructor.
     *
     * @param ParcelServiceFactory $factory
     */
    public function __construct(ParcelServiceFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $data
     *
     * @return ParcelService
     */
    public function mapParcelService(array $data): ParcelService
    {
        $descriptions = array_map(function (array $data): ParcelServiceDescription {
            return $this->factory->createParcelServiceDescription($data['language_code'],
                                                                  $data['url'],
                                                                  $data['comment']);
        },
            $data['descriptions']);
        
        return $this->factory->createParcelService((int)$data['parcel_service_id'],
                                                   $data['name'],
                                                   $data['default'] === '1',
                                                   $this->factory->createParcelServiceDescriptions(...$descriptions),
                                                   $data['shipment_type']);
    }
    
    
    /**
     * @param array $data
     *
     * @return ParcelServices
     */
    public function mapParcelServices(array $data): ParcelServices
    {
        $parcelServices = array_map([$this, 'mapParcelService'], $data);
        
        return $this->factory->createParcelServices(...$parcelServices);
    }
    
    
    /**
     * @param int $id
     *
     * @return ParcelServiceId
     */
    public function mapParcelServiceId(int $id): ParcelServiceId
    {
        return $this->factory->createParcelServiceId($id);
    }
    
    
    /**
     * @param int ...$ids
     *
     * @return ParcelServiceIds
     */
    public function mapParcelServiceIds(int ...$ids): ParcelServiceIds
    {
        $ids = array_map([$this, 'mapParcelServiceId'], $ids);
        
        return $this->factory->createParcelServiceIds(...$ids);
    }
}