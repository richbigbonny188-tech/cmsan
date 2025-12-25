<?php
/*--------------------------------------------------------------------
 ProductOptionsSortOrderUpdated.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model\Events;


use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;

/**
 * Class ProductOptionsSortOrderUpdated
 *
 * @package Gambio\Admin\Modules\ProductOption\Model\Events
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionSortOrderUpdated
 */
class ProductOptionsSortOrderUpdated
{
    /**
     * @var AdditionalOptionId
     */
    private $productOptionId;
    
    /**
     * @var int
     */
    private $sortOrder;
    
    
    /**
     * ProductOptionsSortOrderUpdated constructor.
     *
     * @param AdditionalOptionId $productOptionId
     * @param int             $sortOrder
     */
    private function __construct(AdditionalOptionId $productOptionId, int $sortOrder)
    {
        $this->productOptionId = $productOptionId;
        $this->sortOrder       = $sortOrder;
    }
    
    
    /**
     * @param AdditionalOptionId $productOptionId
     * @param int             $sortOrder
     *
     * @return ProductOptionsSortOrderUpdated
     */
    public static function create(AdditionalOptionId $productOptionId, int $sortOrder): ProductOptionsSortOrderUpdated
    {
        return new self($productOptionId, $sortOrder);
    }
    
    
    /**
     * @return AdditionalOptionId
     */
    public function productOptionId(): AdditionalOptionId
    {
        return $this->productOptionId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}