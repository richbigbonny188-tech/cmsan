<?php
/*--------------------------------------------------------------------------------------------------
    StatisticsResultFactory.php 2021-09-28
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\Factories;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Categories;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummarizedDataItems;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Result;

/**
 *
 */
class StatisticsResultFactory
{
    
    /**
     * @return Result
     */
    public function createEmptyStatisticsResult(): Result
    {
        return new Result(new SummarizedDataItems(), new Categories());
    }
}