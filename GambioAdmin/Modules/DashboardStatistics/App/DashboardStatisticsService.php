<?php
/*--------------------------------------------------------------
   DashboardStatisticsService.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App;

use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsPreferredCategoryStorage;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsPreferredPeriodStorage;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsRepository;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Categories;
use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\SummarizedDataItems;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\DataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\SummarizableTimespan;
use Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions\InvalidPreferredCategoryException;
use Gambio\Admin\Modules\DashboardStatistics\Model\Exceptions\InvalidPreferredPeriodException;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Category;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredCategory;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\PreferredPeriod;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Result;
use Gambio\Admin\Modules\DashboardStatistics\Services\DashboardStatisticsService as DashboardStatisticsServiceInterface;
use Gambio\Admin\Modules\DashboardStatistics\Services\SummarizableTimespanFactory;

class DashboardStatisticsService implements DashboardStatisticsServiceInterface
{
    /**
     * @var SummarizableTimespanFactory
     */
    private $factory;
    
    /**
     * @var DashboardStatisticsPreferredPeriodStorage
     */
    private $preferredPeriodStorage;
    
    /**
     * @var DashboardStatisticsPreferredCategoryStorage
     */
    private $preferredCategoryStorage;
    
    /**
     * @var DashboardStatisticsRepository
     */
    private $repository;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        SummarizableTimespanFactory $factory,
        DashboardStatisticsPreferredPeriodStorage $periodStorage,
        DashboardStatisticsPreferredCategoryStorage $categoryStorage,
        DashboardStatisticsRepository $repository
    ) {
        $this->factory                  = $factory;
        $this->preferredPeriodStorage   = $periodStorage;
        $this->preferredCategoryStorage = $categoryStorage;
        $this->repository               = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getStatistics(): Result
    {
        $timespan = $this->factory->createForTheWholePeriod();
        
        $results = [
            $this->repository->getVisitorsByTimespan($timespan),
            $this->repository->getOrdersByTimespan($timespan),
            $this->repository->getSalesByTimespan($timespan),
            $this->repository->getConversionRatesByTimespan($timespan)
        ];
        
        $categories = array_map(function (DataProviderResult $result) {
            return new Category($result->name(),
                                $result->title(),
                                $result->unit(),
                                $result->style(),
                                $result->minimumFractionDigits());
        }, $results);
        
        $statistics = array_map(function (SummarizableTimespan $timespan) use ($results) {
            return $timespan->summarize(...$results);
        }, iterator_to_array($this->factory->createAll()));
        
        return new Result(new SummarizedDataItems($statistics), new Categories($categories));
    }
    
    
    /**
     * @inheritDoc
     */
    public function updatePreferredPeriod(string $period): void
    {
        try {
            $preferredPeriod = new PreferredPeriod($period);
            $this->preferredPeriodStorage->setPreferredPeriod($preferredPeriod);
        } catch (InvalidPreferredPeriodException $exception) {
            unset($exception);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPreferredPeriod(): ?PreferredPeriod
    {
        return $this->preferredPeriodStorage->getPreferredPeriod();
    }
    
    
    /**
     * @inheritDoc
     */
    public function updatePreferredCategory(string $category): void
    {
        try {
            $preferredCategory = new PreferredCategory($category);
            $this->preferredCategoryStorage->setPreferredCategory($preferredCategory);
        } catch (InvalidPreferredCategoryException $exception) {
            unset($exception);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getPreferredCategory(): ?PreferredCategory
    {
        return $this->preferredCategoryStorage->getPreferredCategory();
    }
}