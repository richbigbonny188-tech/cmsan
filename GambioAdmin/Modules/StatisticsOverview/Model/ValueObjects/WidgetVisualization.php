<?php
/*--------------------------------------------------------------
   WidgetVisualization.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects;

use Webmozart\Assert\Assert;

/**
 * Class representing widget's visualization.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects
 */
class WidgetVisualization
{
    /**
     * Area chart.
     */
    const AREA_CHART = "areaChart";
    
    /**
     * Bar chart.
     */
    const BAR_CHART = "barChart";
    
    /**
     * Pie chart.
     */
    const PIE_CHART = "pieChart";
    
    /**
     * Stacked columns chart.
     */
    const STACKED_COLUMNS_CHART = "stackedColumnsChart";
    
    /**
     * Table.
     */
    const TABLE = "table";
    
    /**
     * Text.
     */
    const TEXT = "text";
    
    /**
     * Treemap chart.
     */
    const TREEMAP_CHART = "treemapChart";
    
    /**
     * Donut chart.
     */
    const DONUT_CHART = "donutChart";
    
    /**
     * Radial bar chart.
     */
    const RADIAL_BAR_CHART = "radialBarChart";
    
    /**
     * Two-Sided bar chart.
     */
    const TWO_SIDED_BAR_CHART = "twoSidedBarChart";
    
    /**
     * Valid values.
     */
    private const VALID_VALUES = [
        self::AREA_CHART,
        self::BAR_CHART,
        self::PIE_CHART,
        self::STACKED_COLUMNS_CHART,
        self::TABLE,
        self::TEXT,
        self::TREEMAP_CHART,
        self::DONUT_CHART,
        self::RADIAL_BAR_CHART,
        self::TWO_SIDED_BAR_CHART
    ];
    
    /**
     * Value.
     *
     * @var string
     */
    private $value;
    
    
    /**
     * Constructor.
     *
     * @param string $value Value.
     */
    private function __construct(string $value)
    {
        Assert::stringNotEmpty($value);
        Assert::inArray($value, self::VALID_VALUES);
        
        $this->value = $value;
    }
    
    
    /**
     * Create instance.
     *
     * @param string $visualization Value.
     *
     * @return WidgetVisualization Instance.
     */
    public static function create(string $visualization): self
    {
        return new self($visualization);
    }
    
    
    /**
     * Return value.
     *
     * @return string Value.
     */
    public function value(): string
    {
        return $this->value;
    }
}