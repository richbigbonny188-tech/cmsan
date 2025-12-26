<?php
/* --------------------------------------------------------------
   AfterbuyOrderShippingAddressMapper.php 2023-02-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\CountryCodesMapper;
use GXModules\Gambio\Afterbuy\OrderExport\Model\Request\ShippingAddress;

/**
 * Class AfterbuyOrderShippingAddressMapper
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderShippingAddressMapper
{
    private const FIELD_DELIVERY_FIRSTNAME          = 'delivery_firstname';
    private const FIELD_DELIVERY_LASTNAME           = 'delivery_lastname';
    private const FIELD_DELIVERY_POSTCODE           = 'delivery_postcode';
    private const FIELD_DELIVERY_CITY               = 'delivery_city';
    private const FIELD_DELIVERY_COUNTRY_ISO_CODE_2 = 'delivery_country_iso_code_2';
    private const FIELD_STREET_ADDRESS              = 'delivery_street_address';
    private const FIELD_HOUSE_NUMBER                = 'delivery_house_number';
    private const FIELD_ADDITIONAL_INFO             = 'delivery_additional_info';
    private const FIELD_TELEPHONE                   = 'customers_telephone';
    private const FIELD_SUBURB                      = 'delivery_suburb';
    private const FIELD_COMPANY                     = 'delivery_company';
    
    
    /**
     * Maps fields for afterbuy order shipping address.
     *
     * @param array $data
     *
     * @return ShippingAddress
     */
    public function map(array $data): ShippingAddress
    {
        $firstname  = $this->prepare($data, self::FIELD_DELIVERY_FIRSTNAME);
        $lastname   = $this->prepare($data, self::FIELD_DELIVERY_LASTNAME);
        $postalCode = $this->prepare($data, self::FIELD_DELIVERY_POSTCODE);
        $city       = $this->prepare($data, self::FIELD_DELIVERY_CITY);
        $country    = $this->prepare($data, self::FIELD_DELIVERY_COUNTRY_ISO_CODE_2);
        $country    = CountryCodesMapper::getLicensePlateCodeForIso2Code($country);
        
        $street          = $this->getStreetAddress($data);
        $stateOfProvince = $this->getStateOfProvince($data);
        $company         = $this->getCompany($data);
        $street2         = $this->getAdditionalStreetInfo($data);
        $phone           = $this->getPhone($data);
        
        return new ShippingAddress($firstname,
                                   $lastname,
                                   $street,
                                   $postalCode,
                                   $city,
                                   $country,
                                   $stateOfProvince,
                                   $company,
                                   $street2,
                                   $phone);
    }
    
    
    /**
     * Returns order shipping address street address.
     *
     * @param array $data
     *
     * @return string
     */
    private function getStreetAddress(array $data): string
    {
        $streetAddress = $this->prepareOrNull($data, self::FIELD_STREET_ADDRESS);
        $houseNumber   = $this->prepareOrNull($data, self::FIELD_HOUSE_NUMBER);
        if ($houseNumber !== '') {
            $streetAddress .= " $houseNumber";
        }
        
        return $streetAddress;
    }
    
    
    /**
     * Returns order shipping address additional street info.
     *
     * @param $data
     *
     * @return string|null
     */
    private function getAdditionalStreetInfo($data): ?string
    {
        return $this->prepareOrNull($data, self::FIELD_ADDITIONAL_INFO);
    }
    
    
    /**
     * Returns order shipping address phone.
     *
     * @param $data
     *
     * @return string|null
     */
    private function getPhone($data): ?string
    {
        return $this->prepareOrNull($data, self::FIELD_TELEPHONE);
    }
    
    
    /**
     * Returns order shipping address state of province.
     *
     * @param $data
     *
     * @return string|null
     */
    private function getStateOfProvince($data): ?string
    {
        return $this->prepareOrNull($data, self::FIELD_SUBURB);
    }
    
    
    /**
     * Returns order shipping address company.
     *
     * @param $data
     *
     * @return string|null
     */
    private function getCompany($data): ?string
    {
        return $this->prepareOrNull($data, self::FIELD_COMPANY);
    }
    
    
    /**
     * Checks if prepared value is empty and returns null if so.
     *
     * @param array  $data
     * @param string $key
     *
     * @return string|null
     */
    private function prepareOrNull(array $data, string $key): ?string
    {
        $value = $this->prepare($data, $key);
        if ($value === '') {
            return null;
        }
        
        return $value;
    }
    
    
    /**
     * Prepares $data[$key] by casting the value to a string and trim the value.
     *
     * @param array  $data
     * @param string $key
     *
     * @return string
     */
    public function prepare(array $data, string $key): string
    {
        $value = $data[$key] ?? '';
        if (!is_string($value)) {
            $value = (string)$value;
        }
        
        return trim($value);
    }
}
