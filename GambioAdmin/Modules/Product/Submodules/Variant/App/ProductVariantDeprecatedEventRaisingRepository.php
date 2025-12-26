<?php
/*--------------------------------------------------------------
   ProductVariantDeprecatedEventRaisingRepository.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\ProductVariant\Model\Events as Deprecated;

/**
 * Class ProductVariantDeprecatedEventRaisingRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 * @deprecated
 */
class ProductVariantDeprecatedEventRaisingRepository extends ProductVariantsRepository
{
    /**
     * @inheritDoc
     */
    public function createProductVariant(
        ProductId                    $productId,
        OptionAndOptionValueIds      $combination,
        ImageListId                  $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): ProductVariantId {
        $id = parent::createProductVariant($productId,
                                           $combination,
                                           $imageListId,
                                           $productCustomization,
                                           $productIdentificationNumbers,
                                           $stock,
                                           $sortOrder);
        
        $this->dispatchEvent(Deprecated\ProductVariantCreated::create($id));
        
        return $id;
    }
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductVariants(array ...$creationArgs): ProductVariantIds
    {
        $ids = parent::createMultipleProductVariants(...$creationArgs);
        foreach ($ids as $id) {
            $this->dispatchEvent(Deprecated\ProductVariantCreated::create($id));
        }
        
        return $ids;
    }
    
    public function deleteProductVariants(ProductVariantId ...$ids): void
    {
        parent::deleteProductVariants(...$ids);
        foreach ($ids as $id) {
            $this->dispatchEvent(Deprecated\ProductVariantDeleted::create($id));
        }
    }
}