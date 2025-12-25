<?php
/* --------------------------------------------------------------
   CustomerAddonValueCreated.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;

/**
 * Class CustomerAddonValueCreated
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\Events
 */
class CustomerAddonValueCreated
{
    private CustomerAddonValueId $customerAddonValueId;
    
    
    /**
     * @param CustomerAddonValueId $customerAddonValueId
     */
    private function __construct(CustomerAddonValueId $customerAddonValueId)
    {
        $this->customerAddonValueId = $customerAddonValueId;
    }
    
    
    /**
     * @param CustomerAddonValueId $customerAddonValueId
     *
     * @return CustomerAddonValueCreated
     */
    public static function create(CustomerAddonValueId $customerAddonValueId): CustomerAddonValueCreated
    {
        return new self($customerAddonValueId);
    }
    
    
    /**
     * @return CustomerAddonValueId
     */
    public function customerAddonValueId(): CustomerAddonValueId
    {
        return $this->customerAddonValueId;
    }
}