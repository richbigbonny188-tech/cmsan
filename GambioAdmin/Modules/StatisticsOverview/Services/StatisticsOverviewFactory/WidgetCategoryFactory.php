<?php
/*--------------------------------------------------------------
   WidgetCategoryFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetCategory;

/**
 * Class representing widget's category factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory
 */
class WidgetCategoryFactory
{
    /**
     * Return customers' category.
     *
     * @return WidgetCategory Category.
     */
    public function createForCustomers(): WidgetCategory
    {
        return WidgetCategory::create(WidgetCategory::CUSTOMERS);
    }
    
    
    /**
     * Return orders' category.
     *
     * @return WidgetCategory Category.
     */
    public function createForOrders(): WidgetCategory
    {
        return WidgetCategory::create(WidgetCategory::ORDERS);
    }
    
    
    /**
     * Return system category.
     *
     * @return WidgetCategory Category.
     */
    public function createForSystem(): WidgetCategory
    {
        return WidgetCategory::create(WidgetCategory::SYSTEM);
    }
}