<?php
/*--------------------------------------------------------------
   CustomerAddressMapper.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\Data;

use DateTimeImmutable;
use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddresses;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressCountryTranslationRepository;

/**
 * Class CustomerAddressMapper
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App\Data
 */
class CustomerAddressMapper extends CustomerAddressFactory
{
    private CustomerAddressCountryTranslationRepository $countryTranslationRepository;
    
    
    public function __construct(CustomerAddressCountryTranslationRepository $repository)
    {
        $this->countryTranslationRepository = $repository;
    }
    
    
    /**
     * @param array $addressData
     * @param bool  $isDefaultForShipping
     * @param bool  $isDefaultForPayment
     *
     * @return CustomerAddress
     * @throws Exception
     */
    public function mapCustomerAddress(
        array $addressData,
        bool  $isDefaultForShipping = false,
        bool  $isDefaultForPayment = false
    ): CustomerAddress {
        $addressId    = $this->createCustomerAddressId((int)$addressData['address_book_id']);
        $customerId   = $this->createCustomerId((int)$addressData['customers_id']);
        $personalInfo = $this->createPersonalInformation($addressData['entry_gender'],
                                                         $addressData['entry_firstname'],
                                                         $addressData['entry_lastname'],
                                                         $addressData['entry_company']);
        
        $addressCountry = $this->countryTranslationRepository->getCustomerCountryByIsoCode2($addressData['countries_iso_code_2']);
        $countryName    = $addressCountry ? $addressCountry->name() : $addressData['countries_name'];
        
        $entryZoneId = $addressData['entry_zone_id'] ?? 0;
        $locationInfo  = $this->createLocationInformation($addressData['entry_street_address'],
                                                          $addressData['entry_house_number'],
                                                          $addressData['entry_postcode'],
                                                          $addressData['entry_city'],
                                                          $countryName,
                                                          $addressData['countries_iso_code_2'],
                                                          $addressData['entry_additional_info'],
                                                          $addressData['entry_suburb'] ?? '',
                                                          (int)$entryZoneId,
                                                          $addressData['entry_state'] ?? '');
        $creationTime  = new DateTimeImmutable($addressData['address_date_added']);
        $updatedAtTime = new DateTimeImmutable($addressData['address_last_modified']);
        
        return CustomerAddress::create($addressId,
                                       $customerId,
                                       $personalInfo,
                                       $locationInfo,
                                       $creationTime,
                                       $updatedAtTime,
                                       $isDefaultForShipping,
                                       $isDefaultForPayment);
    }
    
    
    /**
     * @param array $addressesData
     *
     * @return CustomerAddresses
     */
    public function mapCustomerAddresses(array $addressesData): CustomerAddresses
    {
        return CustomerAddresses::create(...array_map([static::class, 'mapCustomerAddress'], $addressesData));
    }
}