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

namespace Gambio\Admin\Modules\Product\Services;

use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\DeletionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\InsertionOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\ProductDownloadAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\StorageOfProductDownloadsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;

/**
 * Interface ProductDownloadWriteService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Services
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
     * @return AdditionalOptionId
     *
     * @throws InsertionOfProductDownloadsFailedException
     * @throws ProductDownloadAlreadyExistsException
     */
    public function createProductDownload(
        int                      $productId,
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
     */
    public function createMultipleProductDownloads(array ...$creationArguments): AdditionalOptionIds;
    
    
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