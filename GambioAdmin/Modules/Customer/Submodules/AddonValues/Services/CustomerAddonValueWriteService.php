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

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValueIds;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\CustomerAddonValue;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\ValueObjects\CustomerAddonValueId;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CreationOfCustomerAddonValueFailedException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueAlreadyExistsException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\DeletionOfCustomerAddonValueFailedException;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\StorageOfCustomerAddonValueFailedException;

/**
 * Interface CustomerAddonValueWriteService
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services
 */
interface CustomerAddonValueWriteService
{
    /**
     * Creates a new customer addon value and returns its ID.
     *
     * @param int    $customerId
     * @param string $key
     * @param string $value
     *
     * @return CustomerAddonValueId
     *
     * @throws CreationOfCustomerAddonValueFailedException
     * @throws CustomerAddonValueAlreadyExistsException
     */
    public function createCustomerAddonValue(int $customerId, string $key, string $value): CustomerAddonValueId;
    
    
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
     * @param array ...$customerAddonValueIds
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function deleteCustomerAddonValuesByIds(array ...$customerAddonValueIds): void;
    
    
    /**
     * Deletes all customer addon values based on the given addon value keys.
     *
     * @param string ...$keys
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function deleteCustomerAddonValuesByKeys(string ...$keys): void;
    
    
    /**
     * Deletes all customer addon values based on the given customer IDs.
     *
     * @param int ...$ids
     *
     * @return void
     *
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function deleteCustomerAddonValuesByCustomerIds(int ...$ids): void;
}