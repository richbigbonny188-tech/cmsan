<?php
/*--------------------------------------------------------------------
 ProductDownloadRepository.php 2021-10-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\Services;

use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\DeletionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\InsertionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\ProductDownloadAlreadyExistsException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\ProductDownloadDoesNotExistException;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\StorageOfProductDownloadsFailedException;
use Gambio\Admin\Modules\ProductDownload\Model\ProductDownload;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductDownloadStock;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface ProductDownloadRepository
 *
 * @package Gambio\Admin\Modules\ProductDownload\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository
 */
interface ProductDownloadRepository
{
    /**
     * @param ProductId $productId
     *
     * @return ProductDownloads
     */
    public function getProductDownloadsByProductId(ProductId $productId): ProductDownloads;
    
    
    /**
     * @param ProductOptionId $productOptionId
     *
     * @return ProductDownload
     *
     * @throws ProductDownloadDoesNotExistException
     */
    public function getProductDownloadById(ProductOptionId $productOptionId): ProductDownload;
    
    
    /**
     * @param ProductId  $productId
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return ProductDownloads
     */
    public function filterProductDownloads(
        ProductId $productId,
        Filters $filters,
        Sorting $sorting,
        Pagination $pagination
    ): ProductDownloads;
    
    
    /**
     * @param ProductId $productId
     * @param Filters   $filters
     *
     * @return int
     */
    public function getProductDownloadsTotalCount(ProductId $productId, Filters $filters): int;
    
    
    /**
     * @param ProductId                $productId
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
     * @throws OperationHasNotBeenPermittedException
     */
    public function createProductDownload(
        ProductId $productId,
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
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleProductDownloads(array ...$creationArguments): ProductOptionIds;
    
    
    /**
     * @param ProductDownload ...$productDownloads
     *
     * @throws StorageOfProductDownloadsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void;
    
    
    /**
     * @param ProductOptionId ...$ids
     *
     * @throws DeletionOfProductDownloadsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteProductDownloads(ProductOptionId ...$ids): void;
    
    
    /**
     * @param ProductId $productId
     *
     * @throws DeletionOfProductDownloadsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAllProductDownloadsByProductId(ProductId $productId): void;
    
    
    /**
     * @param ProductDownloadOperationPermitter $permitter
     */
    public function registerOperationPermitter(ProductDownloadOperationPermitter $permitter): void;
}