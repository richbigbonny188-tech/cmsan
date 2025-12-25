<?php
/* --------------------------------------------------------------
   InvoiceListItem.inc.php 2016-10-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InvoiceListItem
 *
 * @category   System
 * @package    Invoice
 */
class InvoiceListItem
{
    /**
     * @var int
     */
    protected $invoiceId = 0;
    
    /**
     * @var string
     */
    protected $invoiceNumber = '';
    
    /**
     * Filename of the invoice
     *
     * @var string
     */
    protected $invoiceFilename = '';
    
    /**
     * @var DateTime
     */
    protected $invoiceDate;
    
    /**
     * @var float
     */
    protected $totalSum = 0;
    
    /**
     * @var CurrencyCode
     */
    protected $currency;
    
    /**
     * @var int
     */
    protected $customerId = 0;
    
    /**
     * @var string
     */
    protected $customerName = '';
    
    /**
     * @var int
     */
    protected $customerStatusId = 0;
    
    /**
     * @var string
     */
    protected $customerStatusName = '';
    
    /**
     * @var CustomerMemoCollection
     */
    protected $customerMemos;
    
    /**
     * @var OrderAddressBlock
     */
    protected $paymentAddress;
    
    /**
     * @var OrderAddressBlock
     */
    protected $shippingAddress;
    
    /**
     * @var int
     */
    protected $orderId = 0;
    
    /**
     * @var DateTime
     */
    protected $orderDatePurchased;
    
    /**
     * @var OrderPaymentType
     */
    protected $paymentType;
    
    /**
     * @var int
     */
    protected $orderStatusId = 0;
    
    /**
     * @var string
     */
    protected $orderStatusName = '';
    
    
    /**
     * Invoice ID Getter
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }
    
    
    /**
     * Invoice ID Setter
     *
     * @param IdType $invoiceId
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setInvoiceId(IdType $invoiceId)
    {
        $this->invoiceId = $invoiceId->asInt();
        
        return $this;
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
     * Invoice Number Setter
     *
     * @param StringType $invoiceNumber
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setInvoiceNumber(StringType $invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber->asString();
        
        return $this;
    }
    
    
    /**
     * Invoice Filename Getter
     *
     * @return string
     */
    public function getInvoiceFilename()
    {
        return $this->invoiceFilename;
    }
    
    
    /**
     * Invoice Filename Setter
     *
     * @param FilenameStringType $invoiceFilename
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setInvoiceFilename(FilenameStringType $invoiceFilename)
    {
        $this->invoiceFilename = $invoiceFilename->asString();
        
        return $this;
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
     * Invoice Date Setter
     *
     * @param DateTime $invoiceDate
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setInvoiceDate(DateTime $invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
        
        return $this;
    }
    
    
    /**
     * Total Sum Getter
     *
     * @return float
     */
    public function getTotalSum()
    {
        return $this->totalSum;
    }
    
    
    /**
     * Total Sum Setter
     *
     * @param DecimalType $totalSum
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setTotalSum(DecimalType $totalSum)
    {
        $this->totalSum = $totalSum->asDecimal();
        
        return $this;
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
     * Currency Setter
     *
     * @param CurrencyCode $currency
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setCurrency(CurrencyCode $currency)
    {
        $this->currency = $currency;
        
        return $this;
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
     * Customer ID Setter
     *
     * @param IdType $customerId
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setCustomerId(IdType $customerId)
    {
        $this->customerId = $customerId->asInt();
        
        return $this;
    }
    
    
    /**
     * Customer Name Getter
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }
    
    
    /**
     * Customer Name Setter
     *
     * @param StringType $customerName
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setCustomerName(StringType $customerName)
    {
        $this->customerName = $customerName->asString();
        
        return $this;
    }
    
    
    /**
     * Customer Status ID Getter
     *
     * @return int
     */
    public function getCustomerStatusId()
    {
        return $this->customerStatusId;
    }
    
    
    /**
     * Customer Status ID Setter
     *
     * @param IdType $customerStatusId
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setCustomerStatusId(IdType $customerStatusId)
    {
        $this->customerStatusId = $customerStatusId->asInt();
        
        return $this;
    }
    
    
    /**
     * Customer Status Name Getter
     *
     * @return string
     */
    public function getCustomerStatusName()
    {
        return $this->customerStatusName;
    }
    
    
    /**
     * Customer Status Name Setter
     *
     * @param StringType $customerStatusName
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setCustomerStatusName(StringType $customerStatusName)
    {
        $this->customerStatusName = $customerStatusName->asString();
        
        return $this;
    }
    
    
    /**
     * Customer Memos Getter
     *
     * @return CustomerMemoCollection
     */
    public function getCustomerMemos()
    {
        return $this->customerMemos;
    }
    
    
    /**
     * Customer Memos Setter
     *
     * @param CustomerMemoCollection $customerMemos
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setCustomerMemos(CustomerMemoCollection $customerMemos)
    {
        $this->customerMemos = $customerMemos;
        
        return $this;
    }
    
    
    /**
     * Payment Address Getter
     *
     * @return OrderAddressBlock
     */
    public function getPaymentAddress()
    {
        return $this->paymentAddress;
    }
    
    
    /**
     * Payment Address Setter
     *
     * @param OrderAddressBlock $paymentAddress
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setPaymentAddress(OrderAddressBlock $paymentAddress)
    {
        $this->paymentAddress = $paymentAddress;
        
        return $this;
    }
    
    
    /**
     * Shipping Address Getter
     *
     * @return OrderAddressBlock
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
    
    
    /**
     * Shipping Address Setter
     *
     * @param OrderAddressBlock $shippingAddress
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setShippingAddress(OrderAddressBlock $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        
        return $this;
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
     * Order ID Setter
     *
     * @param IdType $orderId
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setOrderId(IdType $orderId)
    {
        $this->orderId = $orderId->asInt();
        
        return $this;
    }
    
    
    /**
     * Order Date Purchased Getter
     *
     * @return DateTime
     */
    public function getOrderDatePurchased()
    {
        return $this->orderDatePurchased;
    }
    
    
    /**
     * Order Date Purchased Setter
     *
     * @param DateTime $orderDatePurchased
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setOrderDatePurchased(DateTime $orderDatePurchased)
    {
        $this->orderDatePurchased = $orderDatePurchased;
        
        return $this;
    }
    
    
    /**
     * Payment Type Getter
     *
     * @return OrderPaymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }
    
    
    /**
     * Payment Type Setter
     *
     * @param OrderPaymentType $paymentType
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setPaymentType(OrderPaymentType $paymentType)
    {
        $this->paymentType = $paymentType;
        
        return $this;
    }
    
    
    /**
     * Order Status ID Getter
     *
     * @return int
     */
    public function getOrderStatusId()
    {
        return $this->orderStatusId;
    }
    
    
    /**
     * Order Status ID Setter
     *
     * @param IdType $orderStatusId
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setOrderStatusId(IdType $orderStatusId)
    {
        $this->orderStatusId = $orderStatusId->asInt();
        
        return $this;
    }
    
    
    /**
     * Order Status Name Setter
     *
     * @return string
     */
    public function getOrderStatusName()
    {
        return $this->orderStatusName;
    }
    
    
    /**
     * Order Status Name Setter
     *
     * @param StringType $orderStatusName
     *
     * @return InvoiceListItem Returns same instance for chained method calls.
     */
    public function setOrderStatusName(StringType $orderStatusName)
    {
        $this->orderStatusName = $orderStatusName->asString();
        
        return $this;
    }
    
    
    /**
     * Check if invoice is a cancellation invoice.
     *
     * @return bool
     */
    public function isCancellationInvoice()
    {
        return strpos($this->invoiceNumber, '_STORNO') !== false;
    }
}