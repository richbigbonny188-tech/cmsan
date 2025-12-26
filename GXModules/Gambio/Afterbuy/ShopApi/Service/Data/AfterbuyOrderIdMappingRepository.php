<?php
/* --------------------------------------------------------------
   AfterbuyOrderIdMappingRepository.php 2022-11-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\Service\Data;

use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdAlreadyMappedException;
use GXModules\Gambio\Afterbuy\ShopApi\Exceptions\AfterbuyOrderIdMappingException;
use GXModules\Gambio\Afterbuy\ShopApi\Model\AfterbuyOrderIdMapping;

/**
 * Interface AfterbuyOrderIdMappingRepository
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\Service
 */
interface AfterbuyOrderIdMappingRepository
{
    /**
     * Parses the Afterbuy-API xml response, creating a `AfterbuyOrderIdMapping` which can be saved
     * through the repository.
     *
     * @param string $xmlResponse
     *
     * @return AfterbuyOrderIdMapping
     * @throws AfterbuyOrderIdMappingException
     * @see AfterbuyOrderIdMapping
     */
    public function getAfterbuyOrderIdMapping(string $xmlResponse): AfterbuyOrderIdMapping;
    
    
    /**
     * Saves the Afterbuy order id mapping, so it is possible to associate afterbuy order ids
     * with shop order ids later on.
     *
     * @param AfterbuyOrderIdMapping $orderIdMapping
     * @param string                 $xmlResponse
     *
     * @throws AfterbuyOrderIdMappingException In case of general errors, like unavailable database.
     * @throws AfterbuyOrderIdAlreadyMappedException In case that the Afterbuy- and Shop order ids are already
     *                                                     mapped.
     */
    public function saveAfterbuyOrderIdMapping(AfterbuyOrderIdMapping $orderIdMapping, string $xmlResponse): void;
}