<?php
/*--------------------------------------------------------------
   StatisticsOverviewWidgetOptionsStorage.php 2022-06-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\App\Data;

use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetId;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\OptionUpdateSet;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepository;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;

/**
 * Class representing the storage for widget's configuration values.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\App\Data
 */
class StatisticsOverviewWidgetOptionsStorage
{
    /**
     * Storage namespace.
     */
    private const NAMESPACE = 'modules/gambio/statistics/overview/widget_options';
    
    
    /**
     * Repository.
     *
     * @var ConfigurationStorageRepository
     */
    private $repository;
    
    /**
     * Factory.
     *
     * @var StatisticsOverviewFactory
     */
    private $factory;
    
    
    /**
     * Constructor.
     *
     * @param ConfigurationStorageRepositoryBuilder $builder Storage builder.
     * @param StatisticsOverviewFactory             $factory Factory.
     */
    public function __construct(ConfigurationStorageRepositoryBuilder $builder, StatisticsOverviewFactory $factory)
    {
        $this->repository = $builder->build(self::NAMESPACE);
        $this->factory    = $factory;
    }
    
    
    /**
     * Return widget option update set by widget ID.
     *
     * @param WidgetId $id Widget's ID.
     *
     * @return OptionUpdateSet Widget's configuration values as an update set.
     */
    public function getById(WidgetId $id): OptionUpdateSet
    {
        $data = $this->repository->get($id->value());
        $data = $data === '' ? [] : json_decode($data, true);
        
        return $this->factory->useOptions()->createUpdateSet($data);
    }
    
    
    /**
     * Save widget options.
     *
     * @param WidgetId        $id        Widget's ID.
     * @param OptionUpdateSet $updateSet Widget's configuration values as an update set.
     */
    public function save(WidgetId $id, OptionUpdateSet $updateSet): void
    {
        $currentOptions = json_decode($this->repository->get($id->value()), true);
        $mergedOptions  = array_merge($currentOptions ?? [], $updateSet->toArray());
        $this->repository->set($id->value(), json_encode($mergedOptions));
    }
}