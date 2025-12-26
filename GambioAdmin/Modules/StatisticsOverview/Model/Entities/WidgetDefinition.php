<?php
/*--------------------------------------------------------------
   WidgetDefinition.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Entities;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetNames;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetCategory;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetId;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetVisualization;

/**
 * Class representing a widget definition.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities
 */
abstract class WidgetDefinition
{
    /**
     * German language code.
     */
    protected const LANGUAGE_CODE_GERMAN = 'de';
    
    /**
     * English language code.
     */
    protected const LANGUAGE_CODE_ENGLISH = 'en';
    
    /**
     * German language ID.
     */
    protected const LANGUAGE_ID_GERMAN = 2;
    
    /**
     * Date and time format for the beginning of a time range used for database queries.
     */
    protected const DATA_QUERY_TIMESPAN_FORMAT_START = 'Y-m-d 00:00:00';
    
    /**
     * Date and time format for the end of a time range used for database queries.
     */
    protected const DATA_QUERY_TIMESPAN_FORMAT_END = 'Y-m-d 23:59:59';
    
    /**
     * Order status IDs to be excluded from SQL queries.
     */
    protected const EXCLUDED_ORDER_STATUS_IDS = [99];
    
    /**
     * ID.
     *
     * @var WidgetId
     */
    private $id;
    
    /**
     * Multilingual titles.
     *
     * @var WidgetNames
     */
    private $names;
    
    /**
     * Category.
     *
     * @var WidgetCategory
     */
    private $category;
    
    /**
     * Visualization type.
     *
     * @var WidgetVisualization
     */
    private $visualization;
    
    /**
     * Options.
     *
     * @var WidgetOptions
     */
    private $options;
    
    
    /**
     * Constructor.
     *
     * @param WidgetId            $id            ID.
     * @param WidgetNames         $names         Multilingual titles.
     * @param WidgetCategory      $category      Category.
     * @param WidgetVisualization $visualization Visualization type.
     * @param WidgetOptions       $options       Options.
     */
    protected function __construct(
        WidgetId $id,
        WidgetNames $names,
        WidgetCategory $category,
        WidgetVisualization $visualization,
        WidgetOptions $options
    ) {
        $this->id            = $id;
        $this->names         = $names;
        $this->category      = $category;
        $this->visualization = $visualization;
        $this->options       = $options;
    }
    
    
    /**
     * Return ID.
     *
     * @return WidgetId ID.
     */
    public function id(): WidgetId
    {
        return $this->id;
    }
    
    
    /**
     * Return multilingual titles.
     *
     * @return WidgetNames Multilingual titles.
     */
    public function names(): WidgetNames
    {
        return $this->names;
    }
    
    
    /**
     * Return category.
     *
     * @return WidgetCategory Category.
     */
    public function category(): WidgetCategory
    {
        return $this->category;
    }
    
    
    /**
     * Return visualization type.
     *
     * @return WidgetVisualization Visualization type.
     */
    public function visualization(): WidgetVisualization
    {
        return $this->visualization;
    }
    
    
    /**
     * Return options.
     *
     * @return WidgetOptions Options.
     */
    public function options(): WidgetOptions
    {
        return $this->options;
    }
    
    
    /**
     * Return data.
     *
     * @param WidgetOptions $options Widget's options.
     *
     * @return WidgetData Data.
     */
    abstract public function data(WidgetOptions $options): WidgetData;
}