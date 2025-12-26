<?php
/*--------------------------------------------------------------
   CustomerNewsletterReadService.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Services;

use Gambio\Admin\Modules\Newsletter\Model\Collections\CustomerIds;

/**
 * Interface CustomerNewsletterReadService
 *
 * @package Gambio\Admin\Modules\Newsletter\Services
 */
interface CustomerNewsletterReadService
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
     * @param int $customerId
     *
     * @return bool
     */
    public function isCustomerSubscribed(int $customerId): bool;
}