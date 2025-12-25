<?php
/*--------------------------------------------------------------
   ProductVariantsGenerationService.php 2023-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant as Submodule;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;

/**
 * Class ProductVariantsGenerationService
 *
 * @package Gambio\Admin\Modules\ProductVariant\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Services\ProductVariantsFilterService
 */
class ProductVariantsGenerationService implements ProductVariantsGenerationServiceInterface
{
    public function __construct(
        private Submodule\App\ProductVariantsGenerationService $service,
        private ProductVariantFactory                          $factory,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function generateProductVariants(int $productId, array $optionAndOptionValueIds, int $limit = 100, int $offset = 0): ProductVariantIds
    {
        $result = $this->service->generateProductVariants($productId, $optionAndOptionValueIds, $limit, $offset);

        return $this->convertProductVariantIds($result);
    }

    /**
     * @inheritDoc
     */
    public function addOptionToExistingProductVariants(int $productId, int $optionId, array $optionValueIds): ProductVariantIds
    {
        $result = $this->service->addOptionToExistingProductVariants($productId, $optionId, $optionValueIds);

        return $this->convertProductVariantIds($result);
    }

    /**
     * @inheritDoc
     */
    public function removeOptionFromExistingProductVariants(int $productId, int $optionId, int $retainableOptionValueId): void
    {
        $this->service->removeOptionFromExistingProductVariants($productId, $optionId, $retainableOptionValueId);
    }

    /**
     * @param Submodule\Model\Collections\ProductVariantIds $ids
     * @return ProductVariantIds
     */
    private function convertProductVariantIds(Submodule\Model\Collections\ProductVariantIds $ids): ProductVariantIds
    {
        $ids = array_map([$this->factory, 'createProductVariantId'], $ids->toArray());

        return ProductVariantIds::create(...$ids);
    }
}