<?php
/* --------------------------------------------------------------
   CustomerAddonValueReadService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueDoesNotExistException;

/**
 * Interface CustomerAddonValueReadService
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services
 */
interface CustomerAddonValueReadService
{
    /**
     * Returns a specific customer addon value.
     *
     * @param int    $customerId
     * @param string $key
     *
     * @return CustomerAddonValue
     *
     * @throws CustomerAddonValueDoesNotExistException
     */
    public function getCustomerAddonValue(int $customerId, string $key): CustomerAddonValue;
    
    
    /**
     * Returns all available customers addon values of a specific customer.
     *
     * @param int $customerId
     *
     * @return CustomerAddonValues
     */
    public function getCustomerAddonValues(int $customerId): CustomerAddonValues;
}