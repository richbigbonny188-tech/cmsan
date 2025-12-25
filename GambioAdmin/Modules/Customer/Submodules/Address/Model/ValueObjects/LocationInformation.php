<?php
/*--------------------------------------------------------------
   LocationInformation.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects;

/**
 * Class LocationInformation
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Address\ValueObjects
 */
class LocationInformation
{
    /**
     * @var string
     */
    private string $streetName;
    
    
    /**
     * @var string
     */
    private string $houseNumber;
    
    
    /**
     * @var string
     */
    private string $postcode;
    
    
    /**
     * @var string
     */
    private string $city;
    
    
    /**
     * @var CustomerAddressCountry
     */
    private CustomerAddressCountry $country;
    
    
    /**
     * @var string
     */
    private string $additionalInformation;
    
    
    /**
     * @var string
     */
    private string $suburb;
    
    
    /**
     * @var CustomerAddressState
     */
    private CustomerAddressState $state;
    
    
    /**
     * @param string                 $streetName
     * @param string                 $houseNumber
     * @param string                 $postcode
     * @param string                 $city
     * @param CustomerAddressCountry $country
     * @param CustomerAddressState   $state
     * @param string                 $additionalInformation
     * @param string                 $suburb
     */
    private function __construct(
        string                 $streetName,
        string                 $houseNumber,
        string                 $postcode,
        string                 $city,
        CustomerAddressCountry $country,
        CustomerAddressState   $state,
        string                 $additionalInformation = '',
        string                 $suburb = ''
    ) {
        $this->streetName            = $streetName;
        $this->houseNumber           = $houseNumber;
        $this->postcode              = $postcode;
        $this->city                  = $city;
        $this->country               = $country;
        $this->additionalInformation = $additionalInformation;
        $this->suburb                = $suburb;
        $this->state                 = $state;
    }
    
    
    /**
     * @param string                 $streetName
     * @param string                 $houseNumber
     * @param string                 $postcode
     * @param string                 $city
     * @param CustomerAddressCountry $country
     * @param CustomerAddressState   $state
     * @param string                 $additionalInformation
     * @param string                 $suburb
     *
     * @return LocationInformation
     */
    public static function create(
        string                 $streetName,
        string                 $houseNumber,
        string                 $postcode,
        string                 $city,
        CustomerAddressCountry $country,
        CustomerAddressState   $state,
        string                 $additionalInformation = '',
        string                 $suburb = ''
    ): LocationInformation {
        
        return new self($streetName, $houseNumber, $postcode, $city, $country, $state, $additionalInformation, $suburb);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'streetName'            => $this->streetName(),
            'houseNumber'           => $this->houseNumber(),
            'postcode'              => $this->postcode(),
            'city'                  => $this->city(),
            'country'               => ['name' => $this->country()->name(), 'isoCode2' => $this->country()->isoCode2()],
            'additionalInformation' => $this->additionalInformation(),
            'suburb'                => $this->suburb(),
            'state'                 => ['id' => $this->stateId(), 'name' => $this->stateName()],
        ];
    }
    
    
    /**
     * @return string
     */
    public function streetName(): string
    {
        return $this->streetName;
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
     * @return CustomerAddressCountry
     */
    public function country(): CustomerAddressCountry
    {
        return $this->country;
    }
    
    
    /**
     * @return string
     */
    public function countryIsoCode2(): string
    {
        return $this->country->isoCode2();
    }
    
    
    /**
     * @return string
     */
    public function additionalInformation(): string
    {
        return $this->additionalInformation;
    }
    
    
    /**
     * @return string
     */
    public function suburb(): string
    {
        return $this->suburb;
    }
    
    
    /**
     * @return CustomerAddressState
     */
    public function state(): CustomerAddressState
    {
        return $this->state;
    }
    
    
    /**
     * @return int
     */
    public function stateId(): int
    {
        return $this->state->id();
    }
    
    
    /**
     * @return string
     */
    public function stateName(): string
    {
        return $this->state->name();
    }
}