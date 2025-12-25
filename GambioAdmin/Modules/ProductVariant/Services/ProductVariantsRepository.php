<?php
/*--------------------------------------------------------------
   ProductVariantsRepository.php 2021-10-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\Services;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\DeletionOfProductVariantsFailed;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\GenerationOfProductVariantsFailedException;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\ProductVariantDoesNotExist;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantStock;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface ProductVariantsRepository
 * @package Gambio\Admin\Modules\ProductVariant\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *              \Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository
 */
interface ProductVariantsRepository
{
    /**
     * @param ProductId $productId
     *
     * @return ProductVariants
     */
    public function getProductVariantsByProductId(ProductId $productId): ProductVariants;
    
    
    /**
     * @param ProductVariantId $id
     *
     * @return ProductVariant
     *
     * @throws ProductVariantDoesNotExist
     */
    public function getProductVariantById(ProductVariantId $id): ProductVariant;
    
    
    /**
     * @param ProductId                    $productId
     * @param OptionAndOptionValueIds      $combination
     * @param ImageListId                  $imageListId
     * @param ProductCustomization         $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock          $stock
     * @param int                          $sortOrder
     *
     * @return ProductVariantId
     *
     * @throws ProductVariantCombinationAlreadyExists
     * @throws InsertionOfProductVariantsFailed
     * @throws OperationHasNotBeenPermittedException
     */
    public function createProductVariant(
        ProductId $productId,
        OptionAndOptionValueIds $combination,
        ImageListId $imageListId,
        ProductCustomization $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock $stock,
        int $sortOrder = 0
    ): ProductVariantId;
    
    
    /**
     * @param array[] $creationArgs
     *
     * @return ProductVariantIds
     *
     * @throws ProductVariantCombinationAlreadyExists
     * @throws InsertionOfProductVariantsFailed
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleProductVariants(array ...$creationArgs): ProductVariantIds;
    
    
    /**
     * @param ProductVariant ...$productVariants
     *
     * @throws StorageOfProductVariantsFailed
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void;
    
    
    /**
     * @param ProductVariantId ...$ids
     *
     * @throws DeletionOfProductVariantsFailed
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteProductVariants(ProductVariantId ...$ids): void;
    
    
    /**
     * @param ProductId $productId
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAllProductVariantsByProductId(ProductId $productId): void;
    
    
    /**
     * @param ProductId $productId
     * @param int       $optionId
     */
    public function unlinkOptionFromProduct(ProductId $productId, int $optionId): void;
    
    
    /**
     * @param ProductId  $productId
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return ProductVariants
     */
    public function filterProductVariants(
        ProductId $productId,
        Filters $filters,
        Sorting $sorting,
        Pagination $pagination
    ): ProductVariants;
    
    
    /**
     * @param ProductId $productId
     * @param Filters   $filters
     *
     * @return int
     */
    public function getProductVariantsTotalCount(ProductId $productId, Filters $filters): int;
    
    
    /**
     * @param array $optionAndOptionValueIds
     *
     * @throws GenerationOfProductVariantsFailedException
     */
    public function validateThatOptionValuesBelongToOption(array $optionAndOptionValueIds): void;
    
    
    /**
     * @param ProductVariantOperationPermitter $permitter
     */
    public function registerOperationPermitter(ProductVariantOperationPermitter $permitter): void;
}