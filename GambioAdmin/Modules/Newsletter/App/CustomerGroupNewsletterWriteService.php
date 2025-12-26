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

namespace Gambio\Admin\Modules\Newsletter\App;

use Gambio\Admin\Modules\Newsletter\Services\CustomerGroupNewsletterWriteService as CustomerGroupNewsletterWriteServiceInterface;
use Gambio\Admin\Modules\Newsletter\Services\CustomerGroupNewsletterRepository;
use Gambio\Admin\Modules\Newsletter\Services\CustomerNewsletterFactory;

/**
 * Class CustomerGroupNewsletterWriteService
 *
 * @package Gambio\Admin\Modules\Newsletter\App
 */
class CustomerGroupNewsletterWriteService implements CustomerGroupNewsletterWriteServiceInterface
{
    
    /**
     * @param CustomerGroupNewsletterRepository $repository
     * @param CustomerNewsletterFactory         $factory
     */
    public function __construct(
        private CustomerGroupNewsletterRepository $repository,
        private CustomerNewsletterFactory         $factory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function changeCustomerGroup(int $customerId, int $customerGroupId): void
    {
        $this->repository->changeCustomerGroup($this->factory->createCustomerId($customerId),
                                               $this->factory->createCustomerGroup($customerGroupId));
    }
}