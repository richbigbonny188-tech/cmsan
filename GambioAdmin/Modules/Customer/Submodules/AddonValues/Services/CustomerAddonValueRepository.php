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

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValueIds;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueFilters;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Filter\CustomerAddonValueSorting;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueKey;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CreationOfCustomerAddonValueFailedException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueAlreadyExistsException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueDoesNotExistException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\DeletionOfCustomerAddonValueFailedException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\StorageOfCustomerAddonValueFailedException;
use Gambio\Core\Filter\Pagination;

/**
 * Interface CustomerAddonValueRepository
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services
 */
interface CustomerAddonValueRepository
{
    /**
     * Returns a specific customer addon value.
     *
     * @param CustomerId            $customerId
     * @param CustomerAddonValueKey $key
     *
     * @return CustomerAddonValue
     *
     * @throws CustomerAddonValueDoesNotExistException
     */
    public function getCustomerAddonValue(CustomerId $customerId, CustomerAddonValueKey $key): CustomerAddonValue;
    
    
    /**
     * Returns all available customers addon values of a specific customer.
     *
     * @param CustomerId $customerId
     *
     * @return CustomerAddonValues
     */
    public function getCustomerAddonValues(CustomerId $customerId): CustomerAddonValues;
    
    
    /**
     * Returns a filtered and paginated collection of customer addon values based on the given filter and sorting
     * arguments. The filters must be a map, that assigns an attribute its filtering pattern. The sorting must be a
     * comma-separated list of attributes. A `-` can be used to change the order to descend.
     *
     * @param CustomerId                $customerId
     * @param CustomerAddonValueFilters $filters
     * @param CustomerAddonValueSorting $sorting
     * @param Pagination                $pagination
     *
     * @return CustomerAddonValues
     */
    public function filterCustomerAddonValues(
        CustomerId                $customerId,
        CustomerAddonValueFilters $filters,
        CustomerAddonValueSorting $sorting,
        Pagination                $pagination
    ): CustomerAddonValues;
    
    
    /**
     * Returns total count of customer addon values based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it's filtering pattern.
     *
     * @param CustomerId                $customerId
     * @param CustomerAddonValueFilters $filters
     *
     * @return int
     */
    public function getCustomerAddonValuesTotalCount(CustomerId $customerId, CustomerAddonValueFilters $filters): int;
    
    
    /**
     * Creates a new customer addon value and returns its ID.
     *
     * @param CustomerId            $customerId
     * @param CustomerAddonValueKey $key
     * @param string                $value
     *
     * @return CustomerAddonValueId
     *
     * @throws CreationOfCustomerAddonValueFailedException
     * @throws CustomerAddonValueAlreadyExistsException
     */
    public function createCustomerAddonValue(
        CustomerId            $customerId,
        CustomerAddonValueKey $key,
        string                $value
    ): CustomerAddonValueId;
    
    
    /**
     * Creates multiple customer addon values and returns their IDs.
     *
     * @param array ...$creationArguments
     *
     * @return CustomerAddonValueIds
     *
     * @throws CreationOfCustomerAddonValueFailedException
     * @throws CustomerAddonValueAlreadyExistsException
     */
    public function createMultipleCustomerAddonValues(array ...$creationArguments): CustomerAddonValueIds;
    
    
    /**
     * Stores multiple customer addon values.
     *
     * @param CustomerAddonValue ...$customerAddonValues
     *
     * @return void
     *
     * @throws StorageOfCustomerAddonValueFailedException
     */
    public function storeCustomerAddonValues(CustomerAddonValue ...$customerAddonValues): void;
    
    
    /**
     * Deletes customer addon values based on the given customer addon value IDs.
     *
     * @param CustomerAddonValueId ...$customerAddonValueIds
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function deleteCustomerAddonValuesByIds(CustomerAddonValueId ...$customerAddonValueIds): void;
    
    
    /**
     * Deletes all customer addon values based on the given addon value keys.
     *
     * @param CustomerAddonValueKey ...$keys
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function deleteCustomerAddonValuesByKeys(CustomerAddonValueKey ...$keys): void;
    
    
    /**
     * Deletes all customer addon values based on the given customer IDs.
     *
     * @param CustomerId ...$customerIds
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function deleteCustomerAddonValuesByCustomerIds(CustomerId ...$customerIds): void;
}