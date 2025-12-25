<?php
/*--------------------------------------------------------------
   ProductVariantsWriteService.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Services;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\DeletionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;

/**
 * Interface ProductVariantsWriteService
 *
 * @package Gambio\Admin\Modules\Product\Services
 */
interface ProductVariantsWriteService
{
    /**
     * @param int $productId
     * @param OptionAndOptionValueIds $combination
     * @param int|null $imageListId
     * @param ProductCustomization $productCustomization
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     * @param ProductVariantStock $stock
     * @param int $sortOrder
     *
     * @return ProductVariantId
     *
     * @throws InsertionOfProductVariantsFailed
     */
    public function createProductVariant(
        int                          $productId,
        OptionAndOptionValueIds      $combination,
        ?int                         $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
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