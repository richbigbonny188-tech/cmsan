<?php
/*--------------------------------------------------------------
   ProductVariantsReadService.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services as Submodule;

/**
 * Class ProductVariantsReadService
 *
 * @package Gambio\Admin\Modules\Product\App
 */
class ProductVariantsReadService implements ProductVariantsReadServiceInterface
{
    public function __construct(private Submodule\ProductVariantsReadService $service) { }
    
    
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