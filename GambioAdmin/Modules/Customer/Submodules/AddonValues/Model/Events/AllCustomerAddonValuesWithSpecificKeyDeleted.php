<?php
/* --------------------------------------------------------------
   AllCustomerAddonValuesWithSpecificKeyDeleted.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueKey;

/**
 * Class AllCustomerAddonValuesWithSpecificKeyDeleted
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\Events
 */
class AllCustomerAddonValuesWithSpecificKeyDeleted
{
    private CustomerAddonValueKey $customerAddonValueKey;
    
    
    /**
     * @param CustomerAddonValueKey $customerAddonValueKey
     */
    private function __construct(CustomerAddonValueKey $customerAddonValueKey)
    {
        $this->customerAddonValueKey = $customerAddonValueKey;
    }
    
    
    /**
     * @param CustomerAddonValueKey $customerAddonValueKey
     *
     * @return AllCustomerAddonValuesWithSpecificKeyDeleted
     */
    public static function create(CustomerAddonValueKey $customerAddonValueKey
    ): AllCustomerAddonValuesWithSpecificKeyDeleted {
        return new self($customerAddonValueKey);
    }
    
    
    /**
     * @return CustomerAddonValueKey
     */
    public function customerAddonValueKey(): CustomerAddonValueKey
    {
        return $this->customerAddonValueKey;
    }
}