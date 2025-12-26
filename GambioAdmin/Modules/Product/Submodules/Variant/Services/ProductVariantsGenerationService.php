<?php
/*------------------------------------------------------------------------------
  ProductVariantsGenerationService.php 2023-06-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -----------------------------------------------------------------------------
*/

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Services;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\DeletionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\GenerationOfProductVariantsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\InsertionOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;

/**
 * Interface ProductVariantsGenerationService
 *
 * @package    Gambio\Admin\Modules\Product\Submodules\Variant\Services
 */
interface ProductVariantsGenerationService
{
    /**
     * @param int   $productId
     * @param array $optionAndOptionValueIds
     * @param int   $limit
     * @param int   $offset
     *
     * @return ProductVariantIds
     *
     * @throws InsertionOfProductVariantsFailed
     * @throws GenerationOfProductVariantsFailedException
     */
    public function generateProductVariants(
        int   $productId,
        array $optionAndOptionValueIds,
        int   $limit = 100,
        int   $offset = 0
    ): ProductVariantIds;
    
    
    /**
     * @param int   $productId
     * @param int   $optionId
     * @param array $optionValueIds
     *
     * @return ProductVariantIds
     *
     * @throws InsertionOfProductVariantsFailed
     * @throws StorageOfProductVariantsFailed
     * @throws ProductVariantCombinationAlreadyExists
     */
    public function addOptionToExistingProductVariants(
        int   $productId,
        int   $optionId,
        array $optionValueIds
    ): ProductVariantIds;
    
    
    /**
     * Deletes an option from the existing product variant combinations and only keeps the product variants with a
     * combination containing a specific option value of that option.
     *
     * @param int $productId
     * @param int $optionId
     * @param int $retainableOptionValueId
     *
     * @return ProductVariantIds
     *
     * @throws StorageOfProductVariantsFailed
     * @throws DeletionOfProductVariantsFailed
     */
    public function removeOptionFromExistingProductVariants(
        int $productId,
        int $optionId,
        int $retainableOptionValueId
    ): void;
}