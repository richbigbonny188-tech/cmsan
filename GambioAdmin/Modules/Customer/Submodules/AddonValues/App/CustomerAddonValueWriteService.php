<?php
/* --------------------------------------------------------------
   CustomerAddonValueWriteService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValueIds;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueRepository;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService as CustomerAddonValueWriteServiceInterface;
use Webmozart\Assert\Assert;

/**
 * Class CustomerAddonValueWriteService
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App
 */
class CustomerAddonValueWriteService implements CustomerAddonValueWriteServiceInterface
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
    public function createCustomerAddonValue(int $customerId, string $key, string $value): CustomerAddonValueId
    {
        return $this->repository->createCustomerAddonValue($this->factory->createCustomerId($customerId),
                                                           $this->factory->createCustomerAddonValueKey($key),
                                                           $value);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleCustomerAddonValues(array ...$creationArguments): CustomerAddonValueIds
    {
        Assert::allIsList($creationArguments, 'Provided arguments need to be a list.');
        Assert::allMinCount($creationArguments, 3, 'At least three arguments needed per creation.');
        
        foreach ($creationArguments as $index => [$customerId, $key]) {
            $creationArguments[$index][0] = $this->factory->createCustomerId($customerId);
            $creationArguments[$index][1] = $this->factory->createCustomerAddonValueKey($key);
        }
        
        return $this->repository->createMultipleCustomerAddonValues(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerAddonValues(CustomerAddonValue ...$customerAddonValues): void
    {
        $this->repository->storeCustomerAddonValues(...$customerAddonValues);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddonValuesByIds(array ...$customerAddonValueIds): void
    {
        Assert::allKeyExists($customerAddonValueIds, 'customerId', 'Given arrays need to contain a "customerId" key.');
        Assert::allKeyExists($customerAddonValueIds, 'key', 'Given arrays need to contain a "key" key.');
        
        foreach ($customerAddonValueIds as $index => ['customerId' => $customerId, 'key' => $key]) {
            Assert::integer($customerId, 'Customer ID must be an integer. Got: %s');
            Assert::string($key, 'Key must be a string. Got: %s');
            
            $customerAddonValueIds[$index] = $this->factory->createCustomerAddonValueId($customerId, $key);
        }
        $this->repository->deleteCustomerAddonValuesByIds(...$customerAddonValueIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddonValuesByKeys(string ...$keys): void
    {
        $keys = array_map([$this->factory, 'createCustomerAddonValueKey'], $keys);
        
        $this->repository->deleteCustomerAddonValuesByKeys(...$keys);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddonValuesByCustomerIds(int ...$ids): void
    {
        $ids = array_map([$this->factory, 'createCustomerId'], $ids);
        
        $this->repository->deleteCustomerAddonValuesByCustomerIds(...$ids);
    }
}