<?php
/*--------------------------------------------------------------
   CustomerFilterService.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterService as CustomerFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository;

/**
 * Class CustomerFilterService
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerFilterService implements CustomerFilterServiceInterface
{
    private CustomerRepository    $repository;
    private CustomerFilterFactory $factory;
    
    
    /**
     * @param CustomerRepository    $repository
     * @param \Gambio\Admin\Modules\Customer\Services\CustomerFilterFactory $factory
     */
    public function __construct(CustomerRepository $repository, CustomerFilterFactory $factory)
    {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCustomers(
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): Customers {
        
        return $this->repository->filterCustomers($this->factory->createFilters($filters),
                                                  $this->factory->createSorting($sorting),
                                                  $this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomersTotalCount(array $filters): int
    {
        return $this->repository->getCustomersTotalCount($this->factory->createFilters($filters));
    }
}