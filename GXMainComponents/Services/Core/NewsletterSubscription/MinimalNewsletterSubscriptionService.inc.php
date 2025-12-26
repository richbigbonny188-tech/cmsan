<?php
/* --------------------------------------------------------------
   MinimalNewsletterSubscriptionService.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface MinimalNewsletterSubscriptionService
 *
 * @category   System
 * @package    NewsletterSubscription
 */
class MinimalNewsletterSubscriptionService implements NewsletterSubscriptionServiceInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * MinimalNewsletterSubscriptionService constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Unsubscribes a customer from the newsletter.
     *
     * @param \CustomerEmailInterface $email
     */
    public function unsubscribe(CustomerEmailInterface $email)
    {
        $this->db->delete('newsletter_recipients', ['customers_email_address' => (string)$email]);
    }
    
    
    /**
     * Gets the newsletter subscription data by a given customer ID.
     *
     * @param \CustomerEmailInterface $email
     *
     * @return bool|\NewsletterSubscription
     *
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function getSubscriptionByCustomerEmail(CustomerEmailInterface $email)
    {
        $rawData = $this->db->get_where('newsletter_recipients', ['customers_email_address' => (string)$email])
            ->row_array();
        
        if (empty($rawData)) {
            return null;
        }
        
        return $this->createNewsletterSubscriptionFromArray($rawData);
    }
    
    
    /**
     * Creates a newsletter subscription instance from raw data from the database.
     *
     * @param array $newsletterSubscriptionData
     *
     * @return bool|\NewsletterSubscription
     *
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    protected function createNewsletterSubscriptionFromArray(array $newsletterSubscriptionData)
    {
        return MainFactory::create(NewsletterSubscription::class,
                                   new IdType($newsletterSubscriptionData['mail_id']),
                                   new CustomerEmail($newsletterSubscriptionData['customers_email_address']),
                                   new IdType($newsletterSubscriptionData['customers_id']),
                                   new IntType($newsletterSubscriptionData['customers_status']),
                                   new StringType($newsletterSubscriptionData['customers_firstname']),
                                   new StringType($newsletterSubscriptionData['customers_lastname']),
                                   new IntType($newsletterSubscriptionData['mail_status']),
                                   new StringType($newsletterSubscriptionData['mail_key']),
                                   new DateTime($newsletterSubscriptionData['date_added']),
                                   new StringType($newsletterSubscriptionData['ip_address'] ?? ''));
    }
}