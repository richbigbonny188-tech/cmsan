<?php
/* --------------------------------------------------------------
   NewsletterSubscriptionInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface NewsletterSubscriptionInterface
 *
 * @category   System
 * @package    NewsletterSubscription
 * @subpackage Interfaces
 */
interface NewsletterSubscriptionInterface
{
    /**
     * @return int
     */
    public function getId();
    
    
    /**
     * @return string
     */
    public function getEmail();
    
    
    /**
     * @return int
     */
    public function getCustomerId();
    
    
    /**
     * @return int
     */
    public function getCustomerStatus();
    
    
    /**
     * @return string
     */
    public function getFirstName();
    
    
    /**
     * @return string
     */
    public function getLastName();
    
    
    /**
     * @return int
     */
    public function getMailStatus();
    
    
    /**
     * @return string
     */
    public function getMailKey();
    
    
    /**
     * @return \DateTime
     */
    public function getSubscriptionDate();
    
    
    /**
     * @return string
     */
    public function getIpAddress();
}