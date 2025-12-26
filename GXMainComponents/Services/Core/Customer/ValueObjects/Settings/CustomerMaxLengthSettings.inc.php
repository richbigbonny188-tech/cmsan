<?php
/* --------------------------------------------------------------
   CustomerMaxLengthSettings.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerMaxLengthSettings
 */
class CustomerMaxLengthSettings
{
    /**
     * @var int
     */
    protected $firstName;
    
    /**
     * @var int
     */
    protected $lastName;
    
    /**
     * @var int
     */
    protected $company;
    
    /**
     * @var int
     */
    protected $vatNumber;
    
    /**
     * @var int
     */
    protected $street;
    
    /**
     * @var int
     */
    protected $houseNumber;
    
    /**
     * @var int
     */
    protected $postcode;
    
    /**
     * @var int
     */
    protected $city;
    
    /**
     * @var int
     */
    protected $countryZone;
    
    /**
     * @var int
     */
    protected $suburb;
    
    /**
     * @var int
     */
    protected $telephoneNumber;
    
    
    /**
     * CustomerMaxLengthSettings constructor.
     *
     * @param \IntType $firstName       Max length of customers first name.
     * @param \IntType $lastName        Max length of customers last name.
     * @param \IntType $company         Max length of customers company.
     * @param \IntType $vatNumber       Max length of customers vat number.
     * @param \IntType $street          Max length of customers street.
     * @param \IntType $houseNumber     Max length of customers house number.
     * @param \IntType $postcode        Max length of customers post code.
     * @param \IntType $city            Max length of customers city.
     * @param \IntType $countryZone     Max length of customers country zone.
     * @param \IntType $suburb          Max length of customers suburb.
     * @param \IntType $telephoneNumber Max length of customers telephone number.
     */
    public function __construct(
        \IntType $firstName,
        \IntType $lastName,
        \IntType $company,
        \IntType $vatNumber,
        \IntType $street,
        \IntType $houseNumber,
        \IntType $postcode,
        \IntType $city,
        \IntType $countryZone,
        \IntType $suburb,
        \IntType $telephoneNumber
    ) {
        $this->firstName       = $firstName->asInt();
        $this->lastName        = $lastName->asInt();
        $this->company         = $company->asInt();
        $this->vatNumber       = $vatNumber->asInt();
        $this->street          = $street->asInt();
        $this->houseNumber     = $houseNumber->asInt();
        $this->postcode        = $postcode->asInt();
        $this->city            = $city->asInt();
        $this->countryZone     = $countryZone->asInt();
        $this->suburb          = $suburb->asInt();
        $this->telephoneNumber = $telephoneNumber->asInt();
    }
    
    
    /**
     * Max length of customers first name.
     *
     * @return int
     */
    public function firstName()
    {
        return $this->firstName;
    }
    
    
    /**
     * Max length of customers last name.
     *
     * @return int
     */
    public function lastName()
    {
        return $this->lastName;
    }
    
    
    /**
     * Max length of customers company.
     *
     * @return int
     */
    public function company()
    {
        return $this->company;
    }
    
    
    /**
     * Max length of customers vat number.
     *
     * @return int
     */
    public function vatNumber()
    {
        return $this->vatNumber;
    }
    
    
    /**
     * Max length of customers street.
     *
     * @return int
     */
    public function street()
    {
        return $this->street;
    }
    
    
    /**
     * Max length of customers house number.
     *
     * @return int
     */
    public function houseNumber()
    {
        return $this->houseNumber;
    }
    
    
    /**
     * Max length of customers post code.
     *
     * @return int
     */
    public function postcode()
    {
        return $this->postcode;
    }
    
    
    /**
     * Max length of customers city.
     *
     * @return int
     */
    public function city()
    {
        return $this->city;
    }
    
    
    /**
     * Max length of customers country zone.
     *
     * @return int
     */
    public function countryZone()
    {
        return $this->countryZone;
    }
    
    
    /**
     * Max length of customers suburb.
     *
     * @return int
     */
    public function suburb()
    {
        return $this->suburb;
    }
    
    
    /**
     * Max length of customers telephone number.
     *
     * @return int
     */
    public function telephoneNumber()
    {
        return $this->telephoneNumber;
    }
}