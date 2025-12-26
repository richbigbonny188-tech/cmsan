<?php
/* --------------------------------------------------------------
   ParcelServiceFactory.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Services;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceDescriptions;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServiceIds;
use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceDescription;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;

/**
 * Class ParcelServiceFactory
 *
 * @package Gambio\Admin\Modules\ParcelService\Services
 */
class ParcelServiceFactory
{
    /**
     * Creates and returns a parcel service ID.
     *
     * @param int $id
     *
     * @return ParcelServiceId
     */
    public function createParcelServiceId(int $id): ParcelServiceId
    {
        return ParcelServiceId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of parcel service IDs.
     *
     * @param ParcelServiceId ...$ids
     *
     * @return ParcelServiceIds
     */
    public function createParcelServiceIds(ParcelServiceId ...$ids): ParcelServiceIds
    {
        return ParcelServiceIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns a parcel service.
     *
     * @param int                       $id
     * @param string                    $name
     * @param bool                      $isDefault
     * @param ParcelServiceDescriptions $descriptions
     * @param string                    $shipmentType
     *
     * @return ParcelService
     */
    public function createParcelService(
        int                       $id,
        string                    $name,
        bool                      $isDefault,
        ParcelServiceDescriptions $descriptions,
        string                    $shipmentType
    ): ParcelService {
        return ParcelService::create($this->createParcelServiceId($id),
                                     $name,
                                     $isDefault,
                                     $descriptions,
                                     $shipmentType);
    }
    
    
    /**
     * Creates and returns a collection of parcel services.
     *
     * @param ParcelService ...$parcelServices
     *
     * @return ParcelServices
     */
    public function createParcelServices(ParcelService ...$parcelServices): ParcelServices
    {
        return ParcelServices::create(...$parcelServices);
    }
    
    
    /**
     * Creates and returns a parcel service description.
     *
     * @param string $languageCode
     * @param string $url
     * @param string $comment
     *
     * @return ParcelServiceDescription
     */
    public function createParcelServiceDescription(
        string $languageCode,
        string $url,
        string $comment
    ): ParcelServiceDescription {
        return ParcelServiceDescription::create($languageCode, $url, $comment);
    }
    
    
    /**
     * Creates and returns a collection of parcel service descriptions.
     *
     * @param ParcelServiceDescription ...$parcelServiceDescriptions
     *
     * @return ParcelServiceDescriptions
     */
    public function createParcelServiceDescriptions(ParcelServiceDescription ...$parcelServiceDescriptions
    ): ParcelServiceDescriptions {
        return ParcelServiceDescriptions::create(...$parcelServiceDescriptions);
    }
}