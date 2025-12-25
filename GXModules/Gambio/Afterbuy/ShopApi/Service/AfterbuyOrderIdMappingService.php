<?php
/* --------------------------------------------------------------
   AfterbuyOrderIdMappingService.php 2022-11-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\Service;

use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdAlreadyMappedException;
use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdMappingException;

/**
 * Interface AfterbuyOrderIdMappingService
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\Service
 */
interface AfterbuyOrderIdMappingService
{
    /**
     * Parses the Afterbuy (legacy) Shop-API response xml and maps
     * the shop order id to an Afterbuy order id.
     * Saves the mapping in the database table 'afterbuy_orders'.
     *
     * @param string $xmlResponse
     *
     * @throws AfterbuyOrderIdMappingException In case of general errors, like unavailable database.
     * @throws AfterbuyOrderIdAlreadyMappedException In case that the Afterbuy- and Shop order ids are already
     *                                                     mapped.
     */
    public function mapAfterbuyOrderIdToShopOrderId(string $xmlResponse): void;
}