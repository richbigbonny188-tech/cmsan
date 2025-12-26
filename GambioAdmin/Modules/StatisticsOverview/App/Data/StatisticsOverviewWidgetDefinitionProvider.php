<?php
/*--------------------------------------------------------------
   StatisticsOverviewWidgetDefinitionProvider.php 2022-01-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\App\Data;

use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetDefinitions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetCategory;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetId;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Webmozart\Assert\Assert;

/**
 * Class representing a service for collecting and providing all available widget.
 *
 * This class acts as a registry which provides a convenient function to register a new widget.
 * The registered widget can either be retrieved by its ID.
 * You can also return a series of widgets with the same category.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\App\Data
 */
class StatisticsOverviewWidgetDefinitionProvider
{
    /**
     * Registered widgets.
     *
     * @var WidgetDefinition[]
     */
    private $definitions;
    
    /**
     * Factory.
     *
     * @var StatisticsOverviewFactory
     */
    private $factory;
    
    
    /**
     * Constructor.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     */
    public function __construct(StatisticsOverviewFactory $factory)
    {
        $this->factory     = $factory;
        $this->definitions = [];
    }
    
    
    /**
     * Return widget definitions by category.
     *
     * @param WidgetCategory $category Widget category.
     *
     * @return WidgetDefinitions All widgets found for the provided category.
     */
    public function getByCategory(WidgetCategory $category): WidgetDefinitions
    {
        return $this->factory->createWidgetDefinitions(...
            array_values(array_filter($this->definitions, function (WidgetDefinition $definition) use ($category) {
                return $definition->category()->value() === $category->value();
            })));
    }
    
    
    /**
     * Return widget definition by ID.
     *
     * Use this method to retrieve a specific widget.
     *
     * @param WidgetId $id Widget ID.
     *
     * @return WidgetDefinition Widget with the provided ID.
     */
    public function getById(WidgetId $id): WidgetDefinition
    {
        Assert::keyExists($this->definitions, $id->value());
        
        return $this->definitions[$id->value()];
    }
    
    
    /**
     * Register widget definition.
     *
     * Use this method to add your widget to the registry.
     *
     * @param WidgetDefinition $widget Widget.
     */
    public function register(WidgetDefinition $widget): void
    {
        $id = $widget->id()->value();
        
        if (!array_key_exists($id, $this->definitions)) {
            $this->definitions[$id] = $widget;
        }
    }
}