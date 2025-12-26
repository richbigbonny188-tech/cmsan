<?php
/*--------------------------------------------------------------------
 ProductOptionsStockUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\Events;


use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;

/**
 * Class ProductOptionsStockUpdated
 *
 * @package Gambio\Admin\Modules\ProductOption\Model\Events
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionStockUpdated
 */
class ProductOptionsStockUpdated
{
    /**
     * @var AdditionalOptionId
     */
    private $productOptionId;
    
    /**
     * @var AdditionalOptionStock
     */
    private $productOptionStock;
    
    
    /**
     * AdditionalOptionStockUpdated constructor.
     *
     * @param AdditionalOptionId    $productOptionId
     * @param AdditionalOptionStock $productOptionStock
     */
    private function __construct(
        AdditionalOptionId $productOptionId,
        AdditionalOptionStock $productOptionStock
    ) {
        $this->productOptionId = $productOptionId;
        $this->productOptionStock = $productOptionStock;
    }
    
    
    /**
     * @param AdditionalOptionId    $productOptionId
     * @param AdditionalOptionStock $productOptionStock
     *
     * @return ProductOptionsStockUpdated
     */
    public static function create(
        AdditionalOptionId $productOptionId,
        AdditionalOptionStock $productOptionStock
    ): ProductOptionsStockUpdated {
        
        return new self($productOptionId, $productOptionStock);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function productOptionId(): AdditionalOptionId
    {
        return $this->productOptionId;
    }
    
    
    /**
     * @return AdditionalOptionStock
     */
    public function productOptionStock(): AdditionalOptionStock
    {
        return $this->productOptionStock;
    }
}