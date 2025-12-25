<?php
/*--------------------------------------------------------------
   CustomerNewsletterRepository.php 2022-11-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Services;

use Gambio\Admin\Modules\Newsletter\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;

/**
 * Interface CustomerNewsletterRepository
 *
 * @package Gambio\Admin\Modules\Newsletter\Services
 */
interface CustomerNewsletterRepository
{
    /**
     * Returns all for newsletters subscribed customers.
     *
     * @return CustomerIds
     */
    public function getSubscribedCustomers(): CustomerIds;
    
    
    /**
     * Checks if the given customer is subscribed for newsletters.
     *
     * @param CustomerId $customerId
     *
     * @return bool
     */
    public function isCustomerSubscribed(CustomerId $customerId): bool;
    
    
    /**
     * Subscribes the given customer from newsletters.
     *
     * @param CustomerId $customerId
     * @param CustomerId $adminId
     *
     * @return void
     */
    public function subscribe(CustomerId $customerId, CustomerId $adminId): void;
    
    
    /**
     * Unsubscribes the given customer from newsletters.
     *
     * @param CustomerId $customerId
     *
     * @return void
     */
    public function unsubscribe(CustomerId $customerId): void;
}