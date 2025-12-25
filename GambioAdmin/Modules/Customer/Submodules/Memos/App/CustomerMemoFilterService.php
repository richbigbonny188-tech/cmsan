<?php
/* --------------------------------------------------------------
  CustomerMemoFilterService.php 2022-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterFactory;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoFilterService as CustomerMemoFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoRepository;

/**
 * Class CustomerMemoFilterService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App
 */
class CustomerMemoFilterService implements CustomerMemoFilterServiceInterface
{
    private CustomerMemoRepository    $repository;
    private CustomerMemoFactory       $customerMemoFactory;
    private CustomerMemoFilterFactory $customerMemoFilterFactory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\Memos\App\CustomerMemoRepository $repository
     * @param CustomerMemoFactory                                                        $customerMemoFactory
     * @param CustomerMemoFilterFactory                                                  $customerMemoFilterFactory
     */
    public function __construct(
        CustomerMemoRepository    $repository,
        CustomerMemoFactory       $customerMemoFactory,
        CustomerMemoFilterFactory $customerMemoFilterFactory
    ) {
        $this->repository                = $repository;
        $this->customerMemoFactory       = $customerMemoFactory;
        $this->customerMemoFilterFactory = $customerMemoFilterFactory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCustomerMemos(
        int     $customerId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): CustomerMemos {
        
        return $this->repository->filterCustomerMemos($this->customerMemoFactory->createCustomerId($customerId),
                                                      $this->customerMemoFilterFactory->createFilters($filters),
                                                      $this->customerMemoFilterFactory->createSorting($sorting),
                                                      $this->customerMemoFilterFactory->createPagination($limit,
                                                                                                         $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerMemosTotalCount(int $customerId, array $filters): int
    {
        return $this->repository->getCustomerMemosTotalCount($this->customerMemoFactory->createCustomerId($customerId),
                                                             $this->customerMemoFilterFactory->createFilters($filters));
    }
}