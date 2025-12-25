<?php
/* --------------------------------------------------------------
   ParcelServiceReadService.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Services;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;
use Gambio\Admin\Modules\ParcelService\Model\ParcelService;
use Gambio\Admin\Modules\ParcelService\Services\Exceptions\ParcelServiceNotFoundException;

/**
 * Interface ParcelServiceReadService
 *
 * @package Gambio\Admin\Modules\ParcelService\Services
 */
interface ParcelServiceReadService
{
    /**
     * Returns all available parcel services.
     *
     * @return ParcelServices
     */
    public function getParcelServices(): ParcelServices;
    
    
    /**
     * Returns a specific parcel service based on the given ID.
     *
     * @param int $id
     *
     * @return ParcelService
     *
     * @throws ParcelServiceNotFoundException
     */
    public function getParcelServiceById(int $id): ParcelService;
}