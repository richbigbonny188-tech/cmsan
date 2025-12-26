<?php
/*--------------------------------------------------------------
   DeleteCustomerMemosOnCustomerDeleted.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App\EventListeners;

use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoRepository;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\DeletionOfCustomerMemoFailedException;

/**
 * Class DeleteCustomerMemosOnCustomerDeleted
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\EventListeners
 */
class DeleteCustomerMemosOnCustomerDeleted
{
    private CustomerMemoRepository $repository;
    private CustomerMemoFactory    $factory;
    
    
    /**
     * @param CustomerMemoRepository $repository
     * @param CustomerMemoFactory    $factory
     */
    public function __construct(CustomerMemoRepository $repository, CustomerMemoFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @param CustomerDeleted $event
     *
     * @return void
     * @throws DeletionOfCustomerMemoFailedException
     */
    public function __invoke(CustomerDeleted $event): void
    {
        $customerId = $event->customerId()->value();
        $customerId = $this->factory->createCustomerId($customerId);
        
        $this->repository->deleteCustomerMemosByCustomerIds($customerId);
    }
}