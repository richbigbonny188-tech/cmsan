<?php
/*--------------------------------------------------------------------
 ProductDownloadWriteService.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\App;

use Gambio\Admin\Modules\Product\Services\ProductDownloadWriteService;
use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductDownload\Model\ProductDownload;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadWriteService as ProductDownloadWriteServiceInterface;

/**
 * Class ProductDownloadWriteService
 *
 * @package Gambio\Admin\Modules\ProductDownload\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadWriteService implements ProductDownloadWriteServiceInterface
{
    public function __construct(private ProductDownloadWriteService $proxy) { }
    
    
    /**
     * @inheritDoc
     */
    public function createProductDownload(
        int                      $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productDownloadStock,
        int                      $sortOrder = 0
    ): ProductOptionId {
        return $this->proxy->createProductDownload($productId,
                                                   $optionAndOptionValueId,
                                                   $imageListId,
                                                   $optionValueCustomization,
                                                   $productDownloadStock,
                                                   $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductDownloads(array ...$creationArguments): ProductOptionIds
    {
        return $this->proxy->createMultipleProductDownloads(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void
    {
        $this->proxy->storeProductDownloads(...$productDownloads);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductDownloads(int ...$ids): void
    {
        $this->proxy->deleteProductDownloads(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductDownloadsByProductId(int $productId): void
    {
        $this->proxy->deleteAllProductDownloadsByProductId($productId);
    }
}