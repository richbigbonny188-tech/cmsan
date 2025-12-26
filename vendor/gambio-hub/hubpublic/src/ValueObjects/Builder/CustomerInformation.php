<?php
/* --------------------------------------------------------------
   CustomerInformation.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects\Builder;

use HubPublic\ValueObjects\CustomerInformation as CustomerInformationValueObject;

/**
 * Class CustomerInformation
 *
 * A builder for CustomerInformation value objects.
 *
 * @package HubPublic\ValueObjects\Builder
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
    private $customerFirstName = '';
    
    /**
     * Customer last name
     *
     * @var string
     */
    private $customerLastName = '';
    
    /**
     * Customer title
     *
     * @var string
     */
    private $customerTitle = '';
    
    /**
     * Customer gender
     *
     * @var string
     */
    private $customerGender = '';
    
    /**
     * Customer company
     *
     * @var string
     */
    private $customerCompany = '';
    
    /**
     * Customer address 1
     *
     * @var string
     */
    private $customerAddress1 = '';
    
    /**
     * Customer address 2
     *
     * @var string
     */
    private $customerAddress2 = '';
    
    /**
     * Customer postal code
     *
     * @var string
     */
    private $customerPostalCode = '';
    
    /**
     * Customer city
     *
     * @var string
     */
    private $customerCity = '';
    
    /**
     * Customer state
     *
     * @var string
     */
    private $customerState = '';
    
    /**
     * Customer country
     *
     * @var string
     */
    private $customerCountry = '';
    
    /**
     * Customer country code
     *
     * @var string
     */
    private $customerCountryCode = '';
    
    /**
     * Customer date of birth
     *
     * @var string
     */
    private $customerDateOfBirth = '';
    
    /**
     * Customer phone
     *
     * @var string
     */
    private $customerPhone = '';
    
    /**
     * Customer fax
     *
     * @var string
     */
    private $customerFax = '';
    
    /**
     * Customer email
     *
     * @var string
     */
    private $customerEmail = '';
    
    /**
     * Customer B2B status
     *
     * @var string
     */
    private $customerB2bStatus = '';
    
    /**
     * Billing first name
     *
     * @var string
     */
    private $billingFirstName = '';
    
    /**
     * Billing last name
     *
     * @var string
     */
    private $billingLastName = '';
    
    /**
     * Billing title
     *
     * @var string
     */
    private $billingTitle = '';
    
    /**
     * Billing gender
     *
     * @var string
     */
    private $billingGender = '';
    
    /**
     * Billing company
     *
     * @var string
     */
    private $billingCompany = '';
    
    /**
     * Billing address 1
     *
     * @var string
     */
    private $billingAddress1 = '';
    
    /**
     * Billing address 2
     *
     * @var string
     */
    private $billingAddress2 = '';
    
    /**
     * Billing postal code
     *
     * @var string
     */
    private $billingPostalCode = '';
    
    /**
     * Billing city
     *
     * @var string
     */
    private $billingCity = '';
    
    /**
     * Billing state
     *
     * @var string
     */
    private $billingState = '';
    
    /**
     * Billing country
     *
     * @var string
     */
    private $billingCountry = '';
    
    /**
     * Billing country code
     *
     * @var string
     */
    private $billingCountryCode = '';
    
    /**
     * Billing date of birth
     *
     * @var string
     */
    private $billingDateOfBirth = '';
    
    /**
     * Billing phone
     *
     * @var string
     */
    private $billingPhone = '';
    
    /**
     * Billing fax
     *
     * @var string
     */
    private $billingFax = '';
    
    /**
     * Billing email
     *
     * @var string
     */
    private $billingEmail = '';
    
    /**
     * Shipping first name
     *
     * @var string
     */
    private $shippingFirstName = '';
    
    /**
     * Shipping last name
     *
     * @var string
     */
    private $shippingLastName = '';
    
    /**
     * Shipping title
     *
     * @var string
     */
    private $shippingTitle = '';
    
    /**
     * Shipping gender
     *
     * @var string
     */
    private $shippingGender = '';
    
    /**
     * Shipping Company
     *
     * @var string
     */
    private $shippingCompany = '';
    
    /**
     * Shipping address 1
     *
     * @var string
     */
    private $shippingAddress1 = '';
    
    /**
     * Shipping address 2
     *
     * @var string
     */
    private $shippingAddress2 = '';
    
    /**
     * Shipping postal code
     *
     * @var string
     */
    private $shippingPostalCode = '';
    
    /**
     * Shipping city
     *
     * @var string
     */
    private $shippingCity = '';
    
    /**
     * Shipping state
     *
     * @var string
     */
    private $shippingState = '';
    
    /**
     * Shipping country
     *
     * @var string
     */
    private $shippingCountry = '';
    
    /**
     * Shipping country code
     *
     * @var string
     */
    private $shippingCountryCode = '';
    
    /**
     * Shipping date of birth
     *
     * @var string
     */
    private $shippingDateOfBirth = '';
    
    /**
     * Shipping phone
     *
     * @var string
     */
    private $shippingPhone = '';
    
    /**
     * Shipping fax
     *
     * @var string
     */
    private $shippingFax = '';
    
    /**
     * Shipping email
     *
     * @var string
     */
    private $shippingEmail = '';
    
    
    /**
     * Sets the customers number.
     *
     * @param string $customerNumber Customer number
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerNumber(string $customerNumber): CustomerInformation
    {
        $this->customerNumber = $customerNumber;
        
        return $this;
    }
    
    
    /**
     * Sets the customers first name.
     *
     * @param string $customerFirstName Customer first name
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerFirstName(string $customerFirstName): CustomerInformation
    {
        $this->customerFirstName = $customerFirstName;
        
        return $this;
    }
    
    
    /**
     * Sets the customers last name.
     *
     * @param string $customerLastName Customer last name
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerLastName(string $customerLastName): CustomerInformation
    {
        $this->customerLastName = $customerLastName;
        
        return $this;
    }
    
    
    /**
     * Sets the customers title.
     *
     * @param string $customerTitle Customer title
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerTitle(string $customerTitle): CustomerInformation
    {
        $this->customerTitle = $customerTitle;
        
        return $this;
    }
    
    
    /**
     * Sets the customers gender.
     *
     * @param string $customerGender Customer gender
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation
     */
    public function setCustomerGender(string $customerGender): CustomerInformation
    {
        $this->customerGender = $customerGender;
        
        return $this;
    }
    
    
    /**
     * Sets the customers company.
     *
     * @param string $customerCompany Customer company
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerCompany(string $customerCompany): CustomerInformation
    {
        $this->customerCompany = $customerCompany;
        
        return $this;
    }
    
    
    /**
     * Sets the customers first address line.
     *
     * @param string $customerAddress1 Customer address 1
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerAddress1(string $customerAddress1): CustomerInformation
    {
        $this->customerAddress1 = $customerAddress1;
        
        return $this;
    }
    
    
    /**
     * Sets the customers second address line.
     *
     * @param string $customerAddress2 Customer address 2
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerAddress2(string $customerAddress2): CustomerInformation
    {
        $this->customerAddress2 = $customerAddress2;
        
        return $this;
    }
    
    
    /**
     * Sets the customers postal code.
     *
     * @param string $customerPostalCode Customer postal code
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerPostalCode(string $customerPostalCode): CustomerInformation
    {
        $this->customerPostalCode = $customerPostalCode;
        
        return $this;
    }
    
    
    /**
     * Sets the customers city.
     *
     * @param string $customerCity Customer city
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerCity(string $customerCity): CustomerInformation
    {
        $this->customerCity = $customerCity;
        
        return $this;
    }
    
    
    /**
     * Sets the customers state.
     *
     * @param string $customerState Customer state
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerState(string $customerState): CustomerInformation
    {
        $this->customerState = $customerState;
        
        return $this;
    }
    
    
    /**
     * Sets the customers country.
     *
     * @param string $customerCountry Customer country
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerCountry(string $customerCountry): CustomerInformation
    {
        $this->customerCountry = $customerCountry;
        
        return $this;
    }
    
    
    /**
     * Sets the customers country code.
     *
     * @param string $customerCountryCode Customer country code
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerCountryCode(string $customerCountryCode): CustomerInformation
    {
        $this->customerCountryCode = $customerCountryCode;
        
        return $this;
    }
    
    
    /**
     * Sets the customers date of birth.
     *
     * @param string $customerDateOfBirth Customer date of birth
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerDateOfBirth(string $customerDateOfBirth): CustomerInformation
    {
        $this->customerDateOfBirth = $customerDateOfBirth;
        
        return $this;
    }
    
    
    /**
     * Sets the customers phone number.
     *
     * @param string $customerPhone Customer phone
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerPhone(string $customerPhone): CustomerInformation
    {
        $this->customerPhone = $customerPhone;
        
        return $this;
    }
    
    
    /**
     * Sets the customers fax number.
     *
     * @param string $customerFax Customer fax
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerFax(string $customerFax): CustomerInformation
    {
        $this->customerFax = $customerFax;
        
        return $this;
    }
    
    
    /**
     * Sets the customers e-mail address.
     *
     * @param string $customerEmail Customer email
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerEmail(string $customerEmail): CustomerInformation
    {
        $this->customerEmail = $customerEmail;
        
        return $this;
    }
    
    
    /**
     * Sets the customer’s B2B status
     *
     * @param string $customerB2bStatus
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setCustomerB2bStatus(string $customerB2bStatus): CustomerInformation
    {
        $this->customerB2bStatus = $customerB2bStatus;
        
        return $this;
    }
    
    
    /**
     * Sets the first name of the billing.
     *
     * @param string $billingFirstName
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingFirstName(string $billingFirstName): CustomerInformation
    {
        $this->billingFirstName = $billingFirstName;
        
        return $this;
    }
    
    
    /**
     * Sets the last name of the billing.
     *
     * @param string $billingLastName Billing last name
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingLastName(string $billingLastName): CustomerInformation
    {
        $this->billingLastName = $billingLastName;
        
        return $this;
    }
    
    
    /**
     * Sets the title of the billing.
     *
     * @param string $billingTitle Billing title
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingTitle(string $billingTitle): CustomerInformation
    {
        $this->billingTitle = $billingTitle;
        
        return $this;
    }
    
    
    /**
     * Sets the gender of the billing.
     *
     * @param string $billingGender Billing gender
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingGender(string $billingGender): CustomerInformation
    {
        $this->billingGender = $billingGender;
        
        return $this;
    }
    
    
    /**
     * Sets the company of the billing.
     *
     * @param string $billingCompany Billing company
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingCompany(string $billingCompany): CustomerInformation
    {
        $this->billingCompany = $billingCompany;
        
        return $this;
    }
    
    
    /**
     * Sets the first address line of the billing.
     *
     * @param string $billingAddress1 Billing address 1
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingAddress1(string $billingAddress1): CustomerInformation
    {
        $this->billingAddress1 = $billingAddress1;
        
        return $this;
    }
    
    
    /**
     * Sets the second address line of the billing.
     *
     * @param string $billingAddress2 Billing address 2
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingAddress2(string $billingAddress2): CustomerInformation
    {
        $this->billingAddress2 = $billingAddress2;
        
        return $this;
    }
    
    
    /**
     * Sets the postal code of the billing.
     *
     * @param string $billingPostalCode Billing postal code
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingPostalCode(string $billingPostalCode): CustomerInformation
    {
        $this->billingPostalCode = $billingPostalCode;
        
        return $this;
    }
    
    
    /**
     * Sets the city of the billing.
     *
     * @param string $billingCity Billing city
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingCity(string $billingCity): CustomerInformation
    {
        $this->billingCity = $billingCity;
        
        return $this;
    }
    
    
    /**
     * Sets the state of the billing.
     *
     * @param string $billingState Billing state
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingState(string $billingState): CustomerInformation
    {
        $this->billingState = $billingState;
        
        return $this;
    }
    
    
    /**
     * Sets the country of the billing.
     *
     * @param string $billingCountry Billing country
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingCountry(string $billingCountry): CustomerInformation
    {
        $this->billingCountry = $billingCountry;
        
        return $this;
    }
    
    
    /**
     * Sets the country code of the billing.
     *
     * @param string $billingCountryCode Billing country code
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingCountryCode(string $billingCountryCode): CustomerInformation
    {
        $this->billingCountryCode = $billingCountryCode;
        
        return $this;
    }
    
    
    /**
     * Sets the date of birth of the billing.
     *
     * @param string $billingDateOfBirth Billing date of birth
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingDateOfBirth(string $billingDateOfBirth): CustomerInformation
    {
        $this->billingDateOfBirth = $billingDateOfBirth;
        
        return $this;
    }
    
    
    /**
     * Sets the phone number of the billing.
     *
     * @param string $billingPhone Billing phone
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingPhone(string $billingPhone): CustomerInformation
    {
        $this->billingPhone = $billingPhone;
        
        return $this;
    }
    
    
    /**
     * Sets the fax number of the billing.
     *
     * @param string $billingFax Billing fax
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingFax(string $billingFax): CustomerInformation
    {
        $this->billingFax = $billingFax;
        
        return $this;
    }
    
    
    /**
     * Sets the e-mail address of the billing.
     *
     * @param string $billingEmail Billing email
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setBillingEmail(string $billingEmail): CustomerInformation
    {
        $this->billingEmail = $billingEmail;
        
        return $this;
    }
    
    
    /**
     * Sets the first name of the shipping.
     *
     * @param string $shippingFirstName Billing first name
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingFirstName(string $shippingFirstName): CustomerInformation
    {
        $this->shippingFirstName = $shippingFirstName;
        
        return $this;
    }
    
    
    /**
     * Sets the last name of the shipping.
     *
     * @param string $shippingLastName Billing last name
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingLastName(string $shippingLastName): CustomerInformation
    {
        $this->shippingLastName = $shippingLastName;
        
        return $this;
    }
    
    
    /**
     * Sets the title of the shipping.
     *
     * @param string $shippingTitle Shipping title
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingTitle(string $shippingTitle): CustomerInformation
    {
        $this->shippingTitle = $shippingTitle;
        
        return $this;
    }
    
    
    /**
     * Sets the gender of the shipping.
     *
     * @param string $shippingGender Shipping gender
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingGender(string $shippingGender): CustomerInformation
    {
        $this->shippingGender = $shippingGender;
        
        return $this;
    }
    
    
    /**
     * Sets the company of the shipping.
     *
     * @param string $shippingCompany Shipping company
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingCompany(string $shippingCompany): CustomerInformation
    {
        $this->shippingCompany = $shippingCompany;
        
        return $this;
    }
    
    
    /**
     * Sets the first address line of the shipping.
     *
     * @param string $shippingAddress1 Shipping address 1
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingAddress1(string $shippingAddress1): CustomerInformation
    {
        $this->shippingAddress1 = $shippingAddress1;
        
        return $this;
    }
    
    
    /**
     * Sets the second address line of the shipping.
     *
     * @param string $shippingAddress2 Shipping address 2
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingAddress2(string $shippingAddress2): CustomerInformation
    {
        $this->shippingAddress2 = $shippingAddress2;
        
        return $this;
    }
    
    
    /**
     * Sets the postal code of the shipping.
     *
     * @param string $shippingPostalCode Shipping postal code
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingPostalCode(string $shippingPostalCode): CustomerInformation
    {
        $this->shippingPostalCode = $shippingPostalCode;
        
        return $this;
    }
    
    
    /**
     * Sets the city of the shipping.
     *
     * @param string $shippingCity Shipping city
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingCity(string $shippingCity): CustomerInformation
    {
        $this->shippingCity = $shippingCity;
        
        return $this;
    }
    
    
    /**
     * Sets the state of the shipping.
     *
     * @param string $shippingState Shipping state
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingState(string $shippingState): CustomerInformation
    {
        $this->shippingState = $shippingState;
        
        return $this;
    }
    
    
    /**
     * Sets the country of the shipping.
     *
     * @param string $shippingCountry Shipping country
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingCountry(string $shippingCountry): CustomerInformation
    {
        $this->shippingCountry = $shippingCountry;
        
        return $this;
    }
    
    
    /**
     * Sets the country code of the shipping.
     *
     * @param string $shippingCountryCode Shipping country code
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingCountryCode(string $shippingCountryCode): CustomerInformation
    {
        $this->shippingCountryCode = $shippingCountryCode;
        
        return $this;
    }
    
    
    /**
     * Sets the date of birth of the shipping.
     *
     * @param string $shippingDateOfBirth Shipping date of birth
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingDateOfBirth(string $shippingDateOfBirth): CustomerInformation
    {
        $this->shippingDateOfBirth = $shippingDateOfBirth;
        
        return $this;
    }
    
    
    /**
     * Sets the phone number of the shipping.
     *
     * @param string $shippingPhone Shipping phone
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingPhone(string $shippingPhone): CustomerInformation
    {
        $this->shippingPhone = $shippingPhone;
        
        return $this;
    }
    
    
    /**
     * Sets the fax number of the shipping.
     *
     * @param string $shippingFax Shipping fax
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingFax(string $shippingFax): CustomerInformation
    {
        $this->shippingFax = $shippingFax;
        
        return $this;
    }
    
    
    /**
     * Sets the e-mail address of the shipping.
     *
     * @param string $shippingEmail Shipping email
     *
     * @return \HubPublic\ValueObjects\Builder\CustomerInformation For chained method calls
     */
    public function setShippingEmail(string $shippingEmail): CustomerInformation
    {
        $this->shippingEmail = $shippingEmail;
        
        return $this;
    }
    
    
    /**
     * Creates an CustomerInformation value object.
     *
     * @return \HubPublic\ValueObjects\CustomerInformation New CustomerInformation instance
     */
    public function build(): CustomerInformationValueObject
    {
        return new CustomerInformationValueObject(
            $this->customerNumber,
            $this->customerFirstName,
            $this->customerLastName,
            $this->customerTitle,
            $this->customerGender,
            $this->customerCompany,
            $this->customerAddress1,
            $this->customerAddress2,
            $this->customerPostalCode,
            $this->customerCity,
            $this->customerState,
            $this->customerCountry,
            $this->customerCountryCode,
            $this->customerDateOfBirth,
            $this->customerPhone,
            $this->customerFax,
            $this->customerEmail,
            $this->customerB2bStatus,
            $this->billingFirstName,
            $this->billingLastName,
            $this->billingTitle,
            $this->billingGender,
            $this->billingCompany,
            $this->billingAddress1,
            $this->billingAddress2,
            $this->billingPostalCode,
            $this->billingCity,
            $this->billingState,
            $this->billingCountry,
            $this->billingCountryCode,
            $this->billingDateOfBirth,
            $this->billingPhone,
            $this->billingFax,
            $this->billingEmail,
            $this->shippingFirstName,
            $this->shippingLastName,
            $this->shippingTitle,
            $this->shippingGender,
            $this->shippingCompany,
            $this->shippingAddress1,
            $this->shippingAddress2,
            $this->shippingPostalCode,
            $this->shippingCity,
            $this->shippingState,
            $this->shippingCountry,
            $this->shippingCountryCode,
            $this->shippingDateOfBirth,
            $this->shippingPhone,
            $this->shippingFax,
            $this->shippingEmail
        );
    }
}
