<?php
/* --------------------------------------------------------------
   CustomerAddonValueMapper.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValueIds;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFactory;

/**
 * Class CustomerAddonValueMapper
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\Data
 */
class CustomerAddonValueMapper
{
    private CustomerAddonValueFactory $factory;
    
    
    /**
     * @param CustomerAddonValueFactory $factory
     */
    public function __construct(CustomerAddonValueFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $dbData
     *
     * @return CustomerAddonValue
     */
    public function mapCustomerAddonValue(array $dbData): CustomerAddonValue
    {
        return CustomerAddonValue::create($this->mapCustomerAddonValueId((int)$dbData['container_id'],
                                                                         $dbData['addon_key']),
                                          $dbData['addon_value']);
    }
    
    
    /**
     * @param array $dbData
     *
     * @return CustomerAddonValues
     */
    public function mapCustomerAddonValues(array $dbData): CustomerAddonValues
    {
        $customerAddonValues = array_map([$this, 'mapCustomerAddonValue'], $dbData);
        
        return $this->factory->createCustomerAddonValues(...$customerAddonValues);
    }
    
    
    /**
     * @param int    $customerId
     * @param string $key
     *
     * @return CustomerAddonValueId
     */
    public function mapCustomerAddonValueId(int $customerId, string $key): CustomerAddonValueId
    {
        return $this->factory->createCustomerAddonValueId($customerId, $key);
    }
    
    
    /**
     * @param CustomerAddonValueId ...$ids
     *
     * @return CustomerAddonValueIds
     */
    public function mapCustomerAddonValueIds(CustomerAddonValueId ...$ids): CustomerAddonValueIds
    {
        return $this->factory->createCustomerAddonValueIds(...$ids);
    }
}