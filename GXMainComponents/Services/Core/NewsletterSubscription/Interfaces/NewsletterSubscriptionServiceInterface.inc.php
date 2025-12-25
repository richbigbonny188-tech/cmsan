<?php
/* --------------------------------------------------------------
   NewsletterSubscriptionServiceInterface.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface NewsletterSubscriptionServiceInterface
 *
 * @category   System
 * @package    NewsletterSubscription
 * @subpackage Interfaces
 */
interface NewsletterSubscriptionServiceInterface
{
    /**
     * Unsubscribes a customer from the newsletter.
     *
     * @param \CustomerEmailInterface $email
     */
    public function unsubscribe(CustomerEmailInterface $email);
    
    
    /**
     * Gets the newsletter subscription data by a given customer ID.
     *
     * @param \CustomerEmailInterface $email
     *
     * @return bool|\NewsletterSubscription
     */
    public function getSubscriptionByCustomerEmail(CustomerEmailInterface $email);
}