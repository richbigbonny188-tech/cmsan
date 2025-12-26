<?php
/*--------------------------------------------------------------
   CustomerFactory.php 2022-07-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerCredit;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGender;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;

/**
 * Class CustomerFactory
 *
 * @package Gambio\Admin\Modules\Customer\Services
 */
class CustomerFactory
{
    /**
     * Creates and returns a collection of customers.
     *
     * @param Customer ...$customers
     *
     * @return Customers
     */
    public function createCustomers(Customer ...$customers): Customers
    {
        return Customers::create(...$customers);
    }
    
    
    /**
     * Creates and returns a collection of customer IDs.
     *
     * @param CustomerId ...$customerIds
     *
     * @return CustomerIds
     */
    public function createCustomerIds(CustomerId ...$customerIds): CustomerIds
    {
        return CustomerIds::create(...$customerIds);
    }
    
    
    /**
     * Creates and returns a customer ID.
     *
     * @param int $customerId
     *
     * @return CustomerId
     */
    public function createCustomerId(int $customerId): CustomerId
    {
        return CustomerId::create($customerId);
    }
    
    
    /**
     * Creates and returns a customer group.
     *
     * @param int $id
     *
     * @return CustomerGroup
     */
    public function createCustomerGroup(int $id): CustomerGroup
    {
        return CustomerGroup::create($id);
    }
    
    
    /**
     * Creates and returns the personal information of a customer.
     *
     * @param string                 $gender
     * @param string                 $firstName
     * @param string                 $lastName
     * @param string                 $customerNumber
     * @param DateTimeImmutable|null $dateOfBirth
     *
     * @return PersonalInformation
     */
    public function createPersonalInformation(
        string             $gender = '',
        string             $firstName = '',
        string             $lastName = '',
        string             $customerNumber = '',
        ?DateTimeImmutable $dateOfBirth = null
    ): PersonalInformation {
        return PersonalInformation::create(CustomerGender::create($gender),
                                           $firstName,
                                           $lastName,
                                           $customerNumber,
                                           $dateOfBirth);
    }
    
    
    /**
     * Creates and returns the business information of a customer.
     *
     * @param string $companyName
     * @param string $vatId
     * @param bool   $isTradesperson
     * @param bool   $isValidVatId
     *
     * @return BusinessInformation
     */
    public function createBusinessInformation(
        string $companyName = '',
        string $vatId = '',
        bool   $isTradesperson = false,
        bool   $isValidVatId = false
    ): BusinessInformation {
        return BusinessInformation::create($companyName, $vatId, $isTradesperson, $isValidVatId);
    }
    
    
    /**
     * Creates and returns the contact information of a customer.
     *
     * @param string $email
     * @param string $phoneNumber
     * @param string $faxNumber
     *
     * @return ContactInformation
     * @throws Exceptions\EmailAddressIsInvalidException
     */
    public function createContactInformation(
        string $email,
        string $phoneNumber = '',
        string $faxNumber = ''
    ): ContactInformation {
        return ContactInformation::create($email, $phoneNumber, $faxNumber);
    }
    
    
    /**
     * Creates and returns a customer credit.
     *
     * @param float $credit
     *
     * @return CustomerCredit
     */
    public function createCredit(float $credit = 0.0): CustomerCredit
    {
        return CustomerCredit::create($credit);
    }
}