<?php
/*--------------------------------------------------------------------
 ProductDownloadRepository.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Services;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\DeletionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\InsertionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadDoesNotExistException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\StorageOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface ProductDownloadRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Services
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
     * @param AdditionalOptionId $productOptionId
     *
     * @return ProductDownload
     *
     * @throws ProductDownloadDoesNotExistException
     */
    public function getProductDownloadById(AdditionalOptionId $productOptionId): ProductDownload;
    
    
    /**
     * @param ProductId  $productId
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return ProductDownloads
     */
    public function filterProductDownloads(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
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
     * @return AdditionalOptionId
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createProductDownload(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productDownloadStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId;
    
    
    /**
     * @param array $creationArguments
     *
     * @return AdditionalOptionIds
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleProductDownloads(array ...$creationArguments): AdditionalOptionIds;
    
    
    /**
     * @param ProductDownload ...$productDownloads
     *
     * @throws StorageOfProductDownloadsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void;
    
    
    /**
     * @param AdditionalOptionId ...$ids
     *
     * @throws DeletionOfProductDownloadsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteProductDownloads(AdditionalOptionId ...$ids): void;
    
    
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