<?php
/* --------------------------------------------------------------
   CustomerInputValidatorSettings.inc.php 2018-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerInputValidatorSettingsInterface');

/**
 * Value Object
 *
 * Class CustomerInputValidatorSettings
 *
 * CustomerInputValidatorSettings stores all min length values and error messages for registration form validation
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerInputValidatorSettingsInterface
 */
class CustomerInputValidatorSettings implements CustomerInputValidatorSettingsInterface
{
    /**
     * @var \CustomerConfigurationSettings
     */
    protected $configuration;
    
    /**
     * @var \CustomerDisplaySettings
     */
    protected $display;
    
    /**
     * @var \CustomerMinLengthSettings
     */
    protected $minLength;
    
    /**
     * @var \CustomerMaxLengthSettings
     */
    protected $maxLength;
    
    /**
     * @var \CustomerErrorMessageSettings
     */
    protected $errorMessages;
    
    
    /**
     * Constructor of the class CustomerInputValidatorSettings.
     *
     * @param \CustomerConfigurationSettings $configuration     Hold configuration settings.
     * @param \CustomerDisplaySettings       $displaySettings   Hold display settings.
     * @param \CustomerMinLengthSettings     $minLengthSettings Hold min length configuration settings.
     * @param \CustomerMaxLengthSettings     $maxLengthSettings Hold max length configuration settings.
     * @param \CustomerErrorMessageSettings  $errorMessages     Hold messages for validation errors.
     */
    public function __construct(
        \CustomerConfigurationSettings $configuration,
        \CustomerDisplaySettings $displaySettings,
        \CustomerMinLengthSettings $minLengthSettings,
        \CustomerMaxLengthSettings $maxLengthSettings,
        \CustomerErrorMessageSettings $errorMessages
    ) {
        $this->configuration = $configuration;
        $this->display       = $displaySettings;
        $this->minLength     = $minLengthSettings;
        $this->maxLength     = $maxLengthSettings;
        $this->errorMessages = $errorMessages;
    }
    
    
    /**
     * Returns a city error message.
     *
     * @return string City error message.
     */
    public function getCityErrorMessage()
    {
        return $this->errorMessages->city();
    }
    
    
    /**
     * Returns the minimum required city character length.
     *
     * @return int City Minimum required city character length.
     */
    public function getCityMinLength()
    {
        return $this->minLength->city();
    }
    
    
    /**
     * Returns a company error message.
     *
     * @return string Company error message.
     */
    public function getCompanyErrorMessage()
    {
        return $this->errorMessages->company();
    }
    
    
    /**
     * Returns the minimum required company character length.
     *
     * @return int Minimum required company character length.
     */
    public function getCompanyMinLength()
    {
        return $this->minLength->company();
    }
    
    
    /**
     * Returns a country error message.
     *
     * @return string Country error message.
     */
    public function getCountryErrorMessage()
    {
        return $this->errorMessages->country();
    }
    
    
    /**
     * Returns a country zone error message.
     *
     * @return string Country zone error message.
     */
    public function getCountryZoneErrorMessage()
    {
        return $this->errorMessages->countryZone();
    }
    
    
    /**
     * Returns the minimum required country zone character length.
     *
     * @return int Minimum required country zone character length.
     */
    public function getCountryZoneMinLength()
    {
        return $this->minLength->countryZone();
    }
    
    
    /**
     * Returns a country zone selection error message.
     *
     * @return string Country zone selection error message.
     */
    public function getCountryZoneSelectionErrorMessage()
    {
        return $this->errorMessages->countryZoneSelection();
    }
    
    
    /**
     * Returns a date of birth error message.
     *
     * @return string Date of birth error message.
     */
    public function getDateOfBirthErrorMessage()
    {
        return $this->errorMessages->dateOfBirth();
    }
    
    
    /**
     * Returns the minimum required date of birth character length.
     *
     * @return int Minimum required date of birth character length.
     */
    public function getDateOfBirthMinLength()
    {
        return $this->minLength->dateOfBirth();
    }
    
    
    /**
     * Returns an email address check error message.
     *
     * @return string Email address check error message.
     */
    public function getEmailAddressCheckErrorMessage()
    {
        return $this->errorMessages->emailAddressCheck();
    }
    
    
    /**
     * Returns an email confirmation error message.
     *
     * @return string Email confirmation error message.
     */
    public function getEmailConfirmationErrorMessage()
    {
        return $this->errorMessages->emailConfirmation();
    }
    
    
    /**
     * Returns an email error message.
     *
     * @return string Email error message.
     */
    public function getEmailErrorMessage()
    {
        return $this->errorMessages->email();
    }
    
    
    /**
     * Returns an email exists error message.
     *
     * @return string Email exists error message.
     */
    public function getEmailExistsErrorMessage()
    {
        return $this->errorMessages->emailExists();
    }
    
    
    /**
     * Returns the minimum required email character length.
     *
     * @return int Minimum required email character length.
     */
    public function getEmailMinLength()
    {
        return $this->minLength->email();
    }
    
    
    /**
     * Returns a first name error message.
     *
     * @return string First name error message.
     */
    public function getFirstnameErrorMessage()
    {
        return $this->errorMessages->firstName();
    }
    
    
    /**
     * Returns the minimum required first name character length.
     *
     * @return int Minimum required first name character length.
     */
    public function getFirstnameMinLength()
    {
        return $this->minLength->firstName();
    }
    
    
    /**
     * Returns a gender error message.
     *
     * @return string Gender error message.
     */
    public function getGenderErrorMessage()
    {
        return $this->errorMessages->gender();
    }
    
    
    /**
     * Returns a last name error message.
     *
     * @return string Last name error message.
     */
    public function getLastnameErrorMessage()
    {
        return $this->errorMessages->lastName();
    }
    
    
    /**
     * Returns the minimum required last name character length.
     *
     * @return int Minimum required last name character length.
     */
    public function getLastnameMinLength()
    {
        return $this->minLength->lastName();
    }
    
    
    /**
     * Returns a password error message.
     *
     * @return string Password error message.
     */
    public function getPasswordErrorMessage()
    {
        return $this->errorMessages->password();
    }
    
    
    /**
     * Returns the minimum required password character length.
     *
     * @return int Minimum required password character length.
     */
    public function getPasswordMinLength()
    {
        return $this->minLength->password();
    }
    
    
    /**
     * Returns a password mismatch error message.
     *
     * @return string Password mismatch error message.
     */
    public function getPasswordMismatchErrorMessage()
    {
        return $this->errorMessages->passwordMismatch();
    }
    
    
    /**
     * Returns a post code error message.
     *
     * @return string Post code error message.
     */
    public function getPostcodeErrorMessage()
    {
        return $this->errorMessages->postcode();
    }
    
    
    /**
     * Returns the minimum required post code character length.
     *
     * @return int Minimum required post code character length.
     */
    public function getPostcodeMinLength()
    {
        return $this->minLength->postcode();
    }
    
    
    /**
     * Returns a street error message.
     *
     * @return string Street error message.
     */
    public function getStreetErrorMessage()
    {
        return $this->errorMessages->street();
    }
    
    
    /**
     * Returns a house number error message.
     *
     *
     * @return string house number error message.
     */
    public function getHouseNumberErrorMessage()
    {
        return $this->errorMessages->houseNumber();
    }
    
    
    /**
     * Returns the minimum required street character length.
     *
     * @return int Minimum required street character length.
     */
    public function getStreetMinLength()
    {
        return $this->minLength->street();
    }
    
    
    /**
     * Returns the minimum required house number character length.
     *
     * @return int Minimum required house number character length.
     */
    public function getHouseNumberMinLength()
    {
        return $this->minLength->houseNumber();
    }
    
    
    /**
     * Returns a telephone number error message.
     *
     * @return string Telephone number error message.
     */
    public function getTelephoneNumberErrorMessage()
    {
        return $this->errorMessages->telephoneNumber();
    }
    
    
    /**
     * Returns the minimum required telephone number character length.
     *
     * @return int Minimum required telephone number character length.
     */
    public function getTelephoneNumberMinLength()
    {
        return $this->minLength->telephoneNumber();
    }
    
    
    /**
     * Returns a VAT number error message.
     *
     * @return string VAT number error message.
     */
    public function getVatNumberErrorMessage()
    {
        return $this->errorMessages->vatNumber();
    }
    
    
    /**
     * Retrieves state value of company displaying.
     *
     * @return bool Display company?
     */
    public function getDisplayCompany()
    {
        return $this->display->company();
    }
    
    
    /**
     * Retrieves state value of country displaying.
     *
     * @return bool Display country?
     */
    public function getDisplayCountryZone()
    {
        return $this->display->countryZone();
    }
    
    
    /**
     * Retrieves state value of date of birth displaying.
     *
     * @return bool Display date of birth?
     */
    public function getDisplayDateOfBirth()
    {
        return $this->display->dateOfBirth();
    }
    
    
    /**
     * Retrieves state value of gender displaying.
     *
     * @return bool Display gender?
     */
    public function getDisplayGender()
    {
        return $this->display->gender();
    }
    
    
    /**
     * Retrieves state value of telephone number displaying
     *
     * @return bool Display telephone number?
     */
    public function getDisplayTelephone()
    {
        return $this->display->telephone();
    }
    
    
    /**
     * Retrieves state value of fax displaying
     *
     * @return bool Display fax?
     */
    public function getDisplayFax()
    {
        return $this->display->fax();
    }
    
    
    /**
     * Retrieves state value of suburb displaying
     *
     * @return bool Display suburb?
     */
    public function getDisplaySuburb()
    {
        return $this->display->suburb();
    }
    
    
    /**
     * Returns an invalid input error message.
     *
     * @return string Invalid input error message.
     */
    public function getInvalidInputErrorMessage()
    {
        return $this->errorMessages->invalidInput();
    }
    
    
    /**
     * Returns the maximum required first name character length.
     *
     * @return int Maximum required first name character length.
     */
    public function getFirstnameMaxLength()
    {
        return $this->maxLength->firstName();
    }
    
    
    /**
     * Returns the maximum required last name character length.
     *
     * @return int Maximum required last name character length.
     */
    public function getLastnameMaxLength()
    {
        return $this->maxLength->lastName();
    }
    
    
    /**
     * Returns the maximum required company character length.
     *
     * @return int Maximum required company character length.
     */
    public function getCompanyMaxLength()
    {
        return $this->maxLength->company();
    }
    
    
    /**
     * Returns the maximum required VAT number character length.
     *
     * @return int Maximum required VAT number character length.
     */
    public function getVatNumberMaxLength()
    {
        return $this->maxLength->vatNumber();
    }
    
    
    /**
     * Returns the maximum required street character length.
     *
     * @return int Maximum required street character length.
     */
    public function getStreetMaxLength()
    {
        return $this->maxLength->street();
    }
    
    
    /**
     * Returns the maximum required house number character length.
     *
     * @return int Maximum required house number character length.
     */
    public function getHouseNumberMaxLength()
    {
        return $this->maxLength->houseNumber();
    }
    
    
    /**
     * Returns the maximum required post code character length.
     *
     * @return int Maximum required post code character length.
     */
    public function getPostcodeMaxLength()
    {
        return $this->maxLength->postcode();
    }
    
    
    /**
     * Returns the maximum required city character length.
     *
     * @return int Maximum required city character length.
     */
    public function getCityMaxLength()
    {
        return $this->maxLength->city();
    }
    
    
    /**
     * Returns the maximum required country zone character length.
     *
     * @return int Maximum required country zone character length.
     */
    public function getCountryZoneMaxLength()
    {
        return $this->maxLength->countryZone();
    }
    
    
    /**
     * Returns the maximum required suburb character length.
     *
     * @return int Maximum required suburb character length.
     */
    public function getSuburbMaxLength()
    {
        return $this->maxLength->suburb();
    }
    
    
    /**
     * Returns the maximum required call number character length.
     *
     * @return int Maximum required call number character length.
     */
    public function getCallNumberMaxLength()
    {
        return $this->maxLength->telephoneNumber();
    }
    
    
    /**
     * @return bool
     */
    public function isNamesOptional()
    {
        return $this->configuration->optionalNames();
    }
    
    
    /**
     * @return bool
     */
    public function isGenderMandatory()
    {
        return $this->configuration->genderMandatory();
    }
    
    
    /**
     * Retrieves state value of displaying privacy checkbox
     * @return bool Display privacy checkbox?
     */
    public function getAcceptPrivacy()
    {
        return $this->configuration->acceptPrivacy();
    }
    
    
    /**
     * Returns a privacy not accepted error message.
     * @return string Privacy not accepted error message.
     */
    public function getPrivacyErrorMessage()
    {
        return $this->errorMessages->privacy();
    }
    
    
    /**
     * Returns true if street and house number is split
     * @return bool
     */
    public function isStreetInformationenSplit()
    {
        return $this->configuration->splitStreetInformation();
    }
    
    
    /**
     * Returns true if street and house number is split
     * @return bool
     */
    public function isThirdGenerationTemplate()
    {
        return gm_get_env_info('TEMPLATE_VERSION') >= 3.0;
    }
}