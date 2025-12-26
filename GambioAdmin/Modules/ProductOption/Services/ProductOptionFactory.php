<?php
/*--------------------------------------------------------------------
 ProductOptionFactory.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionStock;

/**
 * Class ProductOptionFactory
 *
 * @package    Gambio\Admin\Modules\ProductOption\Services
 * @internal   No method for creation Aggregate root / collection
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory
 */
class ProductOptionFactory extends AdditionalOptionFactory
{
    /**
     * @param int $productOptionId
     *
     * @return ProductOptionId
     * @deprecated method will be unavailable with GX 4.11 use createAdditionalOptionId method instead
     */
    public function createProductOptionId(int $productOptionId): ProductOptionId
    {
        return $this->createAdditionalOptionId($productOptionId);
    }
    
    
    /**
     * @param float  $stock
     * @param string $stockType
     *
     * @return ProductOptionStock
     * @deprecated method will be unavailable with GX 4.11 use createAdditionalOptionStock method instead
     */
    public function createProductOptionStock(
        float  $stock = 0,
        string $stockType = ProductOptionStock::STOCK_TYPE_NOT_MANAGED
    ): ProductOptionStock {
        return $this->createAdditionalOptionStock($stock, $stockType);
    }
}