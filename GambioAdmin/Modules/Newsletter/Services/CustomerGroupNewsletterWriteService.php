<?php
/* --------------------------------------------------------------
   CustomerGroupNewsletterWriteService.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Services;


/**
 * Interface CustomerGroupNewsletterWriteService
 *
 * @package Gambio\Admin\Modules\Newsletter\Services
 */
interface CustomerGroupNewsletterWriteService
{
    
    /**
     * Updates the `customer_status` field in the `newsletter_recipients` table
     *
     * @param int $customerId
     * @param int $customerGroupId
     *
     * @return void
     */
    public function changeCustomerGroup(int $customerId, int $customerGroupId): void;
}