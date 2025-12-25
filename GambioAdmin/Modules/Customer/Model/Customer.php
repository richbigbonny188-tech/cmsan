<?php
/*--------------------------------------------------------------
   Customer.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model;

use Gambio\Admin\Modules\Customer\Model\Events\CustomersBusinessInformationUpdated;
use Gambio\Admin\Modules\Customer\Model\Events\CustomersContactInformationUpdated;
use Gambio\Admin\Modules\Customer\Model\Events\CustomersCreditUpdated;
use Gambio\Admin\Modules\Customer\Model\Events\CustomersCustomerGroupUpdated;
use Gambio\Admin\Modules\Customer\Model\Events\CustomersIsFavoriteStateUpdated;
use Gambio\Admin\Modules\Customer\Model\Events\CustomersPersonalInformationUpdated;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerCredit;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;

/**
 * Class Customer
 *
 * @package Gambio\Admin\Modules\Customer\Model
 */
class Customer extends AbstractEventRaisingEntity
{
    private CustomerId          $id;
    private CustomerGroup       $customerGroup;
    private PersonalInformation $personalInformation;
    private BusinessInformation $businessInformation;
    private ContactInformation  $contactInformation;
    private CustomerCredit      $credit;
    private bool                $isGuestAccount;
    private bool                $isFavorite;
    
    
    /**
     * @param CustomerId          $id
     * @param CustomerGroup       $customerGroup
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param CustomerCredit      $credit
     * @param bool                $isGuestAccount
     * @param bool                $isFavorite
     */
    public function __construct(
        CustomerId          $id,
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isGuestAccount,
        bool                $isFavorite
    ) {
        $this->id                  = $id;
        $this->customerGroup       = $customerGroup;
        $this->personalInformation = $personalInformation;
        $this->businessInformation = $businessInformation;
        $this->contactInformation  = $contactInformation;
        $this->credit              = $credit;
        $this->isGuestAccount      = $isGuestAccount;
        $this->isFavorite          = $isFavorite;
    }
    
    
    /**
     * @param CustomerId          $id
     * @param CustomerGroup       $customerGroup
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param CustomerCredit      $credit
     * @param bool                $isGuestAccount
     * @param bool                $isFavorite
     *
     * @return Customer
     */
    public static function create(
        CustomerId          $id,
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isGuestAccount = false,
        bool                $isFavorite = false
    ): Customer {
        return new self($id,
                        $customerGroup,
                        $personalInformation,
                        $businessInformation,
                        $contactInformation,
                        $credit,
                        $isGuestAccount,
                        $isFavorite);
    }
    
    
    /**
     * Return internal data structure as array.
     *
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return [
            "id"                  => $this->id(),
            "customerGroup"       => $this->customerGroup(),
            "isGuestAccount"      => $this->isGuestAccount(),
            "isFavorite"          => $this->isFavorite(),
            "personalInformation" => $this->personalInformation->toArray($datetimeFormat),
            "contactInformation"  => $this->contactInformation->toArray(),
            "businessInformation" => $this->businessInformation->toArray(),
            "credit"              => $this->credit(),
        ];
    }
    
    
    /**
     * Changes customer group of the customer.
     *
     * @param CustomerGroup $customerGroup
     *
     * @return void
     */
    public function changeCustomerGroup(CustomerGroup $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
        $this->raiseEvent(CustomersCustomerGroupUpdated::create($this->id, $customerGroup));
    }
    
    
    /**
     * Changes personal information of the customer.
     *
     * @param PersonalInformation $personalInformation
     *
     * @return void
     */
    public function changePersonalInformation(PersonalInformation $personalInformation): void
    {
        $this->personalInformation = $personalInformation;
        $this->raiseEvent(CustomersPersonalInformationUpdated::create($this->id, $personalInformation));
    }
    
    
    /**
     * Changes business information of the customer.
     *
     * @param BusinessInformation $businessInformation
     *
     * @return void
     */
    public function changeBusinessInformation(BusinessInformation $businessInformation): void
    {
        $this->businessInformation = $businessInformation;
        $this->raiseEvent(CustomersBusinessInformationUpdated::create($this->id, $businessInformation));
    }
    
    
    /**
     * Changes contact information of the customer.
     *
     * @param ContactInformation $contactInformation
     *
     * @return void
     */
    public function changeContactInformation(ContactInformation $contactInformation): void
    {
        $this->contactInformation = $contactInformation;
        $this->raiseEvent(CustomersContactInformationUpdated::create($this->id, $contactInformation));
    }
    
    
    /**
     * Changes credit of the customer.
     *
     * @param CustomerCredit $credit
     *
     * @return void
     */
    public function changeCredit(CustomerCredit $credit): void
    {
        $this->credit = $credit;
        $this->raiseEvent(CustomersCreditUpdated::create($this->id, $credit));
    }
    
    
    /**
     * Changes the is-favorite state of the customer.
     *
     * @param bool $isFavorite
     *
     * @return void
     */
    public function changeIsFavoriteState(bool $isFavorite): void
    {
        $this->isFavorite = $isFavorite;
        $this->raiseEvent(CustomersIsFavoriteStateUpdated::create($this->id, $isFavorite));
    }
    
    
    /**
     * Return the ID of the customer.
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * Return the customer group ID of the customer.
     *
     * @return int
     */
    public function customerGroup(): int
    {
        return $this->customerGroup->id();
    }
    
    
    /**
     * Return the gender of the customer.
     *
     * @return string
     */
    public function gender(): string
    {
        return $this->personalInformation->gender();
    }
    
    
    /**
     * Return the first name of the customer.
     *
     * @return string
     */
    public function firstName(): string
    {
        return $this->personalInformation->firstName();
    }
    
    
    /**
     * Return the last name of the customer.
     *
     * @return string
     */
    public function lastName(): string
    {
        return $this->personalInformation->lastName();
    }
    
    
    /**
     * Return the date of birth of the customer.
     *
     * @param string $datetimeFormat
     *
     * @return string|null
     */
    public function dateOfBirth(string $datetimeFormat = 'Y-m-d H:i:s'): ?string
    {
        return $this->personalInformation->dateOfBirth($datetimeFormat);
    }
    
    
    /**
     * Return the customer number of the customer.
     *
     * @return string
     */
    public function customerNumber(): string
    {
        return $this->personalInformation->customerNumber();
    }
    
    
    /**
     * Return the company name of the customer.
     *
     * @return string
     */
    public function companyName(): string
    {
        return $this->businessInformation->companyName();
    }
    
    
    /**
     * Return the VAT ID of the customer.
     *
     * @return string
     */
    public function vatId(): string
    {
        return $this->businessInformation->vatId();
    }
    
    
    /**
     * Return the status of the VAT ID (valid or not).
     *
     * @return bool
     */
    public function isValidVatId(): bool
    {
        return $this->businessInformation->isValidVatId();
    }
    
    
    /**
     * Return the is-merchant state of the customer.
     *
     * @return bool
     */
    public function isTradesperson(): bool
    {
        return $this->businessInformation->isTradesperson();
    }
    
    
    /**
     * Return the email of the customer.
     *
     * @return string
     */
    public function email(): string
    {
        return $this->contactInformation->email();
    }
    
    
    /**
     * Return the phone number of the customer.
     *
     * @return string
     */
    public function phoneNumber(): string
    {
        return $this->contactInformation->phoneNumber();
    }
    
    
    /**
     * Return the fax number of the customer.
     *
     * @return string
     */
    public function faxNumber(): string
    {
        return $this->contactInformation->faxNumber();
    }
    
    
    /**
     * Return the credit of the customer.
     *
     * @return float
     */
    public function credit(): float
    {
        return $this->credit->value();
    }
    
    
    /**
     * Return the is-guest-account state of the customer.
     *
     * @return bool
     */
    public function isGuestAccount(): bool
    {
        return $this->isGuestAccount;
    }
    
    
    /**
     * Return the is-favorite state of the customer.
     *
     * @return bool
     */
    public function isFavorite(): bool
    {
        return $this->isFavorite;
    }
}