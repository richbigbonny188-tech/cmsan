<?php
/* --------------------------------------------------------------
   CustomerAddonValuesValueUpdated.php 2022-09-15
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
 * Class CustomerAddonValuesValueUpdated
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\Events
 */
class CustomerAddonValuesValueUpdated
{
    private CustomerAddonValueId $customerAddonValueId;
    private string               $value;
    
    
    /**
     * @param CustomerAddonValueId $customerAddonValueId
     * @param string               $value
     */
    private function __construct(CustomerAddonValueId $customerAddonValueId, string $value)
    {
        $this->customerAddonValueId = $customerAddonValueId;
        $this->value                = $value;
    }
    
    
    /**
     * @param CustomerAddonValueId $customerAddonValueId
     * @param string               $value
     *
     * @return CustomerAddonValuesValueUpdated
     */
    public static function create(
        CustomerAddonValueId $customerAddonValueId,
        string               $value
    ): CustomerAddonValuesValueUpdated {
        return new self($customerAddonValueId, $value);
    }
    
    
    /**
     * @return CustomerAddonValueId
     */
    public function customerAddonValueId(): CustomerAddonValueId
    {
        return $this->customerAddonValueId;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}