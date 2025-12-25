<?php
/* --------------------------------------------------------------
   AgreementCustomer.inc.php 2018-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AgreementCustomerInterface
 *
 * @category   System
 * @package    Agreement
 * @subpackage ValueObjects
 */
class AgreementCustomer implements AgreementCustomerInterface
{
    /**
     * @var string
     */
    protected $customerName;
    
    /**
     * @var \CustomerEmailInterface
     */
    protected $customerEmail;
    
    
    /**
     * AgreementCustomer constructor.
     *
     * @param \StringType             $customerName  Customer name.
     * @param \CustomerEmailInterface $customerEmail Customer email.
     */
    public function __construct(StringType $customerName, CustomerEmailInterface $customerEmail)
    {
        $this->customerName  = $customerName->asString();
        $this->customerEmail = $customerEmail;
    }
    
    
    /**
     * Returns the customers name.
     *
     * @return string Customer name.
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }
    
    
    /**
     * Returns the customers email.
     *
     * @return string Customer email.
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }
}