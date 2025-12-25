<?php
/*--------------------------------------------------------------
   CustomerStatistic.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\Model;

use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\ValueObjects\CustomerId;

/**
 * Class CustomerStatistic
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\Model
 */
class CustomerStatistic
{
    private CustomerId $customerId;
    private int        $numberOfOrders;
    private float      $totalSpending;
    private float      $averageSpending;
    
    
    /**
     * @param CustomerId $customerId
     * @param int        $numberOfOrders
     * @param float      $totalSpending
     * @param float      $averageSpending
     */
    private function __construct(
        CustomerId $customerId,
        int        $numberOfOrders,
        float      $totalSpending,
        float      $averageSpending
    ) {
        $this->customerId      = $customerId;
        $this->numberOfOrders  = $numberOfOrders;
        $this->totalSpending   = $totalSpending;
        $this->averageSpending = $averageSpending;
    }
    
    
    /**
     * @param CustomerId $customerId
     * @param int        $numberOfOrders
     * @param float      $totalSpending
     *
     * @return CustomerStatistic
     */
    public static function create(
        CustomerId $customerId,
        int        $numberOfOrders,
        float      $totalSpending
    ): CustomerStatistic {
    
        $avg = $numberOfOrders === 0 ? 0 : $totalSpending / $numberOfOrders;
        $avg = round($avg, 2);
        
        return new static($customerId, $numberOfOrders, $totalSpending, $avg);
    }
    
    
    /**
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * @return int
     */
    public function numberOfOrders(): int
    {
        return $this->numberOfOrders;
    }
    
    
    /**
     * @return float
     */
    public function totalSpending(): float
    {
        return $this->totalSpending;
    }
    
    
    /**
     * @return float
     */
    public function averageSpending(): float
    {
        return $this->averageSpending;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'customerId'      => $this->customerId(),
            'numberOfOrders'  => $this->numberOfOrders(),
            'totalSpending'   => $this->totalSpending(),
            'averageSpending' => $this->averageSpending(),
        ];
    }
}