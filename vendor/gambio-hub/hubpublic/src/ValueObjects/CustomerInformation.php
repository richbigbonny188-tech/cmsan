<?php
/* --------------------------------------------------------------
   CustomerInformation.php 2017-10-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

/**
 * Class CustomerInformation
 *
 * A CustomerInformation contains the customers number and address,
 * as well as the data of the billing and shipping address.
 *
 * @package HubPublic\ValueObjects
 */
class CustomerInformation
{
    /**
     * Customer number
     *
     * @var string
     */
    private $customerNumber;
    
    /**
     * Customer first name
     *
     * @var string
     */
    private $customerFirstName;
    
    /**
     * Customer last name
     *
     * @var string
     */
    private $customerLastName;
    
    /**
     * Customer title
     *
     * @var string
     */
    private $customerTitle;
    
    /**
     * Customer gender
     *
     * @var string
     */
    private $customerGender;
    
    /**
     * Customer company
     *
     * @var string
     */
    private $customerCompany;
    
    /**
     * Customer address 1
     *
     * @var string
     */
    private $customerAddress1;
    
    /**
     * Customer address 2
     *
     * @var string
     */
    private $customerAddress2;
    
    /**
     * Customer postal code
     *
     * @var string
     */
    private $customerPostalCode;
    
    /**
     * Customer city
     *
     * @var string
     */
    private $customerCity;
    
    /**
     * Customer state
     *
     * @var string
     */
    private $customerState;
    
    /**
     * Customer country
     *
     * @var string
     */
    private $customerCountry;
    
    /**
     * Customer country code
     *
     * @var string
     */
    private $customerCountryCode;
    
    /**
     * Customer date of birth
     *
     * @var string
     */
    private $customerDateOfBirth;
    
    /**
     * Customer phone
     *
     * @var string
     */
    private $customerPhone;
    
    /**
     * Customer fax
     *
     * @var string
     */
    private $customerFax;
    
    /**
     * Customer email
     *
     * @var string
     */
    private $customerEmail;
    
    /**
     * Customer B2B status
     *
     * @var string
     */
    private $customerB2bStatus;
    
    /**
     * Billing first name
     *
     * @var string
     */
    private $billingFirstName;
    
    /**
     * Billing last name
     *
     * @var string
     */
    private $billingLastName;
    
    /**
     * Billing title
     *
     * @var string
     */
    private $billingTitle;
    
    /**
     * Billing gender
     *
     * @var string
     */
    private $billingGender;
    
    /**
     * Billing company
     *
     * @var string
     */
    private $billingCompany;
    
    /**
     * Billing address 1
     *
     * @var string
     */
    private $billingAddress1;
    
    /**
     * Billing address 2
     *
     * @var string
     */
    private $billingAddress2;
    
    /**
     * Billing postal code
     *
     * @var string
     */
    private $billingPostalCode;
    
    /**
     * Billing city
     *
     * @var string
     */
    private $billingCity;
    
    /**
     * Billing state
     *
     * @var string
     */
    private $billingState;
    
    /**
     * Billing country
     *
     * @var string
     */
    private $billingCountry;
    
    /**
     * Billing country code
     *
     * @var string
     */
    private $billingCountryCode;
    
    /**
     * Billing date of birth
     *
     * @var string
     */
    private $billingDateOfBirth;
    
    /**
     * Billing phone
     *
     * @var string
     */
    private $billingPhone;
    
    /**
     * Billing fax
     *
     * @var string
     */
    private $billingFax;
    
    /**
     * Billing email
     *
     * @var string
     */
    private $billingEmail;
    
    /**
     * Shipping first name
     *
     * @var string
     */
    private $shippingFirstName;
    
    /**
     * Shipping last name
     *
     * @var string
     */
    private $shippingLastName;
    
    /**
     * Shipping title
     *
     * @var string
     */
    private $shippingTitle;
    
    /**
     * Shipping gender
     *
     * @var string
     */
    private $shippingGender;
    
    /**
     * Shipping Company
     *
     * @var string
     */
    private $shippingCompany;
    
    /**
     * Shipping address 1
     *
     * @var string
     */
    private $shippingAddress1;
    
    /**
     * Shipping address 2
     *
     * @var string
     */
    private $shippingAddress2;
    
    /**
     * Shipping postal code
     *
     * @var string
     */
    private $shippingPostalCode;
    
    /**
     * Shipping city
     *
     * @var string
     */
    private $shippingCity;
    
    /**
     * Shipping state
     *
     * @var string
     */
    private $shippingState;
    
    /**
     * Shipping country
     *
     * @var string
     */
    private $shippingCountry;
    
    /**
     * Shipping country code
     *
     * @var string
     */
    private $shippingCountryCode;
    
    /**
     * Shipping date of birth
     *
     * @var string
     */
    private $shippingDateOfBirth;
    
    /**
     * Shipping phone
     *
     * @var string
     */
    private $shippingPhone;
    
    /**
     * Shipping fax
     *
     * @var string
     */
    private $shippingFax;
    
    /**
     * Shipping email
     *
     * @var string
     */
    private $shippingEmail;
    
    
    /**
     * CustomerInformation constructor.
     *
     * @param string $customerNumber      Customer number
     * @param string $customerFirstName   Customer first name
     * @param string $customerLastName    Customer last name
     * @param string $customerTitle       Customer title
     * @param string $customerGender      Customer gender
     * @param string $customerCompany     Customer company
     * @param string $customerAddress1    Customer address 1
     * @param string $customerAddress2    Customer address 2
     * @param string $customerPostalCode  Customer postal code
     * @param string $customerCity        Customer city
     * @param string $customerState       Customer state
     * @param string $customerCountry     Customer country
     * @param string $customerCountryCode Customer country code
     * @param string $customerDateOfBirth Customer date of birth
     * @param string $customerPhone       Customer phone
     * @param string $customerFax         Customer fax
     * @param string $customerEmail       Customer email
     * @param string $customerB2bStatus   Customer B2B status
     * @param string $billingFirstName    Billing first name
     * @param string $billingLastName     Billing last name
     * @param string $billingTitle        Billing title
     * @param string $billingGender       Billing gender
     * @param string $billingCompany      Billing company
     * @param string $billingAddress1     Billing address 1
     * @param string $billingAddress2     Billing address 2
     * @param string $billingPostalCode   Billing postal code
     * @param string $billingCity         Billing city
     * @param string $billingState        Billing state
     * @param string $billingCountry      Billing country
     * @param string $billingCountryCode  Billing country code
     * @param string $billingDateOfBirth  Billing date of birth
     * @param string $billingPhone        Billing phone
     * @param string $billingFax          Billing fax
     * @param string $billingEmail        Billing email
     * @param string $shippingFirstName   Shipping first name
     * @param string $shippingLastName    Shipping last name
     * @param string $shippingTitle       Shipping title
     * @param string $shippingGender      Shipping gender
     * @param string $shippingCompany     Shipping company
     * @param string $shippingAddress1    Shipping address 1
     * @param string $shippingAddress2    Shipping address 2
     * @param string $shippingPostalCode  Shipping postal code
     * @param string $shippingCity        Shipping city
     * @param string $shippingState       Shipping state
     * @param string $shippingCountry     Shipping country
     * @param string $shippingCountryCode Shipping country code
     * @param string $shippingDateOfBirth Shipping date of birth
     * @param string $shippingPhone       Shipping phone
     * @param string $shippingFax         Shipping fax
     * @param string $shippingEmail       Shipping email
     */
    public function __construct(
        string $customerNumber,
        string $customerFirstName,
        string $customerLastName,
        string $customerTitle,
        string $customerGender,
        string $customerCompany,
        string $customerAddress1,
        string $customerAddress2,
        string $customerPostalCode,
        string $customerCity,
        string $customerState,
        string $customerCountry,
        string $customerCountryCode,
        string $customerDateOfBirth,
        string $customerPhone,
        string $customerFax,
        string $customerEmail,
        string $customerB2bStatus,
        string $billingFirstName,
        string $billingLastName,
        string $billingTitle,
        string $billingGender,
        string $billingCompany,
        string $billingAddress1,
        string $billingAddress2,
        string $billingPostalCode,
        string $billingCity,
        string $billingState,
        string $billingCountry,
        string $billingCountryCode,
        string $billingDateOfBirth,
        string $billingPhone,
        string $billingFax,
        string $billingEmail,
        string $shippingFirstName,
        string $shippingLastName,
        string $shippingTitle,
        string $shippingGender,
        string $shippingCompany,
        string $shippingAddress1,
        string $shippingAddress2,
        string $shippingPostalCode,
        string $shippingCity,
        string $shippingState,
        string $shippingCountry,
        string $shippingCountryCode,
        string $shippingDateOfBirth,
        string $shippingPhone,
        string $shippingFax,
        string $shippingEmail
    ) {
        
        $this->customerNumber      = $customerNumber;
        $this->customerFirstName   = $customerFirstName;
        $this->customerLastName    = $customerLastName;
        $this->customerTitle       = $customerTitle;
        $this->customerGender      = $customerGender;
        $this->customerCompany     = $customerCompany;
        $this->customerAddress1    = $customerAddress1;
        $this->customerAddress2    = $customerAddress2;
        $this->customerPostalCode  = $customerPostalCode;
        $this->customerCity        = $customerCity;
        $this->customerState       = $customerState;
        $this->customerCountry     = $customerCountry;
        $this->customerCountryCode = $customerCountryCode;
        $this->customerDateOfBirth = $customerDateOfBirth;
        $this->customerPhone       = $customerPhone;
        $this->customerFax         = $customerFax;
        $this->customerEmail       = $customerEmail;
        $this->customerB2bStatus   = $customerB2bStatus;
        $this->billingFirstName    = $billingFirstName;
        $this->billingLastName     = $billingLastName;
        $this->billingTitle        = $billingTitle;
        $this->billingGender       = $billingGender;
        $this->billingCompany      = $billingCompany;
        $this->billingAddress1     = $billingAddress1;
        $this->billingAddress2     = $billingAddress2;
        $this->billingPostalCode   = $billingPostalCode;
        $this->billingCity         = $billingCity;
        $this->billingState        = $billingState;
        $this->billingCountry      = $billingCountry;
        $this->billingCountryCode  = $billingCountryCode;
        $this->billingDateOfBirth  = $billingDateOfBirth;
        $this->billingPhone        = $billingPhone;
        $this->billingFax          = $billingFax;
        $this->billingEmail        = $billingEmail;
        $this->shippingFirstName   = $shippingFirstName;
        $this->shippingLastName    = $shippingLastName;
        $this->shippingTitle       = $shippingTitle;
        $this->shippingGender      = $shippingGender;
        $this->shippingCompany     = $shippingCompany;
        $this->shippingAddress1    = $shippingAddress1;
        $this->shippingAddress2    = $shippingAddress2;
        $this->shippingPostalCode  = $shippingPostalCode;
        $this->shippingCity        = $shippingCity;
        $this->shippingState       = $shippingState;
        $this->shippingCountry     = $shippingCountry;
        $this->shippingCountryCode = $shippingCountryCode;
        $this->shippingDateOfBirth = $shippingDateOfBirth;
        $this->shippingPhone       = $shippingPhone;
        $this->shippingFax         = $shippingFax;
        $this->shippingEmail       = $shippingEmail;
    }
    
    
    /**
     * Returns the customers number.
     *
     * @return string Customer number
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }
    
    
    /**
     * Returns the customers first name.
     *
     * @return string Customer first name
     */
    public function getCustomerFirstName(): string
    {
        return $this->customerFirstName;
    }
    
    
    /**
     * Returns the customers last name.
     *
     * @return string Customer last name
     */
    public function getCustomerLastName(): string
    {
        return $this->customerLastName;
    }
    
    
    /**
     * Returns the customers title.
     *
     * @return string Customer title
     */
    public function getCustomerTitle(): string
    {
        return $this->customerTitle;
    }
    
    
    /**
     * Returns the customers gender.
     *
     * @return string Customer gender
     */
    public function getCustomerGender(): string
    {
        return $this->customerGender;
    }
    
    
    /**
     * Returns the customers company.
     *
     * @return string Customer company
     */
    public function getCustomerCompany(): string
    {
        return $this->customerCompany;
    }
    
    
    /**
     * Returns the customers first address.
     *
     * @return string Customer address 1
     */
    public function getCustomerAddress1(): string
    {
        return $this->customerAddress1;
    }
    
    
    /**
     * Returns the customers second address.
     *
     * @return string Customer address 2
     */
    public function getCustomerAddress2(): string
    {
        return $this->customerAddress2;
    }
    
    
    /**
     * Returns the customers postal code.
     *
     * @return string Customer postal code
     */
    public function getCustomerPostalCode(): string
    {
        return $this->customerPostalCode;
    }
    
    
    /**
     * Returns the customers city.
     *
     * @return string Customer city
     */
    public function getCustomerCity(): string
    {
        return $this->customerCity;
    }
    
    
    /**
     * Returns the customers state.
     *
     * @return string Customer state
     */
    public function getCustomerState(): string
    {
        return $this->customerState;
    }
    
    
    /**
     * Returns the customers country.
     *
     * @return string Customer country
     */
    public function getCustomerCountry(): string
    {
        return $this->customerCountry;
    }
    
    
    /**
     * Returns the customers country code.
     *
     * @return string Customer country code
     */
    public function getCustomerCountryCode(): string
    {
        return $this->customerCountryCode;
    }
    
    
    /**
     * Returns the customers date of birth.
     *
     * @return string Customer date of birth
     */
    public function getCustomerDateOfBirth(): string
    {
        return $this->customerDateOfBirth;
    }
    
    
    /**
     * Returns the customers phone number.
     *
     * @return string Customer phone
     */
    public function getCustomerPhone(): string
    {
        return $this->customerPhone;
    }
    
    
    /**
     * Returns the customers fax number.
     *
     * @return string Customer fax
     */
    public function getCustomerFax(): string
    {
        return $this->customerFax;
    }
    
    
    /**
     * Returns the customers e-mail address.
     *
     * @return string Customer email
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }
    
    
    /**
     * Returns customer B2B status
     *
     * @return string
     */
    public function getCustomerB2bStatus(): string
    {
        return $this->customerB2bStatus;
    }
    
    
    /**
     * Returns the first name of the billing.
     *
     * @return string Billing first name
     */
    public function getBillingFirstName(): string
    {
        return $this->billingFirstName;
    }
    
    
    /**
     * Returns the last name of the billing.
     *
     * @return string Billing last name
     */
    public function getBillingLastName(): string
    {
        return $this->billingLastName;
    }
    
    
    /**
     * Returns the title of the billing.
     *
     * @return string Billing title
     */
    public function getBillingTitle(): string
    {
        return $this->billingTitle;
    }
    
    
    /**
     * Returns the gender of the billing.
     *
     * @return string Billing gender
     */
    public function getBillingGender(): string
    {
        return $this->billingGender;
    }
    
    
    /**
     * Returns the company of the billing.
     *
     * @return string Billing company
     */
    public function getBillingCompany(): string
    {
        return $this->billingCompany;
    }
    
    
    /**
     * Returns the first address line of the billing.
     *
     * @return string Billing address 1
     */
    public function getBillingAddress1(): string
    {
        return $this->billingAddress1;
    }
    
    
    /**
     * Returns the second address line of the billing.
     *
     * @return string Billing address 2
     */
    public function getBillingAddress2(): string
    {
        return $this->billingAddress2;
    }
    
    
    /**
     * Returns the postal code of the billing.
     *
     * @return string Billing postal code
     */
    public function getBillingPostalCode(): string
    {
        return $this->billingPostalCode;
    }
    
    
    /**
     * Returns the city of the billing.
     *
     * @return string Billing city
     */
    public function getBillingCity(): string
    {
        return $this->billingCity;
    }
    
    
    /**
     * Returns the state of the billing.
     *
     * @return string Billing state
     */
    public function getBillingState(): string
    {
        return $this->billingState;
    }
    
    
    /**
     * Returns the country of the billing.
     *
     * @return string Billing country
     */
    public function getBillingCountry(): string
    {
        return $this->billingCountry;
    }
    
    
    /**
     * Returns the country code of the billing.
     *
     * @return string Billing country code
     */
    public function getBillingCountryCode(): string
    {
        return $this->billingCountryCode;
    }
    
    
    /**
     * Returns the date of birth of the billing.
     *
     * @return string Billing date of birth
     */
    public function getBillingDateOfBirth(): string
    {
        return $this->billingDateOfBirth;
    }
    
    
    /**
     * Returns the phone number of the billing.
     *
     * @return string Billing phone
     */
    public function getBillingPhone(): string
    {
        return $this->billingPhone;
    }
    
    
    /**
     * Returns the fax number of the billing.
     *
     * @return string Billing fax
     */
    public function getBillingFax(): string
    {
        return $this->billingFax;
    }
    
    
    /**
     * Returns the e-mail address of the billing.
     *
     * @return string Billing email
     */
    public function getBillingEmail(): string
    {
        return $this->billingEmail;
    }
    
    
    /**
     * Returns the first name of the shipping.
     *
     * @return string Shipping first name
     */
    public function getShippingFirstName(): string
    {
        return $this->shippingFirstName;
    }
    
    
    /**
     * Returns the last name of the shipping.
     *
     * @return string Shipping last name
     */
    public function getShippingLastName(): string
    {
        return $this->shippingLastName;
    }
    
    
    /**
     * Returns the title of the shipping.
     *
     * @return string Shipping title
     */
    public function getShippingTitle(): string
    {
        return $this->shippingTitle;
    }
    
    
    /**
     * Returns the gender of the shipping.
     *
     * @return string Shipping gender
     */
    public function getShippingGender(): string
    {
        return $this->shippingGender;
    }
    
    
    /**
     * Returns the company of the shipping.
     *
     * @return string Shipping company
     */
    public function getShippingCompany(): string
    {
        return $this->shippingCompany;
    }
    
    
    /**
     * Returns the first address line of the shipping.
     *
     * @return string Shipping address 1
     */
    public function getShippingAddress1(): string
    {
        return $this->shippingAddress1;
    }
    
    
    /**
     * Returns the second address line of the shipping.
     *
     * @return string Shipping address 2
     */
    public function getShippingAddress2(): string
    {
        return $this->shippingAddress2;
    }
    
    
    /**
     * Returns the postal code of the shipping.
     *
     * @return string Shipping postal code
     */
    public function getShippingPostalCode(): string
    {
        return $this->shippingPostalCode;
    }
    
    
    /**
     * Returns the city of the shipping.
     *
     * @return string Shipping city
     */
    public function getShippingCity(): string
    {
        return $this->shippingCity;
    }
    
    
    /**
     * Returns the state of the shipping.
     *
     * @return string Shipping state
     */
    public function getShippingState(): string
    {
        return $this->shippingState;
    }
    
    
    /**
     * Returns the country of the shipping.
     *
     * @return string Shipping country
     */
    public function getShippingCountry(): string
    {
        return $this->shippingCountry;
    }
    
    
    /**
     * Returns the country code of the shipping.
     *
     * @return string Shipping country code
     */
    public function getShippingCountryCode(): string
    {
        return $this->shippingCountryCode;
    }
    
    
    /**
     * Returns the date of birth of the shipping.
     *
     * @return string Shipping date of birth
     */
    public function getShippingDateOfBirth(): string
    {
        return $this->shippingDateOfBirth;
    }
    
    
    /**
     * Returns the phone number of the shipping.
     *
     * @return string Shipping phone
     */
    public function getShippingPhone(): string
    {
        return $this->shippingPhone;
    }
    
    
    /**
     * Returns the fax number of the shipping.
     *
     * @return string Shipping fax
     */
    public function getShippingFax(): string
    {
        return $this->shippingFax;
    }
    
    
    /**
     * Returns the e-mail address of the shipping.
     *
     * @return string Shipping email
     */
    public function getShippingEmail(): string
    {
        return $this->shippingEmail;
    }
}
