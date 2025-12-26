<?php
/*--------------------------------------------------------------
   CustomerStatisticsRepository.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\App;

use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data\CustomerStatisticsMapper;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data\CustomerStatisticsReader;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\CustomerStatistic;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsRepository as CustomerStatisticsRepositoryInterface;

/**
 * Class CustomerStatisticsRepository
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\App
 */
class CustomerStatisticsRepository implements CustomerStatisticsRepositoryInterface
{
    private CustomerStatisticsMapper $mapper;
    private CustomerStatisticsReader $reader;
    
    
    /**
     * @param CustomerStatisticsMapper $mapper
     * @param CustomerStatisticsReader $reader
     */
    public function __construct(
        CustomerStatisticsMapper $mapper,
        CustomerStatisticsReader $reader
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerStatistics(CustomerId $customerId): CustomerStatistic
    {
        $data = $this->reader->getCustomerStatistics($customerId);
        
        return $this->mapper->mapCustomerStatistic($customerId, $data);
    }
}