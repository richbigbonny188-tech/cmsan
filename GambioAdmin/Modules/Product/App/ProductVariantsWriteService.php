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

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\ProductVariantsWriteService as ProductVariantsWriteServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services as Submodule;

/**
 * Class ProductVariantsWriteService
 *
 * @package Gambio\Admin\Modules\Product\App
 */
class ProductVariantsWriteService implements ProductVariantsWriteServiceInterface
{
    public function __construct(private Submodule\ProductVariantsWriteService $service) { }
    
    
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
        return $this->service->createMultipleProductVariants(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void
    {
        $this->service->storeProductVariants(...$productVariants);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductVariants(int ...$ids): void
    {
        $this->service->deleteProductVariants(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductVariantsByProductId(int $productId): void
    {
        $this->service->deleteAllProductVariantsByProductId($productId);
    }
}