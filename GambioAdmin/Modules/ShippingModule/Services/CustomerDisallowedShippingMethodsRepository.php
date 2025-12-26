<?php
/*--------------------------------------------------------------
   CustomerDisallowedShippingMethodsRepository.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule\Services;

use Gambio\Admin\Modules\ShippingModule\Model\Collections\ShippingMethods;
use Gambio\Admin\Modules\ShippingModule\Model\ValueObjects\ShippingMethodId;
use Gambio\Admin\Modules\ShippingModule\Services\Exceptions\CustomerDoesNotExistException;

/**
 * Interface CustomerDisallowedShippingMethodsRepository
 *
 * @package Gambio\Admin\Modules\ShippingModule\Services
 */
interface CustomerDisallowedShippingMethodsRepository
{
    /**
     * Returns all disallowed shipping methods for a given customer.
     *
     * @param int $customerId
     *
     * @return ShippingMethods
     *
     * @throws CustomerDoesNotExistException
     */
    public function getCustomersDisallowedShippingMethods(int $customerId): ShippingMethods;
    
    
    /**
     * Sets the disallowed shipping methods for a given customer.
     *
     * @param int              $customerId
     * @param ShippingMethodId ...$shippingMethodIds
     *
     * @return void
     *
     * @throws CustomerDoesNotExistException
     */
    public function setDisallowedShippingMethods(int $customerId, ShippingMethodId ...$shippingMethodIds): void;
}