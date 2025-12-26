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

namespace Gambio\Admin\Modules\ProductDownload\Services;

use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\DeletionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\InsertionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\ProductDownloadAlreadyExistsException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\StorageOfProductDownloadsFailedException;
use Gambio\Admin\Modules\ProductDownload\Model\ProductDownload;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductDownloadStock;

/**
 * Interface ProductDownloadWriteService
 *
 * @package Gambio\Admin\Modules\ProductDownload\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\Proxies\ProductDownloadWriteServiceProxy
 */
interface ProductDownloadWriteService
{
    /**
     * @param int                      $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductDownloadStock     $productDownloadStock
     * @param int                      $sortOrder
     *
     * @return ProductOptionId
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     */
    public function createProductDownload(
        int $productId,
        OptionAndOptionValueId $optionAndOptionValueId,
        ImageListId $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock $productDownloadStock,
        int $sortOrder = 0
    ): ProductOptionId;
    
    
    /**
     * @param array $creationArguments
     *
     * @return ProductOptionIds
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     */
    public function createMultipleProductDownloads(array ...$creationArguments): ProductOptionIds;
    
    
    /**
     * @param ProductDownload ...$productDownloads
     *
     * @throws StorageOfProductDownloadsFailedException
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void;
    
    
    /**
     * @param int ...$ids
     *
     * @throws DeletionOfProductDownloadsFailedException
     */
    public function deleteProductDownloads(int ...$ids): void;
    
    
    /**
     * @param int $productId
     *
     * @throws DeletionOfProductDownloadsFailedException
     */
    public function deleteAllProductDownloadsByProductId(int $productId): void;
}