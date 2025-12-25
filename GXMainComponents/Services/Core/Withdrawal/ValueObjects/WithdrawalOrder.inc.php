<?php
/* --------------------------------------------------------------
   WithdrawalOrder.inc.php 2018-01-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WithdrawalOrder
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage ValueObjects
 */
class WithdrawalOrder implements WithdrawalOrderInterface
{
    /**
     * @var int
     */
    protected $orderId;
    
    /**
     * @var int
     */
    protected $customerId;
    
    /**
     * @var \StringType
     */
    protected $customerGender;
    
    /**
     * @var string
     */
    protected $customerFirstName;
    
    /**
     * @var string
     */
    protected $customerLastName;
    
    /**
     * @var string
     */
    protected $customerStreetAddress;
    
    /**
     * @var int
     */
    protected $customerPostCode;
    
    /**
     * @var string
     */
    protected $customerCity;
    
    /**
     * @var string
     */
    protected $customerCountry;
    
    /**
     * @var string
     */
    protected $customerEmail;
    
    /**
     * @var \DateTime
     */
    protected $orderDate;
    
    /**
     * @var \DateTime
     */
    protected $deliveryDate;
    
    
    /**
     * WithdrawalOrder constructor.
     *
     * @param \IntType    $orderId
     * @param \IntType    $customerId
     * @param \StringType $customerGender
     * @param \StringType $customerFirstName
     * @param \StringType $customerLastName
     * @param \StringType $customerStreetAddress
     * @param \IntType    $customerPostCode
     * @param \StringType $customerCity
     * @param \StringType $customerCountry
     * @param \StringType $customerEmail
     * @param \DateTime   $orderDate
     * @param \DateTime   $deliveryDate
     */
    public function __construct(
        IntType $orderId,
        IntType $customerId,
        StringType $customerGender,
        StringType $customerFirstName,
        StringType $customerLastName,
        StringType $customerStreetAddress,
        IntType $customerPostCode,
        StringType $customerCity,
        StringType $customerCountry,
        StringType $customerEmail,
        DateTime $orderDate,
        DateTime $deliveryDate
    ) {
        $this->orderId               = $orderId->asInt();
        $this->customerId            = $customerId->asInt();
        $this->customerGender        = $customerGender->asString();
        $this->customerFirstName     = $customerFirstName->asString();
        $this->customerLastName      = $customerLastName->asString();
        $this->customerStreetAddress = $customerStreetAddress->asString();
        $this->customerPostCode      = $customerPostCode->asInt();
        $this->customerCity          = $customerCity->asString();
        $this->customerCountry       = $customerCountry->asString();
        $this->customerEmail         = $customerEmail->asString();
        $this->orderDate             = $orderDate;
        $this->deliveryDate          = $deliveryDate;
    }
    
    
    /**
     * Returns the order id.
     *
     * @return int The order id.
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
    
    
    /**
     * Returns the customer id.
     *
     * @return int The customer id.
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
    
    
    /**
     * Returns the customer gender.
     *
     * @return string The customer gender.
     */
    public function getCustomerGender()
    {
        return $this->customerGender;
    }
    
    
    /**
     * Returns the customer first name.
     *
     * @return string The customer first name.
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }
    
    
    /**
     * Returns the customer last name.
     *
     * @return string The customer last name.
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }
    
    
    /**
     * Returns the customer street address.
     *
     * @return string The customer street address.
     */
    public function getCustomerStreetAddress()
    {
        return $this->customerStreetAddress;
    }
    
    
    /**
     * Returns the customer post code.
     *
     * @return int The customer post code.
     */
    public function getCustomerPostCode()
    {
        return $this->customerPostCode;
    }
    
    
    /**
     * Returns the customer city.
     *
     * @return string The customer city.
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }
    
    
    /**
     * Returns the customer country.
     *
     * @return string The customer country.
     */
    public function getCustomerCountry()
    {
        return $this->customerCountry;
    }
    
    
    /**
     * Returns the customer Email.
     *
     * @return string The customer Email.
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }
    
    
    /**
     * Returns the order datetime.
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }
    
    
    /**
     * Returns the delivery datetime.
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }
}