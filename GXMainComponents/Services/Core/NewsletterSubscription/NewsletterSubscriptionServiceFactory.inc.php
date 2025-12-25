<?php
/* --------------------------------------------------------------
   NewsletterSubscriptionServiceFactory.inc.php 2018-05-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class NewsletterSubscriptionServiceFactory
 *
 * @category   System
 * @package    NewsletterSubscription
 */
class NewsletterSubscriptionServiceFactory implements NewsletterSubscriptionServiceFactoryInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    
    /**
     * NewsletterSubscriptionServiceFactory constructor.
     *
     * @param \CI_DB_query_builder $db
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }
    
    
    /**
     * Creates a NewsletterSubscriptionService instance.
     *
     * @return NewsletterSubscriptionServiceInterface
     */
    public function createService()
    {
        return MainFactory::create('MinimalNewsletterSubscriptionService', $this->db);
    }
}