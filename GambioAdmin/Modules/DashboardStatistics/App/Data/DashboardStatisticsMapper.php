<?php
/*--------------------------------------------------------------
   DashboardStatisticsMapper.php 2022-12-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Data;

use Gambio\Admin\Modules\DashboardStatistics\Model\Collections\Values;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\ConversionDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\OrdersDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\SalesDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Value;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\VisitorsDataProviderResult;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\DateString;
use Gambio\Admin\Modules\DashboardStatistics\Services\ConversionDataProviderResultFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\OrdersDataProviderResultFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\SalesDataProviderResultFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\VisitorsDataProviderResultFactory;

class DashboardStatisticsMapper
{
    /**
     * @var OrdersDataProviderResultFactory
     */
    private $ordersDataProviderResultFactory;
    
    /**
     * @var ConversionDataProviderResultFactory
     */
    private $conversionDataProviderResultFactory;
    
    /**
     * @var SalesDataProviderResultFactory
     */
    private $salesDataProviderResultFactory;
    
    /**
     * @var VisitorsDataProviderResultFactory
     */
    private $visitorsDataProviderResultFactory;
    
    
    /**
     * Constructor.
     */
    public function __construct(
        OrdersDataProviderResultFactory $ordersDataProviderResultFactory,
        ConversionDataProviderResultFactory $conversionDataProviderResultFactory,
        SalesDataProviderResultFactory $salesDataProviderResultFactory,
        VisitorsDataProviderResultFactory $visitorsDataProviderResultFactory
    ) {
        $this->ordersDataProviderResultFactory     = $ordersDataProviderResultFactory;
        $this->conversionDataProviderResultFactory = $conversionDataProviderResultFactory;
        $this->salesDataProviderResultFactory      = $salesDataProviderResultFactory;
        $this->visitorsDataProviderResultFactory   = $visitorsDataProviderResultFactory;
    }
    
    
    /**
     * Return orders.
     */
    public function mapOrders(array $orders): OrdersDataProviderResult
    {
        $values = new Values();
        
        foreach ($orders as ['orders_date' => $date, 'orders_count' => $value]) {
            $values[] = new Value(new DateString($date), (float)$value);
        }
        
        return $this->ordersDataProviderResultFactory->create($values);
    }
    
    
    /**
     * Return sales.
     */
    public function mapSales(array $sales): SalesDataProviderResult
    {
        $values = new Values();
        $taxes  = [];
        
        foreach ($sales['taxes'] as $tax) {
            $taxes[$tax['purchased_date']] = $tax['taxes'];
        }
        
        if (count($sales['orders'])) {
            foreach ($sales['orders'] as ['purchased_date' => $date, 'sales' => $value]) {
                $values[] = new Value(new DateString($date),
                                      isset($taxes[$date]) ? (float)$value - (float)$taxes[$date] : (float)$value);
            }
        }
        
        return $this->salesDataProviderResultFactory->create($values);
    }
    
    
    /**
     * Return visitors.
     */
    public function mapVisitors(array $visitors): VisitorsDataProviderResult
    {
        $values = new Values();
        
        foreach ($visitors as ['date' => $date, 'amount' => $value]) {
            $values[] = new Value(new DateString($date), (float)$value);
        }
        
        return $this->visitorsDataProviderResultFactory->create($values);
    }
    
    
    /**
     * Return conversion rates.
     */
    public function mapConversionRates(array $conversionRates): ConversionDataProviderResult
    {
        $values = new Values();
        
        foreach ($conversionRates['orders'] as ['purchased_date' => $purchaseDate, 'purchases' => $purchases]) {
            foreach ($conversionRates['visitors'] as ['visitor_date' => $visitorDate, 'visitors' => $visitors]) {
                if ($purchaseDate !== $visitorDate) {
                    continue;
                }
                
                $visitors = (int)$visitors;
                
                if ($visitors === 0) {
                    $values[] = new Value(new DateString($purchaseDate), 0.0);
                    continue;
                }
                
                $values[] = new Value(new DateString($purchaseDate), round((int)$purchases / $visitors * 100,
                                                                           2));
            }
        }
        
        return $this->conversionDataProviderResultFactory->create($values);
    }
}