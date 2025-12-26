<?php
/* --------------------------------------------------------------
   ChangeNewsletterCustomerGroupEventListener.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);


namespace Gambio\Admin\Modules\Newsletter\App\EventListeners;


use Gambio\Admin\Modules\Customer\Model\Events\CustomersCustomerGroupUpdated;
use Gambio\Admin\Modules\Newsletter\Services\CustomerGroupNewsletterWriteService;

/**
 * Class ChangeNewsletterCustomerGroupEventListener
 *
 * @package Gambio\Admin\Modules\Newsletter\App\EventListeners
 */
class ChangeNewsletterCustomerGroupEventListener
{
    
    /**
     * @param CustomerGroupNewsletterWriteService $writeService
     */
    public function __construct(private CustomerGroupNewsletterWriteService $writeService)
    {
    }
    
    
    /**
     * @param CustomersCustomerGroupUpdated $event
     *
     * @return void
     */
    public function __invoke(CustomersCustomerGroupUpdated $event): void
    {
        $this->writeService->changeCustomerGroup($event->customerId()->value(), $event->customerGroup()->id());
    }
}