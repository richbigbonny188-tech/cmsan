<?php
/*--------------------------------------------------------------
   CustomerSearchService.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Services\CustomerFilterFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository as CustomerRepositoryInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerSearchService as CustomerSearchServiceInterface;

/**
 * Class CustomerSearchService
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerSearchService implements CustomerSearchServiceInterface
{
    private CustomerFilterFactory       $factory;
    private CustomerRepositoryInterface $repository;
    
    
    /**
     * @param CustomerFilterFactory       $factory
     * @param CustomerRepositoryInterface $repository
     */
    public function __construct(CustomerFilterFactory $factory, CustomerRepositoryInterface $repository)
    {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function searchCustomers(string $searchTerm, ?string $sorting = null, int $limit = 25, int $offset = 0): Customers
    {
        return $this->repository->searchCustomers($this->factory->createSearch($searchTerm),
                                                  $this->factory->createSorting($sorting),
                                                  $this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSearchedCustomerTotalCount(string $searchTerm): int
    {
        return $this->repository->getSearchedCustomerTotalCount($this->factory->createSearch($searchTerm));
    }
}