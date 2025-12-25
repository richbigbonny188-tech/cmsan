<?php
/*--------------------------------------------------------------
   StatisticsOverviewFactory.php 2022-05-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Services;

use Gambio\Admin\Modules\Language\Model\ValueObjects\LanguageCode;
use Gambio\Admin\Modules\Language\Services\LanguageFactory;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\OverviewWidgets;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetDefinitions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetNames;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\OverviewWidget;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetCategory;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetId;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetName;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetVisualization;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetCategoryFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetVisualizationFactory;

/**
 * Class representing the domain's factory.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Services
 */
class StatisticsOverviewFactory
{
    /**
     * Widget option factory.
     *
     * @var WidgetOptionFactory
     */
    private $optionFactory;
    
    /**
     * Widget visualization factory.
     *
     * @var WidgetVisualizationFactory
     */
    private $visualizationFactory;
    
    /**
     * Widget category factory.
     *
     * @var WidgetCategoryFactory
     */
    private $categoryFactory;
    
    /**
     * Widget data factory.
     *
     * @var WidgetDataFactory
     */
    private $dataFactory;
    
    /**
     * Language factory.
     *
     * @var LanguageFactory
     */
    private $languageFactory;
    
    
    /**
     * Constructor.
     *
     * @param WidgetOptionFactory        $optionFactory        Widget option factory.
     * @param WidgetVisualizationFactory $visualizationFactory Widget visualization factory.
     * @param WidgetCategoryFactory      $categoryFactory      Widget category factory.
     * @param WidgetDataFactory          $dataFactory          Widget data factory.
     * @param LanguageFactory            $languageFactory      Language factory.
     */
    public function __construct(
        WidgetOptionFactory $optionFactory,
        WidgetVisualizationFactory $visualizationFactory,
        WidgetCategoryFactory $categoryFactory,
        WidgetDataFactory $dataFactory,
        LanguageFactory $languageFactory
    ) {
        $this->optionFactory        = $optionFactory;
        $this->visualizationFactory = $visualizationFactory;
        $this->categoryFactory      = $categoryFactory;
        $this->dataFactory          = $dataFactory;
        $this->languageFactory      = $languageFactory;
    }
    
    
    /**
     * Return option factory.
     *
     * @return WidgetOptionFactory Widget option factory.
     */
    public function useOptions(): WidgetOptionFactory
    {
        return $this->optionFactory;
    }
    
    
    /**
     * Return visualization factory.
     *
     * @return WidgetVisualizationFactory Widget visualization factory.
     */
    public function useVisualizations(): WidgetVisualizationFactory
    {
        return $this->visualizationFactory;
    }
    
    
    /**
     * Return category factory.
     *
     * @return WidgetCategoryFactory Widget category factory.
     */
    public function useCategories(): WidgetCategoryFactory
    {
        return $this->categoryFactory;
    }
    
    
    /**
     * Return data factory.
     *
     * @return WidgetDataFactory Widget data factory.
     */
    public function useData(): WidgetDataFactory
    {
        return $this->dataFactory;
    }
    
    
    /**
     * Return widget definitions.
     *
     * @param WidgetDefinition ...$widgetDefinitions Widget definitions.
     *
     * @return WidgetDefinitions Widget definitions.
     */
    public function createWidgetDefinitions(WidgetDefinition ...$widgetDefinitions): WidgetDefinitions
    {
        return WidgetDefinitions::create(...$widgetDefinitions);
    }
    
    
    /**
     * Return overview widget.
     *
     * @param WidgetId            $id            Widget's ID.
     * @param WidgetNames         $names         Widget's multilingual names.
     * @param WidgetCategory      $category      Widget's category.
     * @param WidgetVisualization $visualization Widget's visualization.
     * @param WidgetOptions       $options       Widget's options.
     * @param WidgetData          $data          Widget's data.
     *
     * @return OverviewWidget
     */
    public function createOverviewWidget(
        WidgetId $id,
        WidgetNames $names,
        WidgetCategory $category,
        WidgetVisualization $visualization,
        WidgetOptions $options,
        WidgetData $data
    ): OverviewWidget {
        return OverviewWidget::create($id, $names, $category, $visualization, $options, $data);
    }
    
    
    /**
     * Return overview widgets.
     *
     * @param OverviewWidget ...$widgets Widgets.
     *
     * @return OverviewWidgets Widgets.
     */
    public function createOverviewWidgets(OverviewWidget ...$widgets): OverviewWidgets
    {
        return OverviewWidgets::create(...$widgets);
    }
    
    
    /**
     * Return ID.
     *
     * @param string $id Widget ID.
     *
     * @return WidgetId Widget ID.
     */
    public function createId(string $id): WidgetId
    {
        return WidgetId::create($id);
    }
    
    
    /**
     * Return name.
     *
     * @param LanguageCode $languageCode Language code.
     * @param string       $name         Name.
     *
     * @return WidgetName Widget name.
     */
    public function createName(LanguageCode $languageCode, string $name): WidgetName
    {
        return WidgetName::create($languageCode, $name);
    }
    
    
    /**
     * Return multilingual names for a widget.
     *
     * @param WidgetName ...$names Multilingual names.
     *
     * @return WidgetNames Multilingual names for a widget.
     */
    public function createNames(WidgetName ...$names): WidgetNames
    {
        return WidgetNames::create(...$names);
    }
    
    
    /**
     * Return language code.
     *
     * @param string $languageCode Language code.
     *
     * @return LanguageCode Language code.
     */
    public function createLanguageCode(string $languageCode): LanguageCode
    {
        return $this->languageFactory->createLanguageCode($languageCode);
    }
}