<?php
/*--------------------------------------------------------------
   ProductVariantsRepository.php 2021-10-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\App;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\ProductVariant\Model\ProductVariant;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\ProductVariant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantOperationPermitter;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsRepository as ProductVariantsRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services as Submodule;

/**
 * Class ProductVariantsRepository
 * @package Gambio\Admin\Modules\ProductVariant\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *              submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *              \Gambio\Admin\Modules\Product\Services\ProductVariantsRepository
 */
class ProductVariantsRepository extends AbstractEventDispatchingRepository implements ProductVariantsRepositoryInterface
{
    public function __construct(private Submodule\ProductVariantsRepository $repository) { }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantsByProductId(ProductId $productId): ProductVariants
    {
        return $this->repository->getProductVariantsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantById(ProductVariantId $id): ProductVariant
    {
        return $this->repository->getProductVariantById($id);
    }
    
    
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
        return $this->repository->createProductVariant($productId,
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
    public function createMultipleProductVariants(array ...$creationArgs): ProductVariantIds
    {
        return $this->repository->createMultipleProductVariants(...$creationArgs);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void
    {
        $this->repository->storeProductVariants(...$productVariants);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductVariants(ProductVariantId ...$ids): void
    {
        $this->deleteProductVariants(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductVariantsByProductId(ProductId $productId): void
    {
        $this->deleteAllProductVariantsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function unlinkOptionFromProduct(ProductId $productId, int $optionId): void
    {
        $this->unlinkOptionFromProduct($productId, $optionId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductVariants(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): ProductVariants {
        return $this->repository->filterProductVariants($productId,
                                                        $filters,
                                                        $sorting,
                                                        $pagination);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantsTotalCount(ProductId $productId, Filters $filters): int
    {
        return $this->repository->getProductVariantsTotalCount($productId, $filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateThatOptionValuesBelongToOption(array $optionAndOptionValueIds): void
    {
        $this->repository->validateThatOptionValuesBelongToOption($optionAndOptionValueIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(ProductVariantOperationPermitter $permitter): void
    {
        $this->repository->registerOperationPermitter($permitter);
    }
}