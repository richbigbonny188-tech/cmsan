<?php
/*--------------------------------------------------------------
   CustomerReadService.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\EmailAddressIsInvalidException;

/**
 * Interface CustomerReadService
 *
 * @package Gambio\Admin\Modules\Customer\Services
 */
interface CustomerReadService
{
    /**
     * Returns all available customers.
     *
     * @return Customers
     */
    public function getCustomers(): Customers;
    
    
    /**
     * Returns all available customers that are guest accounts.
     *
     * @return Customers
     */
    public function getGuestAccounts(): Customers;
    
    
    /**
     * Returns a specific customer based on the given ID.
     *
     * @param int $id
     *
     * @return Customer
     *
     * @throws CustomerDoesNotExistException
     */
    public function getCustomerById(int $id): Customer;
    
    
    /**
     * Checks if an email address is valid in its self
     * or is already taken by another customer
     *
     * @param string $email
     *
     * @return true
     *
     * @throws EmailAddressIsInvalidException
     * @throws CustomerEmailAddressMustBeUniqueException
     */
    public function validateEmailAddress(string $email): bool;
}