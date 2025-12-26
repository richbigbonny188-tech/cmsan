<?php
/*--------------------------------------------------------------
   WidgetVisualizationFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetVisualization;

/**
 * Class representing widget visualization factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory
 */
class WidgetVisualizationFactory
{
    /**
     * Return area chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createAreaChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::AREA_CHART);
    }
    
    
    /**
     * Return bar chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createBarChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::BAR_CHART);
    }
    
    
    /**
     * Return pie chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createPieChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::PIE_CHART);
    }
    
    
    /**
     * Return stacked columns chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createStackedColumnsChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::STACKED_COLUMNS_CHART);
    }
    
    
    /**
     * Return table visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createTable(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::TABLE);
    }
    
    
    /**
     * Return text visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createText(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::TEXT);
    }
    
    
    /**
     * Return treemap chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createTreemapChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::TREEMAP_CHART);
    }
    
    
    /**
     * Return radial bar chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createRadialBarChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::RADIAL_BAR_CHART);
    }
    
    
    /**
     * Return two-sided bar chart visualization.
     *
     * @return WidgetVisualization Visualization.
     */
    public function createTwoSidedBarChart(): WidgetVisualization
    {
        return WidgetVisualization::create(WidgetVisualization::TWO_SIDED_BAR_CHART);
    }
}