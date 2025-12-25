<?php
/* --------------------------------------------------------------
   OrderContent.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects\Builder;

use DateTime;
use HubPublic\ValueObjects\CustomerInformation as CustomerInformationValueObject;
use HubPublic\ValueObjects\OrderContent as OrderContentValueObject;

/**
 * Class OrderContent
 *
 * @package HubPublic\ValueObjects\Builder
 */
class OrderContent
{
    /**
     * Customer information
     *
     * @var \HubPublic\ValueObjects\CustomerInformation
     */
    private $customer;
    
    /**
     * Amount
     *
     * @var float
     */
    private $amount;
    
    /**
     * Currency code
     *
     * @var string
     */
    private $currencyCode;
    
    /**
     * Language code
     *
     * @var string
     */
    private $languageCode;
    
    /**
     * Payment method
     *
     * @var string
     */
    private $paymentMethod;
    
    /**
     * Shipping method
     *
     * @var string
     */
    private $shippingMethod;
    
    /**
     * Customer number
     *
     * @var string
     */
    private $customerNumber;
    
    /**
     * Order date time
     *
     * @var \DateTime
     */
    private $orderDateTime;
    
    /**
     * Order number
     *
     * @var string
     */
    private $orderNumber;
    
    /**
     * Invoice date time
     *
     * @var \DateTime
     */
    private $invoiceDateTime;
    
    /**
     * Invoice number
     *
     * @var  string
     */
    private $invoiceNumber;
    
    
    /**
     * Sets the customer information
     *
     * @param \HubPublic\ValueObjects\CustomerInformation $customer CustomerInformation instance, provides customer
     *                                                              information
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setCustomer(CustomerInformationValueObject $customer): OrderContent
    {
        $this->customer = $customer;
        
        return $this;
    }
    
    
    /**
     * Sets the amount.
     *
     * @param float $amount Amount that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setAmount(float $amount): OrderContent
    {
        $this->amount = $amount;
        
        return $this;
    }
    
    
    /**
     * Sets the currency code.
     *
     * @param string $currencyCode Code that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setCurrencyCode(string $currencyCode): OrderContent
    {
        $this->currencyCode = trim($currencyCode);
        
        return $this;
    }
    
    
    /**
     * Sets the language code.
     *
     * @param string $languageCode Code that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setLanguageCode(string $languageCode): OrderContent
    {
        $this->languageCode = trim($languageCode);
        
        return $this;
    }
    
    
    /**
     * Sets the payment method.
     *
     * @param string $paymentMethod Method that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setPaymentMethod(string $paymentMethod): OrderContent
    {
        $this->paymentMethod = trim($paymentMethod);
        
        return $this;
    }
    
    
    /**
     * Sets the payment method.
     *
     * @param string $shippingMethod Method that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setShippingMethod(string $shippingMethod): OrderContent
    {
        $this->shippingMethod = trim($shippingMethod);
        
        return $this;
    }
    
    
    /**
     * Sets the customer number.
     *
     * @param string|null $customerNumber Number that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setCustomerNumber(?string $customerNumber = null): OrderContent
    {
        $this->customerNumber = trim((string)$customerNumber);
        
        return $this;
    }
    
    
    /**
     * Sets the order date time.
     *
     * @param \DateTime|null $orderDateTime DateTime-Object that provides the date time information
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setOrderDateTime(?DateTime $orderDateTime = null): OrderContent
    {
        $this->orderDateTime = $orderDateTime;
        
        return $this;
    }
    
    
    /**
     * Sets the order number
     *
     * @param string|null $orderNumber Number that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setOrderNumber(?string $orderNumber = null): OrderContent
    {
        $this->orderNumber = trim((string)$orderNumber);
        
        return $this;
    }
    
    
    /**
     * Sets the invoice date time
     *
     * @param \DateTime|null $invoiceDateTime DateTime-Object that provides the date time information
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setInvoiceDateTime(?DateTime $invoiceDateTime = null): OrderContent
    {
        $this->invoiceDateTime = $invoiceDateTime;
        
        return $this;
    }
    
    
    /**
     * Sets the invoice number
     *
     * @param string|null $invoiceNumber Number that should be set
     *
     * @return \HubPublic\ValueObjects\Builder\OrderContent For chained method calls
     */
    public function setInvoiceNumber(?string $invoiceNumber = null): OrderContent
    {
        $this->invoiceNumber = trim((string)$invoiceNumber);
        
        return $this;
    }
    
    
    /**
     * Creates an OrderContent value object.
     *
     * @return \HubPublic\ValueObjects\OrderContent
     */
    public function build(): OrderContentValueObject
    {
        return new OrderContentValueObject(
            $this->customer,
            $this->amount,
            $this->currencyCode,
            $this->languageCode,
            $this->paymentMethod,
            $this->shippingMethod,
            $this->customerNumber,
            $this->orderDateTime,
            $this->orderNumber,
            $this->invoiceDateTime,
            $this->invoiceNumber
        );
    }
}
