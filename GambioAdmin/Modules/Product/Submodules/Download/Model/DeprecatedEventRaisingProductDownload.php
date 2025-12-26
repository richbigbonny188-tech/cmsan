<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingProductDownload.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadImageListIdUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadsSortOrderUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadsStockUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadsValueCustomizationUpdated;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductOptionId;

/**
 * Class DeprecatedEventRaisingProductDownload
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model
 * @deprecated
 */
class DeprecatedEventRaisingProductDownload extends ProductDownload
{
    /**
     * @return ProductOptionId
     */
    private function createIdFromValue(): ProductOptionId
    {
        return ProductOptionId::create($this->id());
    }
    
    
    public function changeImageListId(ImageListId $imageListId): void
    {
        parent::changeImageListId($imageListId);
        $this->raiseEvent(ProductDownloadImageListIdUpdated::create($this->createIdFromValue(), $imageListId));
    }
    
    
    public function changeOptionValueCustomization(OptionValueCustomization $optionValueCustomization): void
    {
        parent::changeOptionValueCustomization($optionValueCustomization);
        $this->raiseEvent(ProductDownloadsValueCustomizationUpdated::create($this->createIdFromValue(),
                                                                            $optionValueCustomization));
    }
    
    public function changeProductOptionStock(ProductDownloadStock $productOptionStock): void
    {
        parent::changeProductOptionStock($productOptionStock);
        $this->raiseEvent(ProductDownloadsStockUpdated::create($this->createIdFromValue(), $productOptionStock));
    }
    
    public function changeSortOrder(int $sorOrder): void
    {
        parent::changeSortOrder($sorOrder);
        $this->raiseEvent(ProductDownloadsSortOrderUpdated::create($this->createIdFromValue(), $sorOrder));
    }
}