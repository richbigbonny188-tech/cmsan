<?php
/* --------------------------------------------------------------
   CustomerSettingErrorMessages.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerSettingErrorMessages
 */
class CustomerErrorMessageSettings
{
    /**
     * @var string
     */
    protected $gender;
    
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
    protected $dateOfBirth;
    
    /**
     * @var string
     */
    protected $company;
    
    /**
     * @var string
     */
    protected $vatNumber;
    
    /**
     * @var string
     */
    protected $email;
    
    /**
     * @var string
     */
    protected $emailAddressCheck;
    
    /**
     * @var string
     */
    protected $emailConfirmation;
    
    /**
     * @var string
     */
    protected $emailExists;
    
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
    protected $postcode;
    
    /**
     * @var string
     */
    protected $city;
    
    /**
     * @var string
     */
    protected $country;
    
    /**
     * @var string
     */
    protected $countryZone;
    
    /**
     * @var string
     */
    protected $countryZoneSelection;
    
    /**
     * @var string
     */
    protected $telephoneNumber;
    
    /**
     * @var string
     */
    protected $password;
    
    /**
     * @var string
     */
    protected $passwordMismatch;
    
    /**
     * @var string
     */
    protected $privacy;
    
    /**
     * @var string
     */
    protected $invalidInput;
    
    
    /**
     * CustomerSettingErrorMessages constructor.
     *
     * @param \StringType $gender               Error message for customers gender validation error.
     * @param \StringType $firstName            Error message for customers first name validation error.
     * @param \StringType $lastName             Error message for customers last name validation error.
     * @param \StringType $dateOfBirth          Error message for customers date of birth validation error.
     * @param \StringType $company              Error message for customers company validation error.
     * @param \StringType $vatNumber            Error message for customers vat number validation error.
     * @param \StringType $email                Error message for customers email validation error.
     * @param \StringType $emailAddressCheck    Error message for customers email address check error.
     * @param \StringType $emailConfirmation    Error message for customers email confirmation error.
     * @param \StringType $emailExists          Error message if customers email already exist.
     * @param \StringType $street               Error message for customers street validation error.
     * @param \StringType $houseNumber          Error message for customers house number validation error.
     * @param \StringType $postcode             Error message for customers post code validation error.
     * @param \StringType $city                 Error message for customers city validation error.
     * @param \StringType $country              Error message for customers country validation error.
     * @param \StringType $countryZone          Error message for customers country zone validation error.
     * @param \StringType $countryZoneSelection Error message for customers country zone selection error.
     * @param \StringType $telephoneNumber      Error message for customers telephone number validation error.
     * @param \StringType $password             Error message for customers password validation error.
     * @param \StringType $passwordMismatch     Error message if customers password confirmation mismatch.
     * @param \StringType $privacy              Error message for customers privacy validation error.
     * @param \StringType $invalidInput         Error message for invalid input.
     */
    public function __construct(
        \StringType $gender,
        \StringType $firstName,
        \StringType $lastName,
        \StringType $dateOfBirth,
        \StringType $company,
        \StringType $vatNumber,
        \StringType $email,
        \StringType $emailAddressCheck,
        \StringType $emailConfirmation,
        \StringType $emailExists,
        \StringType $street,
        \StringType $houseNumber,
        \StringType $postcode,
        \StringType $city,
        \StringType $country,
        \StringType $countryZone,
        \StringType $countryZoneSelection,
        \StringType $telephoneNumber,
        \StringType $password,
        \StringType $passwordMismatch,
        \StringType $privacy,
        \StringType $invalidInput
    ) {
        $this->gender               = $gender->asString();
        $this->firstName            = $firstName->asString();
        $this->lastName             = $lastName->asString();
        $this->dateOfBirth          = $dateOfBirth->asString();
        $this->company              = $company->asString();
        $this->vatNumber            = $vatNumber->asString();
        $this->email                = $email->asString();
        $this->emailAddressCheck    = $emailAddressCheck->asString();
        $this->emailConfirmation    = $emailConfirmation->asString();
        $this->emailExists          = $emailExists->asString();
        $this->street               = $street->asString();
        $this->houseNumber          = $houseNumber->asString();
        $this->postcode             = $postcode->asString();
        $this->city                 = $city->asString();
        $this->country              = $country->asString();
        $this->countryZone          = $countryZone->asString();
        $this->countryZoneSelection = $countryZoneSelection->asString();
        $this->telephoneNumber      = $telephoneNumber->asString();
        $this->password             = $password->asString();
        $this->passwordMismatch     = $passwordMismatch->asString();
        $this->privacy              = $privacy->asString();
        $this->invalidInput         = $invalidInput->asString();
    }
    
    
    /**
     * Error message for customers gender validation error.
     *
     * @return string
     */
    public function gender()
    {
        return $this->gender;
    }
    
    
    /**
     * Error message for customers first name validation error.
     *
     * @return string
     */
    public function firstName()
    {
        return $this->firstName;
    }
    
    
    /**
     * Error message for customers last name validation error.
     *
     * @return string
     */
    public function lastName()
    {
        return $this->lastName;
    }
    
    
    /**
     * Error message for customers date of birth validation error.
     *
     * @return string
     */
    public function dateOfBirth()
    {
        return $this->dateOfBirth;
    }
    
    
    /**
     * Error message for customers company validation error.
     *
     * @return string
     */
    public function company()
    {
        return $this->company;
    }
    
    
    /**
     * Error message for customers vat number validation error.
     *
     * @return string
     */
    public function vatNumber()
    {
        return $this->vatNumber;
    }
    
    
    /**
     * Error message for customers email validation error.
     *
     * @return string
     */
    public function email()
    {
        return $this->email;
    }
    
    
    /**
     * Error message for customers email address check error.
     *
     * @return string
     */
    public function emailAddressCheck()
    {
        return $this->emailAddressCheck;
    }
    
    
    /**
     * Error message for customers email confirmation error.
     *
     * @return string
     */
    public function emailConfirmation()
    {
        return $this->emailConfirmation;
    }
    
    
    /**
     * Error message if customers email already exist.
     *
     * @return string
     */
    public function emailExists()
    {
        return $this->emailExists;
    }
    
    
    /**
     * Error message for customers street validation error.
     *
     * @return string
     */
    public function street()
    {
        return $this->street;
    }
    
    
    /**
     * Error message for customers house number validation error.
     *
     * @return string
     */
    public function houseNumber()
    {
        return $this->houseNumber;
    }
    
    
    /**
     * Error message for customers post code validation error.
     *
     * @return string
     */
    public function postcode()
    {
        return $this->postcode;
    }
    
    
    /**
     * Error message for customers city validation error.
     *
     * @return string
     */
    public function city()
    {
        return $this->city;
    }
    
    
    /**
     * Error message for customers country validation error.
     *
     * @return string
     */
    public function country()
    {
        return $this->country;
    }
    
    
    /**
     * Error message for customers country zone validation error.
     *
     * @return string
     */
    public function countryZone()
    {
        return $this->countryZone;
    }
    
    
    /**
     * Error message for customers country zone selection error.
     *
     * @return string
     */
    public function countryZoneSelection()
    {
        return $this->countryZoneSelection;
    }
    
    
    /**
     * Error message for customers telephone number validation error.
     *
     * @return string
     */
    public function telephoneNumber()
    {
        return $this->telephoneNumber;
    }
    
    
    /**
     * Error message for customers password validation error.
     *
     * @return string
     */
    public function password()
    {
        return $this->password;
    }
    
    
    /**
     * Error message if customers password confirmation mismatch.
     *
     * @return string
     */
    public function passwordMismatch()
    {
        return $this->passwordMismatch;
    }
    
    
    /**
     * Error message for customers privacy validation error.
     *
     * @return string
     */
    public function privacy()
    {
        return $this->privacy;
    }
    
    
    /**
     * Error message for invalid input.
     *
     * @return string
     */
    public function invalidInput()
    {
        return $this->invalidInput;
    }
}