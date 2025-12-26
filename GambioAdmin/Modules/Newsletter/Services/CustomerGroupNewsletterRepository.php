<?php
/* --------------------------------------------------------------
   CustomerGroupNewsletterRepository.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Services;


use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Newsletter\Model\ValueObjects\CustomerId;

/**
 * Interface CustomerGroupNewsletterRepository
 *
 * @package Gambio\Admin\Modules\Newsletter\Services
 */
interface CustomerGroupNewsletterRepository
{
    /**
     * Changes the `customer_status` in the `newsletter_recipients` table
     *
     * @param CustomerId    $customerId
     * @param CustomerGroup $customerGroup
     *
     * @return void
     */
    public function changeCustomerGroup(CustomerId $customerId, CustomerGroup $customerGroup): void;
}