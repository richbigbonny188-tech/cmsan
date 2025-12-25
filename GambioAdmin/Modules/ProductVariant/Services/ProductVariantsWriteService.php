<?php
/*------------------------------------------------------------------------------
 ProductVariantsWriteService.php 2020-03-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\ProductVariant\Services;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\DeletionOfProductVariantsFailed;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\ProductVariant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantStock;

/**
 * Interface ProductVariantsWriteService
 * @package Gambio\Admin\Modules\ProductVariant\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService
 */
interface ProductVariantsWriteService
{
    /**
     * @param int                          $productId
     * @param OptionAndOptionValueIds      $combination
     * @param int|null                     $imageListId
     * @param ProductCustomization         $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock          $stock
     * @param int                          $sortOrder
     *
     * @return ProductVariantId
     *
     * @throws InsertionOfProductVariantsFailed
     */
    public function createProductVariant(
        int $productId,
        OptionAndOptionValueIds $combination,
        ?int $imageListId,
        ProductCustomization $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock $stock,
        int $sortOrder = 0
    ): ProductVariantId;
    
    
    /**
     * @param array $creationArguments
     *
     * @return ProductVariantIds
     *
     * @throws InsertionOfProductVariantsFailed
     * @throws ProductVariantCombinationAlreadyExists
     */
    public function createMultipleProductVariants(array ...$creationArguments): ProductVariantIds;
    
    
    /**
     * @param ProductVariant ...$productVariants
     *
     * @throws StorageOfProductVariantsFailed
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void;
    
    
    /**
     * @param int ...$ids
     *
     * @throws DeletionOfProductVariantsFailed
     */
    public function deleteProductVariants(int ...$ids): void;
    
    
    /**
     * @param int $productId
     */
    public function deleteAllProductVariantsByProductId(int $productId): void;
}