<?php
/* --------------------------------------------------------------
   AllCustomerAddonValuesOfSpecificCustomerDeleted.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerId;

/**
 * Class AllCustomerAddonValuesOfSpecificCustomerDeleted
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\Events
 */
class AllCustomerAddonValuesOfSpecificCustomerDeleted
{
    private CustomerId $customerId;
    
    
    /**
     * @param CustomerId $customerId
     */
    private function __construct(CustomerId $customerId)
    {
        $this->customerId = $customerId;
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return AllCustomerAddonValuesOfSpecificCustomerDeleted
     */
    public static function create(CustomerId $customerId): AllCustomerAddonValuesOfSpecificCustomerDeleted
    {
        return new self($customerId);
    }
    
    
    /**
     * @return CustomerId
     */
    public function customerId(): CustomerId
    {
        return $this->customerId;
    }
}