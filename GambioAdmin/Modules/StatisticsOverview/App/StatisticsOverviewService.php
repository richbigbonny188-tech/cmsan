<?php
/*--------------------------------------------------------------
   StatisticsOverviewService.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\App;

use Gambio\Admin\Modules\StatisticsOverview\App\Data\StatisticsOverviewWidgetDefinitionProvider;
use Gambio\Admin\Modules\StatisticsOverview\App\Data\StatisticsOverviewWidgetOptionsStorage;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\OverviewWidgets;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\OverviewWidget;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetCategory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewService as StatisticsOverviewServiceInterface;
use InvalidArgumentException;

/**
 * Class representing the domain's service.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\App
 */
class StatisticsOverviewService implements StatisticsOverviewServiceInterface
{
    /**
     * Widget provider.
     *
     * @var StatisticsOverviewWidgetDefinitionProvider
     */
    private $provider;
    
    /**
     * Widget's configuration value storage.
     *
     * @var StatisticsOverviewWidgetOptionsStorage
     */
    private $storage;
    
    /**
     * Factory.
     *
     * @var StatisticsOverviewFactory
     */
    private $factory;
    
    
    /**
     * Constructor.
     *
     * @param StatisticsOverviewWidgetDefinitionProvider $provider Widget provider.
     * @param StatisticsOverviewWidgetOptionsStorage     $storage  Widget's configuration value storage.
     * @param StatisticsOverviewFactory                  $factory  Factory.
     */
    public function __construct(
        StatisticsOverviewWidgetDefinitionProvider $provider,
        StatisticsOverviewWidgetOptionsStorage     $storage,
        StatisticsOverviewFactory                  $factory
    ) {
        $this->provider = $provider;
        $this->storage  = $storage;
        $this->factory  = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getWidgetsByCategory(string $category): OverviewWidgets
    {
        switch ($category) {
            case WidgetCategory::CUSTOMERS:
                $widgetCategory = $this->factory->useCategories()->createForCustomers();
                break;
            case WidgetCategory::ORDERS:
                $widgetCategory = $this->factory->useCategories()->createForOrders();
                break;
            case WidgetCategory::SYSTEM:
                $widgetCategory = $this->factory->useCategories()->createForSystem();
                break;
            default:
                throw new InvalidArgumentException("Invalid Category '{$category}'");
        }
        
        return $this->factory->createOverviewWidgets(...
            array_map([$this, 'createWidget'],
                      iterator_to_array($this->provider->getByCategory($widgetCategory)->getIterator())));
    }
    
    
    /**
     * @inheritDoc
     */
    public function configureWidget(string $id, array $options): void
    {
        $definition = $this->provider->getById($this->factory->createId($id));
        
        $definition->options()->validateUpdateSet($options);
        
        $this->storage->save($definition->id(),
                             $this->factory->useOptions()->createUpdateSet($options));
    }
    
    
    /**
     * Return widget.
     *
     * @param WidgetDefinition $definition Widget's definition.
     *
     * @return OverviewWidget Widget instance.
     *
     * @codeCoverageIgnore This method is called by array_map (getWidgetsByCategory()) which is not recognized by
     *                     PHPUnit.
     */
    private function createWidget(WidgetDefinition $definition): OverviewWidget
    {
        $options = $this->factory->useOptions()->createOptionsFromUpdateSet($this->storage->getById($definition->id()),
                                                                            $definition->options());
        
        return $this->factory->createOverviewWidget($definition->id(),
                                                    $definition->names(),
                                                    $definition->category(),
                                                    $definition->visualization(),
                                                    $options,
                                                    $definition->data($options));
    }
}