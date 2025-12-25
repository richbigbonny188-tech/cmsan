<?php
/*--------------------------------------------------------------
   OverviewWidget.php 2022-05-13
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
 * Class representing a widget.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities
 */
class OverviewWidget
{
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
     * Data.
     *
     * @var WidgetData
     */
    private $data;
    
    
    /**
     * Constructor.
     *
     * @param WidgetId            $id            ID.
     * @param WidgetNames         $names         Multilingual titles.
     * @param WidgetCategory      $category      Category.
     * @param WidgetVisualization $visualization Visualization type.
     * @param WidgetOptions       $options       Options.
     * @param WidgetData          $data          Data.
     */
    private function __construct(
        WidgetId $id,
        WidgetNames $names,
        WidgetCategory $category,
        WidgetVisualization $visualization,
        WidgetOptions $options,
        WidgetData $data
    ) {
        $this->id            = $id;
        $this->names         = $names;
        $this->category      = $category;
        $this->visualization = $visualization;
        $this->options       = $options;
        $this->data          = $data;
    }
    
    
    /**
     * Create instance.
     *
     * @param WidgetId            $id            ID.
     * @param WidgetNames         $names         Multilingual titles.
     * @param WidgetCategory      $category      Category.
     * @param WidgetVisualization $visualization Visualization type.
     * @param WidgetOptions       $options       Options.
     * @param WidgetData          $data          Data.
     *
     * @return OverviewWidget Instance.
     */
    public static function create(
        WidgetId $id,
        WidgetNames $names,
        WidgetCategory $category,
        WidgetVisualization $visualization,
        WidgetOptions $options,
        WidgetData $data
    ): self {
        return new self($id, $names, $category, $visualization, $options, $data);
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
     * @return WidgetData Data.
     */
    public function data(): WidgetData
    {
        return $this->data;
    }
}