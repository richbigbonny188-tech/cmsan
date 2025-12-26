<?php
/*--------------------------------------------------------------
   PredefinedOptionFactory.php 2022-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\CheckboxOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\NumberOption;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\MaxEntriesOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\SortOrderOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\TimespanOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\VisibilityOptionFactory;

/**
 * Class representing a predefined option factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory
 */
class PredefinedOptionFactory
{
    /**
     * Return time span dropdown.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return DropdownOption Time span dropdown.
     */
    public function createTimespanDropdown(StatisticsOverviewFactory $factory): DropdownOption
    {
        return TimespanOptionFactory::create($factory);
    }
    
    
    /**
     * Return time span dropdown including today.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return DropdownOption Time span dropdown.
     */
    public function createTimespanDropdownIncludingToday(StatisticsOverviewFactory $factory): DropdownOption
    {
        return TimespanOptionFactory::createIncludingToday($factory);
    }
    
    
    /**
     * Return maximum entries dropdown.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return DropdownOption Maximum entries dropdown.
     */
    public function createMaxEntriesDropdown(StatisticsOverviewFactory $factory): DropdownOption
    {
        return MaxEntriesOptionFactory::create($factory);
    }
    
    
    /**
     * Return sort order number.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return NumberOption Sort order number.
     */
    public function createSortOrderNumber(StatisticsOverviewFactory $factory): NumberOption
    {
        return SortOrderOptionFactory::create($factory);
    }
    
    
    /**
     * Return visibility checkbox.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     *
     * @return CheckboxOption Visibility checkbox.
     */
    public function createVisibilityCheckbox(StatisticsOverviewFactory $factory): CheckboxOption
    {
        return VisibilityOptionFactory::create($factory);
    }
}