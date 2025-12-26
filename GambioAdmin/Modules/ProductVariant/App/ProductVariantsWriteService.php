<?php
/*------------------------------------------------------------------------------
 ProductVariantsWriteService.php 2010-08-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\ProductVariant\App;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsWriteService as ProductVariantsWriterServiceInterface;
use Gambio\Admin\Modules\Product\Services as Product;

/**
 * Class ProductVariantsWriteService
 *
 * @package Gambio\Admin\Modules\ProductVariant\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *              \Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService
 */
class ProductVariantsWriteService implements ProductVariantsWriterServiceInterface
{
    public function __construct(private Product\ProductVariantsWriteService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function createProductVariant(
        int                          $productId,
        OptionAndOptionValueIds      $combination,
        ?int                         $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): ProductVariantId {
        return $this->service->createProductVariant($productId,
                                                    $combination,
                                                    $imageListId,
                                                    $productCustomization,
                                                    $productIdentificationNumbers,
                                                    $stock,
                                                    $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductVariants(array ...$creationArguments): ProductVariantIds
    {
        // TODO: Implement createMultipleProductVariants() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void
    {
        // TODO: Implement storeProductVariants() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductVariants(int ...$ids): void
    {
        // TODO: Implement deleteProductVariants() method.
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductVariantsByProductId(int $productId): void
    {
        // TODO: Implement deleteAllProductVariantsByProductId() method.
    }
}