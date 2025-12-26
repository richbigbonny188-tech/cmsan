<?php
/*--------------------------------------------------------------
   DeprecatedEventRaisingProductVariantFactory.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Services;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\ProductVariant\Model as Deprecated;

/**
 * Class DeprecatedEventRaisingProductVariantFactory
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Services
 * @deprecated
 */
class DeprecatedEventRaisingProductVariantFactory extends ProductVariantFactory
{
    /**
     * @inheritDoc
     */
    public function createProductVariant(
        ProductVariantId             $variantId,
        ProductId                    $productId,
        OptionAndOptionValueIds      $combination,
        ImageListId                  $imageListId,
        ProductCustomization         $productCustomization,
        ProductIdentificationNumbers $productIdentificationNumbers,
        ProductVariantStock          $stock,
        int                          $sortOrder = 0
    ): ProductVariant {
        return Deprecated\ProductVariant::create($variantId,
                                                 $productId,
                                                 $combination,
                                                 $imageListId,
                                                 $productCustomization,
                                                 $productIdentificationNumbers,
                                                 $stock,
                                                 $sortOrder);
    }
}