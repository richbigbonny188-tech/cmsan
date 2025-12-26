<?php
/*--------------------------------------------------------------
   ProductDownloadWriteServiceProxy.php 2023-06-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\ProductDownloadWriteService as ProductDownloadWriteServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\Product\Submodules\Download\Services as Submodule;

/**
 * Class ProductDownloadWriteServiceProxy
 *
 * @package Gambio\Admin\Modules\Product\App\Proxies
 */
class ProductDownloadWriteService implements ProductDownloadWriteServiceInterface
{
    public function __construct(private Submodule\ProductDownloadWriteService $service) { }
    
    
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
    ): AdditionalOptionId {
        return $this->service->createProductDownload($productId,
                                                     $optionAndOptionValueId,
                                                     $imageListId,
                                                     $optionValueCustomization,
                                                     $productDownloadStock,
                                                     $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductDownloads(array ...$creationArguments): AdditionalOptionIds
    {
        return $this->service->createMultipleProductDownloads(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void
    {
        $this->service->storeProductDownloads(...$productDownloads);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductDownloads(int ...$ids): void
    {
        $this->service->deleteProductDownloads(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductDownloadsByProductId(int $productId): void
    {
        $this->service->deleteAllProductDownloadsByProductId($productId);
    }
}