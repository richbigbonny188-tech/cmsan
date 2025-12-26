<?php
/*------------------------------------------------------------------------------
 ProductVariantsReadService.php 2020-03-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\ProductVariant\App;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Services as Product;

/**
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *              \Gambio\Admin\Modules\Product\Services\ProductVariantsReadService
 */
class ProductVariantsReadService implements ProductVariantsReadServiceInterface
{
    public function __construct(private Product\ProductVariantsReadService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantsByProductId(int $productId): ProductVariants
    {
        return $this->service->getProductVariantsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantById(int $variantId): ProductVariant
    {
        return $this->service->getProductVariantById($variantId);
    }
}