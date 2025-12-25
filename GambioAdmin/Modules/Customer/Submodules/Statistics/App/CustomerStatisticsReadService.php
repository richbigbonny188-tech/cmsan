<?php
/*--------------------------------------------------------------
   CustomerStatisticsReadService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\App;

use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\CustomerStatistic;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsFactory;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsReadService as CustomerStatisticsReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsRepository as CustomerStatisticsRepositoryInterface;

/**
 * Class CustomerStatisticsReadService
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\App
 */
class CustomerStatisticsReadService implements CustomerStatisticsReadServiceInterface
{
    private CustomerStatisticsFactory             $factory;
    private CustomerStatisticsRepositoryInterface $repository;
    
    
    /**
     * @param CustomerStatisticsFactory             $factory
     * @param CustomerStatisticsRepositoryInterface $repository
     */
    public function __construct(
        CustomerStatisticsFactory             $factory,
        CustomerStatisticsRepositoryInterface $repository
    ) {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerStatistics(int $customerId): CustomerStatistic
    {
        $customerId = $this->factory->createCustomerId($customerId);
        
        return $this->repository->getCustomerStatistics($customerId);
    }
}