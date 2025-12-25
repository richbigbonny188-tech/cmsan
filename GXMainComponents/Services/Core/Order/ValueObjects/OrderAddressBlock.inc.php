<?php

/* --------------------------------------------------------------
   OrderAddressBlock.inc.php 2023-11-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderAddressBlock
 *
 * This class is used by the OrderListItem and InvoiceListItem
 * and includes all required address values for the listings.
 *
 * @category   System
 * @package    Order
 * @subpackage ValueObjects
 */
class OrderAddressBlock
{
    /**
     * @var string
     */
    protected $firstName;
    
    /**
     * @var string
     */
    protected $lastName;
    
    /**
     * @var string
     */
    protected $company;
    
    /**
     * @var string
     */
    protected $street;
    
    /**
     * @var string
     */
    protected $houseNumber;
    
    /**
     * @var string
     */
    protected $additionalAddressInfo;
    
    /**
     * @var String
     */
    protected $postcode;
    
    /**
     * @var string
     */
    protected $city;
    
    /**
     * @var string
     */
    protected $state;
    
    /**
     * @var string
     */
    protected $country;
    
    /**
     * @var string
     */
    protected $countryIsoCode;
    
    /**
     * @var string
     */
    protected $gender;
    
    
    /**
     * OrderAddressBlock constructor.
     *
     * @param StringType      $firstName
     * @param StringType      $lastName
     * @param StringType      $company
     * @param StringType      $street
     * @param StringType      $houseNumber
     * @param StringType      $additionalAddressInfo
     * @param StringType      $postcode
     * @param StringType      $city
     * @param StringType      $state
     * @param StringType      $country
     * @param StringType      $countryIsoCode
     * @param StringType|null $gender
     */
    public function __construct(
        StringType  $firstName,
        StringType  $lastName,
        StringType  $company,
        StringType  $street,
        StringType  $houseNumber,
        StringType  $additionalAddressInfo,
        StringType  $postcode,
        StringType  $city,
        StringType  $state,
        StringType  $country,
        StringType  $countryIsoCode,
        ?StringType $gender
    ) {
        $this->firstName             = $firstName->asString();
        $this->lastName              = $lastName->asString();
        $this->company               = $company->asString();
        $this->street                = $street->asString();
        $this->houseNumber           = $houseNumber->asString();
        $this->additionalAddressInfo = $additionalAddressInfo->asString();
        $this->postcode              = $postcode->asString();
        $this->city                  = $city->asString();
        $this->state                 = $state->asString();
        $this->country               = $country->asString();
        $this->countryIsoCode        = $countryIsoCode->asString();
        $this->gender                = $gender?->asString();
    }
    
    
    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    
    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
    
    
    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }
    
    
    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }
    
    
    /**
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }
    
    
    /**
     * @return string
     */
    public function getAdditionalAddressInfo(): string
    {
        return $this->additionalAddressInfo;
    }
    
    
    /**
     * @return String
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }
    
    
    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
    
    
    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }
    
    
    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
    
    
    /**
     * @return string
     */
    public function getCountryIsoCode(): string
    {
        return $this->countryIsoCode;
    }
    
    
    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }
}