<?php
/* --------------------------------------------------------------
   InvoiceInformation.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceInformation
 *
 * @category   System
 * @package    Invoice
 * @subpackage ValueObjects
 */
class InvoiceInformation
{
    /**
     * @var string
     */
    protected $invoiceNumber;
    
    /**
     * @var DateTime
     */
    protected $invoiceDate;
    
    /**
     * @var CurrencyCode
     */
    protected $currency;
    
    /**
     * @var double
     */
    protected $totalSum;
    
    /**
     * @var int
     */
    protected $customerId;
    
    /**
     * @var CustomerStatusInformation
     */
    protected $customerStatusInformation;
    
    /**
     * @var AddressBlockInterface
     */
    protected $paymentAddress;
    
    /**
     * @var AddressBlockInterface
     */
    protected $shippingAddress;
    
    /**
     * @var int
     */
    protected $orderId;
    
    /**
     * @var DateTime
     */
    protected $orderPurchaseDate;
    
    /**
     * @var OrderPaymentType
     */
    protected $paymentType;
    
    
    /**
     * InvoiceInformation constructor.
     *
     * @param StringType                $invoiceNumber
     * @param DateTime                  $invoiceDate
     * @param CurrencyCode              $currency
     * @param DecimalType               $totalSum
     * @param IdType                    $customerId
     * @param CustomerStatusInformation $customerStatusInformation
     * @param AddressBlockInterface     $paymentAddress
     * @param AddressBlockInterface     $shippingAddress
     * @param IdType                    $orderId
     * @param DateTime                  $orderPurchaseDate
     * @param OrderPaymentType          $paymentType
     */
    public function __construct(
        StringType $invoiceNumber,
        DateTime $invoiceDate,
        CurrencyCode $currency,
        DecimalType $totalSum,
        IdType $customerId,
        CustomerStatusInformation $customerStatusInformation,
        AddressBlockInterface $paymentAddress,
        AddressBlockInterface $shippingAddress,
        IdType $orderId,
        DateTime $orderPurchaseDate,
        OrderPaymentType $paymentType
    ) {
        $this->invoiceNumber             = $invoiceNumber->asString();
        $this->invoiceDate               = $invoiceDate;
        $this->currency                  = $currency;
        $this->totalSum                  = $totalSum->asDecimal();
        $this->customerId                = $customerId->asInt();
        $this->customerStatusInformation = $customerStatusInformation;
        $this->paymentAddress            = $paymentAddress;
        $this->shippingAddress           = $shippingAddress;
        $this->orderId                   = $orderId->asInt();
        $this->orderPurchaseDate         = $orderPurchaseDate;
        $this->paymentType               = $paymentType;
    }
    
    
    /**
     * Invoice Number Getter
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }
    
    
    /**
     * Invoice Date Getter
     *
     * @return DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }
    
    
    /**
     * Currency Getter
     *
     * @return CurrencyCode
     */
    public function getCurrency()
    {
        return $this->currency;
    }
    
    
    /**
     * Total Sum Getter
     *
     * @return double
     */
    public function getTotalSum()
    {
        return $this->totalSum;
    }
    
    
    /**
     * Customer ID Getter
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
    
    
    /**
     * Customer Status Information Getter
     *
     * @return CustomerStatusInformation
     */
    public function getCustomerStatusInformation()
    {
        return $this->customerStatusInformation;
    }
    
    
    /**
     * Payment Address Getter
     *
     * @return AddressBlockInterface
     */
    public function getPaymentAddress()
    {
        return $this->paymentAddress;
    }
    
    
    /**
     * Shipping Address Getter
     *
     * @return AddressBlockInterface
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
    
    
    /**
     * Order ID Getter
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
    
    
    /**
     * Order Purchase Date Getter
     *
     * @return DateTime
     */
    public function getOrderPurchaseDate()
    {
        return $this->orderPurchaseDate;
    }
    
    
    /**
     * Get Payment Type Getter
     *
     * @return OrderPaymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }
}