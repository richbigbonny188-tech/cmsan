<?php
/* --------------------------------------------------------------
   OrderContent.php 2016-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use DateTime;

/**
 * Class OrderContent
 *
 * An OrderContent contains customer, amount, currencyCode, languageCode,
 * paymentMethod, shippingMethod, customerNumber, orderDateTime, orderNumber,
 * invoiceDateTime, invoiceNumber
 *
 * @package HubPublic\ValueObjects
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
     * OrderContent constructor.
     *
     * @param \HubPublic\ValueObjects\CustomerInformation $customer        Customer information
     * @param float                                       $amount          Amount
     * @param string                                      $currencyCode    Currency code
     * @param string                                      $languageCode    Language code
     * @param string                                      $paymentMethod   Payment method
     * @param string                                      $shippingMethod  Shipping method
     * @param string|null                                 $customerNumber  Customer number
     * @param \DateTime|null                              $orderDateTime   Order DateTime object
     * @param string|null                                 $orderNumber     Order number
     * @param \DateTime|null                              $invoiceDateTime Invoice DateTime object
     * @param string|null                                 $invoiceNumber   Invoice number
     */
    public function __construct(
        CustomerInformation $customer,
        float $amount,
        string $currencyCode,
        string $languageCode,
        string $paymentMethod,
        string $shippingMethod,
        string $customerNumber = null,
        DateTime $orderDateTime = null,
        string $orderNumber = null,
        DateTime $invoiceDateTime = null,
        string $invoiceNumber = null
    ) {
        
        $this->customer        = $customer;
        $this->amount          = $amount;
        $this->currencyCode    = $currencyCode;
        $this->languageCode    = $languageCode;
        $this->paymentMethod   = $paymentMethod;
        $this->shippingMethod  = $shippingMethod;
        $this->customerNumber  = ($customerNumber === null) ? '' : $customerNumber;
        $this->orderDateTime   = ($orderDateTime !== null) ? $orderDateTime : new DateTime('1000-01-01 00:00:00');
        $this->orderNumber     = ($orderNumber === null) ? '' : $orderNumber;
        $this->invoiceDateTime = ($invoiceDateTime !== null) ? $invoiceDateTime : new DateTime('1000-01-01 00:00:00');
        $this->invoiceNumber   = ($invoiceNumber === null) ? '' : $invoiceNumber;
    }
    
    
    /**
     * Returns the CustomerInformation Object.
     *
     * @return \HubPublic\ValueObjects\CustomerInformation Customer information instance
     */
    public function getCustomer(): CustomerInformation
    {
        return $this->customer;
    }
    
    
    /**
     * Returns the amount.
     *
     * @return float Amount
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    
    /**
     * Returns the currency code.
     *
     * @return string Currency code
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
    
    
    /**
     * Returns the language code.
     *
     * @return string Language code
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * Returns the payment method.
     *
     * @return string Payment method
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    
    
    /**
     * Returns the shipping method.
     *
     * @return string Shipping method
     */
    public function getShippingMethod(): string
    {
        return $this->shippingMethod;
    }
    
    
    /**
     * Returns the customer number.
     *
     * @return string Customer number
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }
    
    
    /**
     * Returns the date time of the order.
     *
     * @return \DateTime Order DateTime object
     */
    public function getOrderDateTime(): DateTime
    {
        return $this->orderDateTime;
    }
    
    
    /**
     * Returns the order number.
     *
     * @return string Order number
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }
    
    
    /**
     * Returns the date time of the invoice.
     *
     * @return \DateTime Invoice DateTime object
     */
    public function getInvoiceDateTime(): DateTime
    {
        return $this->invoiceDateTime;
    }
    
    
    /**
     * Returns the invoice number
     *
     * @return string Invoice Number
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }
}
