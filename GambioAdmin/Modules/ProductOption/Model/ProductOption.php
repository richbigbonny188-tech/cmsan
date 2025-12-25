<?php
/*--------------------------------------------------------------------
 ProductOption.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Model;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionStock;

/**
 * Class ProductOption
 *
 * @package    Gambio\Admin\Modules\ProductOption\Model
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption
 */
class ProductOption extends AdditionalOption
{
    /**
     * @param ProductOptionStock $productOptionStock
     *
     * @return void
     * @deprecated method will be unavailable with GX 4.11 use changeAdditionalOptionStock method instead
     */
    public function changeProductOptionStock(ProductOptionStock $productOptionStock): void
    {
        $this->changeAdditionalOptionStock($productOptionStock);
    }
}
