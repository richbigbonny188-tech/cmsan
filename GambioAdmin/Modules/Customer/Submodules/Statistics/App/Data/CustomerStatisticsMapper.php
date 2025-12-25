<?php
/*--------------------------------------------------------------
   CustomerStatisticsMapper.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data;

use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\CustomerStatistic;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\CustomerStatisticsFactory;
use Webmozart\Assert\Assert;

/**
 * Class CustomerStatisticsMapper
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\App\Data
 */
class CustomerStatisticsMapper extends CustomerStatisticsFactory
{
    /**
     * @param CustomerId $customerId
     * @param array      $customerData
     *
     * @return CustomerStatistic
     */
    public function mapCustomerStatistic(CustomerId $customerId, array $customerData): CustomerStatistic
    {
        Assert::keyExists($customerData, 'order_total');
        Assert::keyExists($customerData, 'order_count');
        
        [
            'order_total' => $total,
            'order_count' => $count,
        ] = $customerData;
        
        return CustomerStatistic::create($customerId, (int)$count, (float)$total);
    }
}