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

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;

/**
 * Class ProductOptionsStockUpdated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Events
 */
class ProductDownloadsStockUpdated
{
    /**
     * ProductOptionStockUpdated constructor.
     *
     * @param AdditionalOptionId $productOptionId
     * @param ProductDownloadStock $productOptionStock
     */
    private function __construct(
        private AdditionalOptionId   $productOptionId,
        private ProductDownloadStock $productOptionStock
    )
    {
    }


    /**
     * @param AdditionalOptionId $productOptionId
     * @param ProductDownloadStock $productOptionStock
     *
     * @return ProductDownloadsStockUpdated
     */
    public static function create(
        AdditionalOptionId   $productOptionId,
        ProductDownloadStock $productOptionStock
    ): ProductDownloadsStockUpdated
    {
        return new static($productOptionId, $productOptionStock);
    }


    /**
     * @return AdditionalOptionId
     */
    public function productOptionId(): AdditionalOptionId
    {
        return $this->productOptionId;
    }


    /**
     * @return ProductDownloadStock
     */
    public function productOptionStock(): ProductDownloadStock
    {
        return $this->productOptionStock;
    }
}