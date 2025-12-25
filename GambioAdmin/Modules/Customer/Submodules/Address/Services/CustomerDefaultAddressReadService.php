<?php
/*--------------------------------------------------------------
   CustomerDefaultAddressReadService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CustomerAddressDoesNotExistException;

/**
 * Interface CustomerDefaultAddressReadService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services
 */
interface CustomerDefaultAddressReadService
{
    /**
     * Returns the default shipping address of a customer
     *
     * @param int $customerId
     *
     * @return CustomerAddress
     * @throws CustomerAddressDoesNotExistException
     */
    public function getDefaultShippingAddress(int $customerId): CustomerAddress;
    
    
    /**
     * Returns the default payment address of a customer
     *
     * @param int $customerId
     *
     * @return CustomerAddress
     * @throws CustomerAddressDoesNotExistException
     */
    public function getDefaultPaymentAddress(int $customerId): CustomerAddress;
}