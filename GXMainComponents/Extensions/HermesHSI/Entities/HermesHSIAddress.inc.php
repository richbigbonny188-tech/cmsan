<?php
/* --------------------------------------------------------------
   HermesHSIAddress.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

class HermesHSIAddress implements JsonSerializable
{
    /** @var string */
    protected $street;
    
    /** @var string */
    protected $houseNumber;
    
    /** @var string */
    protected $zipCode;
    
    /** @var string */
    protected $town;
    
    /** @var string */
    protected $countryCode;
    
    /** @var string */
    protected $addressAddition;
    
    /** @var string */
    protected $addressAddition2;
    
    /** @var string */
    protected $addressAddition3;
    
    
    /**
     * HermesHSIAddress constructor.
     *
     * @param NonEmptyStringType $street
     * @param NonEmptyStringType $houseNumber
     * @param NonEmptyStringType $zipCode
     * @param NonEmptyStringType $town
     *
     * @throws HermesHSIInvalidDataException
     */
    public function __construct(
        NonEmptyStringType $street,
        StringType $houseNumber,
        NonEmptyStringType $zipCode,
        NonEmptyStringType $town
    ) {
        $this->setStreet($street->asString());
        $this->setHouseNumber($houseNumber->asString());
        $this->setZipCode($zipCode->asString());
        $this->setTown($town->asString());
        $this->countryCode      = '';
        $this->addressAddition  = '';
        $this->addressAddition2 = '';
        $this->addressAddition3 = '';
    }
    
    
    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }
    
    
    /**
     * @param string $street
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setStreet(string $street): void
    {
        if (empty($street) || mb_strlen($street) > 27) {
            throw new HermesHSIInvalidDataException('street must be 1 to 27 characters');
        }
        $this->street = $street;
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
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setHouseNumber(string $houseNumber): void
    {
        if (/*empty($houseNumber) ||*/ mb_strlen($houseNumber) > 5) {
            throw new HermesHSIInvalidDataException('houseNumber must be 0 to 5 characters');
        }
        $this->houseNumber = $houseNumber;
    }
    
    
    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }
    
    
    /**
     * @param string $zipCode
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setZipCode(string $zipCode): void
    {
        if (empty($zipCode) || mb_strlen($zipCode) > 8) {
            throw new HermesHSIInvalidDataException('zipCode must be 1 to 8 characters');
        }
        if ($this->countryCode === 'DE' && preg_match('/^\d{5}$/', $zipCode) !== 1) {
            throw new HermesHSIInvalidDataException('zipCode must be numeric and exactly 5 digits for DE');
        }
        $this->zipCode = $zipCode;
    }
    
    
    /**
     * @return string
     */
    public function getTown(): string
    {
        return $this->town;
    }
    
    
    /**
     * @param string $town
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setTown(string $town): void
    {
        if (empty($town) || mb_strlen($town) > 30) {
            throw new HermesHSIInvalidDataException('town must be 1 to 30 characters');
        }
        $this->town = $town;
    }
    
    
    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
    
    
    /**
     * @param string $countryCode
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setCountryCode(string $countryCode): void
    {
        $countryCode = mb_strtoupper($countryCode);
        if (preg_match('/^[A-Z]{2}$/', $countryCode) !== 1) {
            throw new HermesHSIInvalidDataException('countryCode must be 2 letters');
        }
        $this->countryCode = $countryCode;
    }
    
    
    /**
     * @return string
     */
    public function getAddressAddition(): string
    {
        return $this->addressAddition;
    }
    
    
    /**
     * @param string $addressAddition
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setAddressAddition(string $addressAddition): void
    {
        if (mb_strlen($addressAddition) > 20) {
            throw new HermesHSIInvalidDataException('addressAddition cannot be longer than 20 characters');
        }
        $this->addressAddition = $addressAddition;
    }
    
    
    /**
     * @return string
     */
    public function getAddressAddition2(): string
    {
        return $this->addressAddition2;
    }
    
    
    /**
     * @param string $addressAddition2
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setAddressAddition2(string $addressAddition2): void
    {
        if (mb_strlen($addressAddition2) > 20) {
            throw new HermesHSIInvalidDataException('addressAddition2 cannot be longer than 20 characters');
        }
        $this->addressAddition2 = $addressAddition2;
    }
    
    
    /**
     * @return string
     */
    public function getAddressAddition3(): string
    {
        return $this->addressAddition3;
    }
    
    
    /**
     * @param string $addressAddition3
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setAddressAddition3(string $addressAddition3): void
    {
        if (mb_strlen($addressAddition3) > 20) {
            throw new HermesHSIInvalidDataException('addressAddition3 cannot be longer than 20 characters');
        }
        $this->addressAddition3 = $addressAddition3;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'street'           => $this->street,
            'houseNumber'      => $this->houseNumber,
            'zipCode'          => $this->zipCode,
            'town'             => $this->town,
            'countryCode'      => $this->countryCode,
            'addressAddition'  => $this->addressAddition,
            'addressAddition2' => $this->addressAddition2,
            'addressAddition3' => $this->addressAddition3,
        ];
    }
}
