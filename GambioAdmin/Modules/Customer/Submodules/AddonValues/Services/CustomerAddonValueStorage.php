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

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\StorageOfCustomerAddonValueFailedException;

/**
 * Interface CustomerAddonValueStorage
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services
 */
interface CustomerAddonValueStorage
{
    /**
     * Sets a customer addon value based on the given customer ID, key and value.
     * If the customer addon values didn't exist before it will be created.
     *
     * @param int    $customerId
     * @param string $key
     * @param string $value
     *
     * @return void
     *
     * @throws StorageOfCustomerAddonValueFailedException
     */
    public function setValue(int $customerId, string $key, string $value): void;
    
    
    /**
     * Returns a customer addon value based on the given customer ID and key.
     * If the customer addon value does not exist the default value will be returned.
     *
     * @param int    $customerId
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    public function getValue(int $customerId, string $key, string $defaultValue = ''): string;
}