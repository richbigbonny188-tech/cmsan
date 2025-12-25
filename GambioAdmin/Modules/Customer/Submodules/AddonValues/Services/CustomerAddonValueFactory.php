<?php
/* --------------------------------------------------------------
   CustomerAddonValueFactory.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValueIds;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueKey;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerId;

/**
 * Class CustomerAddonValueFactory
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services
 */
class CustomerAddonValueFactory
{
    /**
     * Creates and returns a collection of customer addon values.
     *
     * @param CustomerAddonValue ...$customerAddonValues
     *
     * @return CustomerAddonValues
     */
    public function createCustomerAddonValues(CustomerAddonValue ...$customerAddonValues): CustomerAddonValues
    {
        return CustomerAddonValues::create(...$customerAddonValues);
    }
    
    
    /**
     * Creates and returns a collection of customer addon value IDs.
     *
     * @param CustomerAddonValueId ...$customerAddonValueId
     *
     * @return CustomerAddonValueIds
     */
    public function createCustomerAddonValueIds(CustomerAddonValueId ...$customerAddonValueId): CustomerAddonValueIds
    {
        return CustomerAddonValueIds::create(...$customerAddonValueId);
    }
    
    
    /**
     * Creates and returns a customer addon value ID.
     *
     * @param int    $customerId
     * @param string $key
     *
     * @return CustomerAddonValueId
     */
    public function createCustomerAddonValueId(int $customerId, string $key): CustomerAddonValueId
    {
        return CustomerAddonValueId::create($this->createCustomerId($customerId),
                                            $this->createCustomerAddonValueKey($key));
    }
    
    
    /**
     * Creates and returns a customer addon value key.
     *
     * @param string $key
     *
     * @return CustomerAddonValueKey
     */
    public function createCustomerAddonValueKey(string $key): CustomerAddonValueKey
    {
        return CustomerAddonValueKey::create($key);
    }
    
    
    /**
     * Creates and returns a customer ID.
     *
     * @param int $customerId
     *
     * @return CustomerId
     */
    public function createCustomerId(int $customerId): CustomerId
    {
        return CustomerId::create($customerId);
    }
}