<?php
/*--------------------------------------------------------------
   DashboardStatisticsRepository.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Data;

use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\ConversionDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\OrdersDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\SalesDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Timespan;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\VisitorsDataProviderResult;

class DashboardStatisticsRepository
{
    /**
     * @var DashboardStatisticsReader
     */
    private $reader;
    
    /**
     * @var DashboardStatisticsMapper
     */
    private $mapper;
    
    
    /**
     * Constructor.
     */
    public function __construct(DashboardStatisticsReader $reader, DashboardStatisticsMapper $mapper)
    {
        $this->reader = $reader;
        $this->mapper = $mapper;
    }
    
    
    /**
     * Return orders for provided time span.
     */
    public function getOrdersByTimespan(Timespan $timespan): OrdersDataProviderResult
    {
        return $this->mapper->mapOrders($this->reader->ordersByTimespan($timespan));
    }
    
    
    /**
     * Return sales for provided time span.
     */
    public function getSalesByTimespan(Timespan $timespan): SalesDataProviderResult
    {
        return $this->mapper->mapSales($this->reader->salesByTimespan($timespan));
    }
    
    
    /**
     * Return visitors for provided time span.
     */
    public function getVisitorsByTimespan(Timespan $timespan): VisitorsDataProviderResult
    {
        return $this->mapper->mapVisitors($this->reader->visitorsByTimespan($timespan));
    }
    
    
    /**
     * Return conversion rates for provided time span.
     */
    public function getConversionRatesByTimespan(Timespan $timespan): ConversionDataProviderResult
    {
        return $this->mapper->mapConversionRates($this->reader->conversionRatesByTimespan($timespan));
    }
}