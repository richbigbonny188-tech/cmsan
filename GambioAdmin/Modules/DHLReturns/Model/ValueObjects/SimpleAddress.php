<?php
/* --------------------------------------------------------------
   SimpleAddress.php 2021-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Model\ValueObjects;

class SimpleAddress
{
    /**
     * @var string
     */
    private $name1;
    /**
     * @var string
     */
    private $streetName;
    /**
     * @var string
     */
    private $houseNumber;
    /**
     * @var string
     */
    private $postCode;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $name2;
    /**
     * @var string
     */
    private $name3;
    /**
     * @var ?Country
     */
    private $country;
    
    
    /**
     * SimpleAddress constructor.
     */
    public function __construct(string $name1, string $streetName, string $houseNumber, string $postCode, string $city)
    {
        $this->name1       = $name1;
        $this->streetName  = $streetName;
        $this->houseNumber = $houseNumber;
        $this->postCode    = $postCode;
        $this->city        = $city;
        $this->name2       = '';
        $this->name3       = '';
        $this->country     = null;
    }
    
    
    /**
     * @return string
     */
    public function getName1(): string
    {
        return $this->name1;
    }
    
    
    /**
     * @param string $name1
     */
    public function setName1(string $name1): void
    {
        $this->name1 = $name1;
    }
    
    
    /**
     * @return string
     */
    public function getStreetName(): string
    {
        return $this->streetName;
    }
    
    
    /**
     * @param string $streetName
     */
    public function setStreetName(string $streetName): void
    {
        $this->streetName = $streetName;
    }
    
    
    /**
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }
    
    
    /**
     * @param string $houseNumber
     */
    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }
    
    
    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }
    
    
    /**
     * @param string $postCode
     */
    public function setPostCode(string $postCode): void
    {
        $this->postCode = $postCode;
    }
    
    
    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
    
    
    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }
    
    
    /**
     * @return string
     */
    public function getName2(): string
    {
        return $this->name2;
    }
    
    
    /**
     * @param string $name2
     */
    public function setName2(string $name2): void
    {
        $this->name2 = $name2;
    }
    
    
    /**
     * @return string
     */
    public function getName3(): string
    {
        return $this->name3;
    }
    
    
    /**
     * @param string $name3
     */
    public function setName3(string $name3): void
    {
        $this->name3 = $name3;
    }
    
    
    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }
    
    
    /**
     * @param Country|null $country
     */
    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }
    
    
}
