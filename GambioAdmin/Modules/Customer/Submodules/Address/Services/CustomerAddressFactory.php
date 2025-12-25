<?php
/*--------------------------------------------------------------
   CustomerAddressFactory.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddresses;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressCountry;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressState;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerGender;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;

/**
 * Class CustomerAddressFactory
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services
 */
class CustomerAddressFactory
{
    /**
     * @param CustomerAddress ...$customerAddresses
     *
     * @return CustomerAddresses
     */
    public function createCustomerAddresses(CustomerAddress ...$customerAddresses): CustomerAddresses
    {
        return CustomerAddresses::create(...$customerAddresses);
    }
    
    
    /**
     * @param CustomerAddressId ...$customerAddressIds
     *
     * @return CustomerAddressIds
     */
    public function createCustomerAddressIds(CustomerAddressId ...$customerAddressIds): CustomerAddressIds
    {
        return CustomerAddressIds::create(...$customerAddressIds);
    }
    
    
    /**
     * @param int $customerAddressId
     *
     * @return CustomerAddressId
     */
    public function createCustomerAddressId(int $customerAddressId): CustomerAddressId
    {
        return CustomerAddressId::create($customerAddressId);
    }
    
    
    /**
     * @param string $gender
     * @param string $firstName
     * @param string $lastName
     * @param string $companyName
     *
     * @return PersonalInformation
     */
    public function createPersonalInformation(
        string $gender,
        string $firstName,
        string $lastName,
        string $companyName = ''
    ): PersonalInformation {
        
        $gender = CustomerGender::create($gender);
        
        return PersonalInformation::create($gender,
                                           $firstName,
                                           $lastName,
                                           $companyName);
    }
    
    
    /**
     * @param string $streetName
     * @param string $houseNumber
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     * @param string $countryIsoCode2
     * @param string $additionalInformation
     * @param string $suburb
     * @param int    $stateId
     * @param string $stateName
     *
     * @return LocationInformation
     */
    public function createLocationInformation(
        string $streetName,
        string $houseNumber,
        string $postcode,
        string $city,
        string $countryName,
        string $countryIsoCode2,
        string $additionalInformation = '',
        string $suburb = '',
        int    $stateId = 0,
        string $stateName = ''
    ): LocationInformation {
        
        return LocationInformation::create($streetName,
                                           $houseNumber,
                                           $postcode,
                                           $city,
                                           $this->createCustomerAddressCountry($countryName, $countryIsoCode2),
                                           $this->createCustomerAddressState($stateId, $stateName),
                                           $additionalInformation,
                                           $suburb);
    }
    
    
    /**
     * @param int $customerId
     *
     * @return CustomerId
     */
    public function createCustomerId(int $customerId): CustomerId
    {
        return CustomerId::create($customerId);
    }
    
    
    /**
     * @param string      $name
     * @param string|null $isoCode2
     *
     * @return CustomerAddressCountry
     */
    public function createCustomerAddressCountry(string $name, ?string $isoCode2 = ''): CustomerAddressCountry
    {
        return CustomerAddressCountry::create($name, $isoCode2);
    }
    
    
    /**
     * @param int    $id
     * @param string $name
     *
     * @return CustomerAddressState
     */
    public function createCustomerAddressState(int $id, string $name): CustomerAddressState
    {
        return CustomerAddressState::create($id, $name);
    }
}