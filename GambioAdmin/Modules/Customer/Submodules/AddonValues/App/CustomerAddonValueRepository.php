<?php
/* --------------------------------------------------------------
   CustomerAddonValueRepository.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\CustomerAddonValueMapper;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\CustomerAddonValueReader;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\Data\CustomerAddonValueWriter;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValueIds;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events\AllCustomerAddonValuesOfSpecificCustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events\AllCustomerAddonValuesWithSpecificKeyDeleted;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events\CustomerAddonValueCreated;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Events\CustomerAddonValueDeleted;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueFilters;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueSorting;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueKey;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueRepository as CustomerAddonValueRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class CustomerAddonValueRepository
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App
 */
class CustomerAddonValueRepository extends AbstractEventDispatchingRepository
    implements CustomerAddonValueRepositoryInterface
{
    private CustomerAddonValueReader $reader;
    private CustomerAddonValueWriter $writer;
    private CustomerAddonValueMapper $mapper;
    
    
    /**
     * @param CustomerAddonValueReader $reader
     * @param CustomerAddonValueWriter $writer
     * @param CustomerAddonValueMapper $mapper
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        CustomerAddonValueReader $reader,
        CustomerAddonValueWriter $writer,
        CustomerAddonValueMapper $mapper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
        
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerAddonValue(CustomerId $customerId, CustomerAddonValueKey $key): CustomerAddonValue
    {
        $dbData = $this->reader->getCustomerAddonValue($customerId, $key);
        
        return $this->mapper->mapCustomerAddonValue($dbData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerAddonValues(CustomerId $customerId): CustomerAddonValues
    {
        $dbData = $this->reader->getCustomerAddonValues($customerId);
        
        return $this->mapper->mapCustomerAddonValues($dbData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCustomerAddonValues(
        CustomerId                $customerId,
        CustomerAddonValueFilters $filters,
        CustomerAddonValueSorting $sorting,
        Pagination                $pagination
    ): CustomerAddonValues {
        $dbData = $this->reader->filterCustomerAddonValues($customerId, $filters, $sorting, $pagination);
        
        return $this->mapper->mapCustomerAddonValues($dbData);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerAddonValuesTotalCount(CustomerId $customerId, Filters $filters): int
    {
        return $this->reader->getCustomerAddonValuesTotalCount($customerId, $filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddonValue(
        CustomerId            $customerId,
        CustomerAddonValueKey $key,
        string                $value
    ): CustomerAddonValueId {
        $this->writer->createCustomerAddonValue($customerId, $key, $value);
        
        $id = $this->mapper->mapCustomerAddonValueId($customerId->value(), $key->value());
        $this->dispatchEvent(CustomerAddonValueCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleCustomerAddonValues(array ...$creationArguments): CustomerAddonValueIds
    {
        Assert::allIsList($creationArguments, 'Provided arguments need to be a list.');
        Assert::allMinCount($creationArguments, 3, 'At least three arguments needed per creation.');
        
        foreach ($creationArguments as [$customerId, $key, $value]) {
            Assert::isInstanceOf($customerId,
                                 CustomerId::class,
                                 'First creation argument must implement ' . CustomerId::class);
            Assert::isInstanceOf($key,
                                 CustomerAddonValueKey::class,
                                 'Second creation argument must implement ' . CustomerId::class);
            Assert::string($value, 'Third creation argument must be a string.');
        }
        
        $this->writer->createCustomerAddonValues(...$creationArguments);
        
        $ids = [];
        foreach ($creationArguments as [$customerId, $key, $value]) {
            $id    = $this->mapper->mapCustomerAddonValueId($customerId->value(), $key->value());
            $ids[] = $id;
            $this->dispatchEvent(CustomerAddonValueCreated::create($id));
        }
        
        return $this->mapper->mapCustomerAddonValueIds(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerAddonValues(CustomerAddonValue ...$customerAddonValues): void
    {
        $this->writer->storeCustomerAddonValues(...$customerAddonValues);
        foreach ($customerAddonValues as $customerAddonValue) {
            $this->dispatchEntityEvents($customerAddonValue);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddonValuesByIds(CustomerAddonValueId ...$customerAddonValueIds): void
    {
        $this->writer->deleteCustomerAddonValuesByIds(...$customerAddonValueIds);
        foreach ($customerAddonValueIds as $customerAddonValueId) {
            $this->dispatchEvent(CustomerAddonValueDeleted::create($customerAddonValueId));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddonValuesByKeys(CustomerAddonValueKey ...$keys): void
    {
        $this->writer->deleteCustomerAddonValuesByKeys(...$keys);
        foreach ($keys as $key) {
            $this->dispatchEvent(AllCustomerAddonValuesWithSpecificKeyDeleted::create($key));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddonValuesByCustomerIds(CustomerId ...$customerIds): void
    {
        $this->writer->deleteCustomerAddonValuesByCustomerIds(...$customerIds);
        foreach ($customerIds as $customerId) {
            $this->dispatchEvent(AllCustomerAddonValuesOfSpecificCustomerDeleted::create($customerId));
        }
    }
}