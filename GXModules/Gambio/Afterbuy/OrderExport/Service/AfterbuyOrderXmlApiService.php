<?php
/* --------------------------------------------------------------
   AfterbuyOrderXmlApiService.php 2022-11-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Service;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;

/**
 * Interface AfterbuyOrderXmlApiService
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Service
 */
interface AfterbuyOrderXmlApiService
{
    /**
     * Updates order information for Afterbuy using the XML-API.
     *
     * @param OrderId $orderId
     */
    public function updateOrderViaXmlApi(OrderId $orderId): void;
}