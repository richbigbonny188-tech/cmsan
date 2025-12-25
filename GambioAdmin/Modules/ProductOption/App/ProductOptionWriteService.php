<?php
/*--------------------------------------------------------------------
 ProductOptionWriteService.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService;
use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionStock;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionWriteService as ProductOptionWriteServiceInterface;

/**
 * Class ProductOptionWriteService
 * @package Gambio\Admin\Modules\ProductOption\App
  * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionWriteService
 */
class ProductOptionWriteService implements ProductOptionWriteServiceInterface
{
    public function __construct(private AdditionalOptionWriteService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function createProductOption(int                      $productId,
                                        OptionAndOptionValueId   $optionAndOptionValueId,
                                        ImageListId              $imageListId,
                                        OptionValueCustomization $optionValueCustomization,
                                        ProductOptionStock       $productOptionStock,
                                        int                      $sortOrder = 0
    ): ProductOptionId {
        return $this->service->createAdditionalOption($productId,
                                                      $optionAndOptionValueId,
                                                      $imageListId,
                                                      $optionValueCustomization,
                                                      $productOptionStock,
                                                      $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductOptions(array ...$creationArguments): ProductOptionIds
    {
        return $this->service->createMultipleAdditionalOptions(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductOptions(ProductOption ...$productOptions): void
    {
        $this->service->storeAdditionalOptions(...$productOptions);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductOptions(int ...$ids): void
    {
        $this->service->deleteAdditionalOptions(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductOptionsByProductId(int $productId): void
    {
        $this->service->deleteAllAdditionalOptionsByProductId($productId);
    }
}