<?php
/*--------------------------------------------------------------
   CustomerAddressReadService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddresses;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CustomerAddressDoesNotExistException;

/**
 * Interface CustomerAddressReadService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services
 */
interface CustomerAddressReadService
{
    /**
     * Returns the addresses of a customer
     *
     * @param int $customerId
     *
     * @return CustomerAddresses
     * @throws CustomerAddressDoesNotExistException
     */
    public function getAddresses(int $customerId): CustomerAddresses;
}