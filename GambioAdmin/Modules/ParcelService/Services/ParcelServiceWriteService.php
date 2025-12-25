<?php
/* --------------------------------------------------------------
   ParcelServiceWriteService.php 2021-10-07
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
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Model\ValueObjects\ParcelServiceId;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\CreationOfParcelServicesFailedException;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\DeletionOfParcelServicesFailedException;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\StorageOfParcelServicesFailedException;

/**
 * Interface ParcelServiceWriteService
 *
 * @package Gambio\Admin\Modules\ParcelService\Services
 */
interface ParcelServiceWriteService
{
    /**
     * Creates a new parcel service and returns its ID.
     *
     * @param string                    $name
     * @param ParcelServiceDescriptions $descriptions
     * @param bool                      $isDefault
     * @param string                    $shipmentType
     *
     * @return ParcelServiceId
     *
     * @throws CreationOfParcelServicesFailedException
     */
    public function createParcelService(
        string                    $name,
        ParcelServiceDescriptions $descriptions,
        bool                      $isDefault = false,
        string                    $shipmentType = ''
    ): ParcelServiceId;
    
    
    /**
     * Creates multiple parcel services and returns their IDs.
     *
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return ParcelServiceIds
     *
     * @throws CreationOfParcelServicesFailedException
     */
    public function createMultipleParcelServices(array ...$creationArguments): ParcelServiceIds;
    
    
    /**
     * Stores multiple parcel services.
     *
     * @param ParcelService ...$parcelServices
     *
     * @throws StorageOfParcelServicesFailedException
     */
    public function storeParcelServices(ParcelService ...$parcelServices): void;
    
    
    /**
     * Deletes parcel services based on the given IDs.
     *
     * @param int ...$ids
     *
     * @throws DeletionOfParcelServicesFailedException
     */
    public function deleteParcelServices(int ...$ids): void;
}