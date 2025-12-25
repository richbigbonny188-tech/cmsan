<?php
/* --------------------------------------------------------------
   OrderAddress.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\Model\Export;

/**
 * Class OrderAddress
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\Model\Export
 */
class OrderAddress
{
    /**
     * @var string
     */
    private string $gender;
    
    
    /**
     * @var string
     */
    private string $firstname;
    
    
    /**
     * @var string
     */
    private string $lastname;
    
    
    /**
     * @var string
     */
    private string $company;
    
    
    /**
     * @var string
     */
    private string $street;
    
    
    /**
     * @var string
     */
    private string $houseNumber;
    
    
    /**
     * @var string
     */
    private string $additionalAddressInfo;
    
    
    /**
     * @var string
     */
    private string $suburb;
    
    
    /**
     * @var string
     */
    private string $postcode;
    
    
    /**
     * @var string
     */
    private string $city;
    
    
    /**
     * @var string
     */
    private string $country;
    
    
    /**
     * @var string|null
     */
    private ?string $countryIsoCode;
    
    
    /**
     * OrderAddress constructor.
     *
     * @param string      $gender
     * @param string      $firstname
     * @param string      $lastname
     * @param string      $company
     * @param string      $street
     * @param string      $houseNumber
     * @param string      $additionalAddressInfo
     * @param string      $suburb
     * @param string      $postcode
     * @param string      $city
     * @param string      $country
     * @param string|null $countryIsoCode
     */
    public function __construct(
        string  $gender,
        string  $firstname,
        string  $lastname,
        string  $company,
        string  $street,
        string  $houseNumber,
        string  $additionalAddressInfo,
        string  $suburb,
        string  $postcode,
        string  $city,
        string  $country,
        ?string $countryIsoCode
    ) {
        $this->gender                = $gender;
        $this->firstname             = $firstname;
        $this->lastname              = $lastname;
        $this->company               = $company;
        $this->street                = $street;
        $this->houseNumber           = $houseNumber;
        $this->additionalAddressInfo = $additionalAddressInfo;
        $this->suburb                = $suburb;
        $this->postcode              = $postcode;
        $this->city                  = $city;
        $this->country               = $country;
        $this->countryIsoCode        = $countryIsoCode;
    }
    
    
    /**
     * Checks if address is equal to other address.
     *
     * @param OrderAddress $other
     *
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->gender === $other->gender
               && $this->firstname === $other->firstname
               && $this->lastname === $other->lastname
               && $this->company === $other->company
               && $this->street === $other->street
               && $this->houseNumber === $other->houseNumber
               && $this->additionalAddressInfo === $other->additionalAddressInfo
               && $this->suburb === $other->suburb
               && $this->postcode === $other->postcode
               && $this->city === $other->city
               && $this->country === $other->country
               && $this->countryIsoCode === $other->countryIsoCode;
    }
    
    
    /**
     * @return string
     */
    public function gender(): string
    {
        return $this->gender;
    }
    
    
    /**
     * @return string
     */
    public function firstname(): string
    {
        return $this->firstname;
    }
    
    
    /**
     * @return string
     */
    public function lastname(): string
    {
        return $this->lastname;
    }
    
    
    /**
     * @return string
     */
    public function company(): string
    {
        return $this->company;
    }
    
    
    /**
     * @return string
     */
    public function street(): string
    {
        return $this->street;
    }
    
    
    /**
     * @return string
     */
    public function houseNumber(): string
    {
        return $this->houseNumber;
    }
    
    
    /**
     * @return string
     */
    public function additionalAddressInfo(): string
    {
        return $this->additionalAddressInfo;
    }
    
    
    /**
     * @return string
     */
    public function suburb(): string
    {
        return $this->suburb;
    }
    
    
    /**
     * @return string
     */
    public function postcode(): string
    {
        return $this->postcode;
    }
    
    
    /**
     * @return string
     */
    public function city(): string
    {
        return $this->city;
    }
    
    
    /**
     * @return string
     */
    public function country(): string
    {
        return $this->country;
    }
    
    
    /**
     * @return string|null
     */
    public function countryIsoCode(): ?string
    {
        return $this->countryIsoCode;
    }
}