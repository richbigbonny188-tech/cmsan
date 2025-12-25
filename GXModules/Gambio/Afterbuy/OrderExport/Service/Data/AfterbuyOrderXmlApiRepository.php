<?php
/* --------------------------------------------------------------
   AfterbuyOrderXmlApiRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Service\Data;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderExport\Model\OrderIds;
use GXModules\Gambio\Afterbuy\OrderExport\Model\Request\Request;

/**
 * Interface AfterbuyOrderXmlApiRepository
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Service
 */
interface AfterbuyOrderXmlApiRepository
{
    /**
     * Sends the request information to the afterbuy.
     *
     * @param Request $request
     * @param OrderId $orderId
     *
     * @throws AfterbuyResponseException
     */
    public function send(Request $request, OrderId $orderId): void;
    
    
    /**
     * Returns the afterbuy request including orders of the given order id's.
     *
     * @param OrderIds $orderIds
     *
     * @return Request
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     */
    public function getRequest(OrderIds $orderIds): Request;
}