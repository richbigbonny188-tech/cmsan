<?php
/* --------------------------------------------------------------
   CustomerAddonValueId.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects;

/**
 * Class CustomerAddonValueId
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Model\ValueObjects
 */
class CustomerAddonValueId
{
    private CustomerId            $customerId;
    private CustomerAddonValueKey $key;
    
    
    /**
     * @param CustomerId            $customerId
     * @param CustomerAddonValueKey $key
     */
    private function __construct(CustomerId $customerId, CustomerAddonValueKey $key)
    {
        $this->customerId = $customerId;
        $this->key        = $key;
    }
    
    
    /**
     * @param CustomerId            $customerId
     * @param CustomerAddonValueKey $key
     *
     * @return CustomerAddonValueId
     */
    public static function create(CustomerId $customerId, CustomerAddonValueKey $key): CustomerAddonValueId
    {
        return new self($customerId, $key);
    }
    
    
    /**
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key->value();
    }
    
    
    /**
     * @return array<integer, string>
     */
    public function toArray(): array
    {
        return [
            'customerId' => $this->customerId(),
            'key'        => $this->key(),
        ];
    }
}