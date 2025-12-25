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

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadWriteService as ProductDownloadWriteServiceInterface;

/**
 * Class ProductDownloadWriteService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App
 */
class ProductDownloadWriteService implements ProductDownloadWriteServiceInterface
{
    /**
     * ProductDownloadWriteService constructor.
     *
     * @param ProductDownloadRepositoryInterface $repository
     * @param ProductDownloadFactory             $factory
     */
    public function __construct(
        private ProductDownloadRepositoryInterface $repository,
        private ProductDownloadFactory             $factory
    ) {
    }
    
    
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
        return $this->repository->createProductDownload($this->factory->createProductId($productId),
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
        return $this->repository->createMultipleProductDownloads(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void
    {
        $this->repository->storeProductDownloads(...$productDownloads);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductDownloads(int ...$ids): void
    {
        $this->repository->deleteProductDownloads(...array_map([$this->factory, 'createProductOptionId'], $ids));
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductDownloadsByProductId(int $productId): void
    {
        $productId = $this->factory->createProductId($productId);
        
        $this->repository->deleteAllProductDownloadsByProductId($productId);
    }
}