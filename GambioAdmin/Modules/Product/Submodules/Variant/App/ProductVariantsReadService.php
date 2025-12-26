<?php
/*------------------------------------------------------------------------------
 ProductVariantsReadService.php 2023-06-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsReadService as ProductVariantsReadServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository as ProductVariantsRepositoryInterface;

class ProductVariantsReadService implements ProductVariantsReadServiceInterface
{
    /**
     * ProductVariantsReadService constructor.
     *
     * @param ProductVariantsRepositoryInterface $repository
     * @param ProductVariantFactory $factory
     */
    public function __construct(
        private ProductVariantsRepositoryInterface $repository,
        private ProductVariantFactory              $factory
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function getProductVariantsByProductId(int $productId): ProductVariants
    {
        return $this->repository->getProductVariantsByProductId($this->factory->createProductId($productId));
    }


    /**
     * @inheritDoc
     */
    public function getProductVariantById(int $variantId): ProductVariant
    {
        return $this->repository->getProductVariantById($this->factory->createProductVariantId($variantId));
    }
}