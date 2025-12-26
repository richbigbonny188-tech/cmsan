<?php

/* --------------------------------------------------------------
   WithdrawalOrderInterface.inc.php 2017-10-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface WithdrawalOrderInterface
 *
 * @category   System
 * @package    Withdrawal
 * @subpackage ValueObjects
 */
interface WithdrawalOrderInterface
{
    /**
     * Returns the order id.
     *
     * @return int The order id.
     */
    public function getOrderId();
    
    
    /**
     * Returns the customer id.
     *
     * @return int The customer id.
     */
    public function getCustomerId();
    
    
    /**
     * Returns the customer gender.
     *
     * @return string The customer gender.
     */
    public function getCustomerGender();
    
    
    /**
     * Returns the customer first name.
     *
     * @return string The customer first name.
     */
    public function getCustomerFirstName();
    
    
    /**
     * Returns the customer last name.
     *
     * @return string The customer last name.
     */
    public function getCustomerLastName();
    
    
    /**
     * Returns the customer street address.
     *
     * @return string The customer street address.
     */
    public function getCustomerStreetAddress();
    
    
    /**
     * Returns the customer post code.
     *
     * @return int The customer post code.
     */
    public function getCustomerPostCode();
    
    
    /**
     * Returns the customer city.
     *
     * @return string The customer city.
     */
    public function getCustomerCity();
    
    
    /**
     * Returns the customer country.
     *
     * @return string The customer country.
     */
    public function getCustomerCountry();
    
    
    /**
     * Returns the customer Email.
     *
     * @return string The customer Email.
     */
    public function getCustomerEmail();
    
    
    /**
     * Returns the order datetime.
     *
     * @return \DateTime
     */
    public function getOrderDate();
    
    
    /**
     * Returns the delivery datetime.
     *
     * @return \DateTime
     */
    public function getDeliveryDate();
}