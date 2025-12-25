<?php
/* --------------------------------------------------------------
   CustomerAddonValueStorage.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueRepository;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueStorage as CustomerAddonValueStorageInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueDoesNotExistException;

/**
 * Class CustomerAddonValueStorage
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App
 */
class CustomerAddonValueStorage implements CustomerAddonValueStorageInterface
{
    private CustomerAddonValueRepository $repository;
    private CustomerAddonValueFactory    $factory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueRepository $repository
     * @param CustomerAddonValueFactory                                                              $factory
     */
    public function __construct(CustomerAddonValueRepository $repository, CustomerAddonValueFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setValue(int $customerId, string $key, string $value): void
    {
        try {
            $addonValue = $this->repository->getCustomerAddonValue($this->factory->createCustomerId($customerId),
                                                                   $this->factory->createCustomerAddonValueKey($key));
            
            $addonValue->changeValue($value);
            $this->repository->storeCustomerAddonValues($addonValue);
        } catch (CustomerAddonValueDoesNotExistException $e) {
            $this->repository->createCustomerAddonValue($this->factory->createCustomerId($customerId),
                                                        $this->factory->createCustomerAddonValueKey($key),
                                                        $value);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getValue(int $customerId, string $key, string $defaultValue = ''): string
    {
        try {
            $addonValue = $this->repository->getCustomerAddonValue($this->factory->createCustomerId($customerId),
                                                                   $this->factory->createCustomerAddonValueKey($key));
            
            return $addonValue->value();
        } catch (CustomerAddonValueDoesNotExistException $e) {
            return $defaultValue;
        }
    }
}