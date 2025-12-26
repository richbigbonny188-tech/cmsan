<?php
/* --------------------------------------------------------------
   CustomerAddonValueFilterService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\App;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFilterFactory;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFilterService as CustomerAddonValueFilterServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueRepository;

/**
 * Class CustomerAddonValueFilterService
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\App
 */
class CustomerAddonValueFilterService implements CustomerAddonValueFilterServiceInterface
{
    private CustomerAddonValueRepository    $repository;
    private CustomerAddonValueFactory       $domainFactory;
    private CustomerAddonValueFilterFactory $filterFactory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueRepository $repository
     * @param CustomerAddonValueFactory                                                              $domainFactory
     * @param CustomerAddonValueFilterFactory                                                        $filterFactory
     */
    public function __construct(
        CustomerAddonValueRepository    $repository,
        CustomerAddonValueFactory       $domainFactory,
        CustomerAddonValueFilterFactory $filterFactory
    ) {
        $this->repository    = $repository;
        $this->domainFactory = $domainFactory;
        $this->filterFactory = $filterFactory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCustomerAddonValues(
        int     $customerId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): CustomerAddonValues {
        return $this->repository->filterCustomerAddonValues($this->domainFactory->createCustomerId($customerId),
                                                            $this->filterFactory->createFilters($filters),
                                                            $this->filterFactory->createSorting($sorting),
                                                            $this->filterFactory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerAddonValuesTotalCount(int $customerId, array $filters): int
    {
        return $this->repository->getCustomerAddonValuesTotalCount($this->domainFactory->createCustomerId($customerId),
                                                                   $this->filterFactory->createFilters($filters));
    }
}