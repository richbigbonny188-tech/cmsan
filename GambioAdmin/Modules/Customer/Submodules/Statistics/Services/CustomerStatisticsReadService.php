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

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\Services;

use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\CustomerStatistic;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\Exceptions\CustomerNotFoundException;

/**
 * Interface CustomerStatisticsReadService
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\Services
 */
interface CustomerStatisticsReadService
{
    /**
     * @param int $customerId
     *
     * @return CustomerStatistic
     * @throws CustomerNotFoundException
     */
    public function getCustomerStatistics(int $customerId): CustomerStatistic;
}