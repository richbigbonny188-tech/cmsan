<?php

/* --------------------------------------------------------------
   ReviewCustomer.inc.php 2017-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewCustomer
 *
 * @category   System
 * @package    Review
 * @subpackage ValueObjects
 */
class ReviewCustomer implements ReviewCustomerInterface
{
    /**
     * @var int
     */
    protected $customerId;
    
    /**
     * @var string
     */
    protected $customerName;
    
    
    /**
     * ReviewCustomer constructor.
     *
     * @param \IntType    $customerId
     * @param \StringType $customerName
     */
    public function __construct(IntType $customerId, StringType $customerName)
    {
        $this->customerId   = $customerId->asInt();
        $this->customerName = $customerName->asString();
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
     * Returns the customer name.
     *
     * @return string The customer name.
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }
}