<?php
/* --------------------------------------------------------------
   DeleteAddonValuesAfterDeletionOfACustomerEventListener.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\EventListeners;

use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\DeletionOfCustomerAddonValueFailedException;

/**
 * Class DeleteAddonValuesAfterDeletionOfACustomerEventListener
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App\EventHandler
 */
class DeleteAddonValuesAfterDeletionOfACustomerEventListener
{
    private CustomerAddonValueWriteService $writeService;
    
    
    /**
     * @param CustomerAddonValueWriteService $writeService
     */
    public function __construct(CustomerAddonValueWriteService $writeService)
    {
        $this->writeService = $writeService;
    }
    
    
    /**
     * @param CustomerDeleted $event
     *
     * @return void
     * @throws DeletionOfCustomerAddonValueFailedException
     */
    public function __invoke(CustomerDeleted $event): void
    {
        $this->writeService->deleteCustomerAddonValuesByCustomerIds($event->customerId()->value());
    }
}