<?php
/* --------------------------------------------------------------
   CustomerMinLengthSettings.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerMinLengthSettings
 */
class CustomerMinLengthSettings
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
    protected $dateOfBirth;
    
    /**
     * @var int
     */
    protected $email;
    
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
    protected $company;
    
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
    protected $telephoneNumber;
    
    /**
     * @var int
     */
    protected $password;
    
    
    /**
     * CustomerMinLengthSettings constructor.
     *
     * @param \IntType $firstName       Min length of customers first name.
     * @param \IntType $lastName        Min length of customers last name.
     * @param \IntType $dateOfBirth     Min length of customers date of birth.
     * @param \IntType $email           Min length of customers email.
     * @param \IntType $street          Min length of customers street.
     * @param \IntType $houseNumber     Min length of customers house number.
     * @param \IntType $company         Min length of customers company.
     * @param \IntType $postcode        Min length of customers post code.
     * @param \IntType $city            Min length of customers city.
     * @param \IntType $countryZone     Min length of customers country zone.
     * @param \IntType $telephoneNumber Min length of customers telephone number.
     * @param \IntType $password        Min length of customers password.
     */
    public function __construct(
        \IntType $firstName,
        \IntType $lastName,
        \IntType $dateOfBirth,
        \IntType $email,
        \IntType $street,
        \IntType $houseNumber,
        \IntType $company,
        \IntType $postcode,
        \IntType $city,
        \IntType $countryZone,
        \IntType $telephoneNumber,
        \IntType $password
    ) {
        $this->firstName       = $firstName->asInt();
        $this->lastName        = $lastName->asInt();
        $this->dateOfBirth     = $dateOfBirth->asInt();
        $this->email           = $email->asInt();
        $this->street          = $street->asInt();
        $this->houseNumber     = $houseNumber->asInt();
        $this->company         = $company->asInt();
        $this->postcode        = $postcode->asInt();
        $this->city            = $city->asInt();
        $this->countryZone     = $countryZone->asInt();
        $this->telephoneNumber = $telephoneNumber->asInt();
        $this->password        = $password->asInt();
    }
    
    
    /**
     * Min length of customers first name.
     *
     * @return int
     */
    public function firstName()
    {
        return $this->firstName;
    }
    
    
    /**
     * Min length of customers last name.
     *
     * @return int
     */
    public function lastName()
    {
        return $this->lastName;
    }
    
    
    /**
     * Min length of customers date of birth.
     *
     * @return int
     */
    public function dateOfBirth()
    {
        return $this->dateOfBirth;
    }
    
    
    /**
     * Min length of customers email.
     *
     * @return int
     */
    public function email()
    {
        return $this->email;
    }
    
    
    /**
     * Min length of customers street.
     *
     * @return int
     */
    public function street()
    {
        return $this->street;
    }
    
    
    /**
     * Min length of customers house number.
     *
     * @return int
     */
    public function houseNumber()
    {
        return $this->houseNumber;
    }
    
    
    /**
     * Min length of customers company.
     *
     * @return int
     */
    public function company()
    {
        return $this->company;
    }
    
    
    /**
     * Min length of customers post code.
     *
     * @return int
     */
    public function postcode()
    {
        return $this->postcode;
    }
    
    
    /**
     * Min length of customers city.
     *
     * @return int
     */
    public function city()
    {
        return $this->city;
    }
    
    
    /**
     * Min length of customers country zone.
     *
     * @return int
     */
    public function countryZone()
    {
        return $this->countryZone;
    }
    
    
    /**
     * Min length of customers telephone number.
     *
     * @return int
     */
    public function telephoneNumber()
    {
        return $this->telephoneNumber;
    }
    
    
    /**
     * Min length of customers password.
     *
     * @return int
     */
    public function password()
    {
        return $this->password;
    }
}