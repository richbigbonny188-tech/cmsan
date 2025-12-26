<?php
/* --------------------------------------------------------------
   CustomerAddonValue.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events\CustomerAddonValuesValueUpdated;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class CustomerAddonValue
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model
 */
class CustomerAddonValue extends AbstractEventRaisingEntity
{
    private CustomerAddonValueId $id;
    private string               $value;
    
    
    /**
     * @param CustomerAddonValueId $id
     * @param string               $value
     */
    private function __construct(CustomerAddonValueId $id, string $value)
    {
        $this->id    = $id;
        $this->value = $value;
    }
    
    
    /**
     * Creates and returns a customer addon value.
     *
     * @param CustomerAddonValueId $id
     * @param string               $value
     *
     * @return CustomerAddonValue
     */
    public static function create(CustomerAddonValueId $id, string $value): CustomerAddonValue
    {
        return new self($id, $value);
    }
    
    
    /**
     * Return the customer ID.
     *
     * @return int
     */
    public function customerId(): int
    {
        return $this->id->customerId();
    }
    
    
    /**
     * Returns the addon value key.
     *
     * @return string
     */
    public function key(): string
    {
        return $this->id->key();
    }
    
    
    /**
     * Returns the value of this customer addon value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * Changes the value of this customer addon value.
     *
     * @param string $value
     *
     * @return void
     */
    public function changeValue(string $value): void
    {
        $this->value = $value;
        
        $this->raiseEvent(CustomerAddonValuesValueUpdated::create($this->id, $value));
    }
    
    
    /**
     * Returns the internal data structure as an array.
     *
     * @return array<integer, string, string>
     */
    public function toArray(): array
    {
        return [
            'customerId' => $this->customerId(),
            'key'        => $this->key(),
            'value'      => $this->value(),
        ];
    }
}