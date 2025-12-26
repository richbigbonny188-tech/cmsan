<?php
/*------------------------------------------------------------------------------
 ProductVariantsWriteService.php 2010-08-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository as ProductVariantsRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsWriteService as ProductVariantsWriterServiceInterface;
use Webmozart\Assert\Assert;

/**
 * Class ProductVariantsWriteService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 */
class ProductVariantsWriteService implements ProductVariantsWriterServiceInterface
{
    /**
     * ProductVariantsWriteService constructor.
     *
     * @param ProductVariantsRepositoryInterface $repository
     * @param ProductVariantFactory              $factory
     */
    public function __construct(
        private ProductVariantsRepositoryInterface $repository,
        private ProductVariantFactory              $factory
    ) {
    }
    
    
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
        return $this->repository->createProductVariant($this->factory->createProductId($productId),
                                                       $combination,
                                                       $this->factory->createImageListId($imageListId),
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
        Assert::allIsList($creationArguments, 'Provided arguments need to be a list.');
        Assert::allMinCount($creationArguments, 6, 'At least two arguments needed per creation.');
        
        foreach ($creationArguments as $index => $creationArgument) {
            Assert::integer($creationArgument[0], 'Product ID must be an integer. Index: ' . $index);
            
            if ($creationArgument[2] !== null) {
                Assert::integer($creationArgument[2], 'Image list ID must be an integer. Index: ' . $index);
            }
            
            $creationArguments[$index][0] = $this->factory->createProductId($creationArgument[0]);
            $creationArguments[$index][2] = $this->factory->createImageListId($creationArgument[2]);
        }
        
        return $this->repository->createMultipleProductVariants(...$creationArguments);
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
    public function deleteProductVariants(int ...$ids): void
    {
        $callback = fn(int $id): ProductVariantId => $this->factory->createProductVariantId($id);
        $ids      = array_map($callback, $ids);
        
        $this->repository->deleteProductVariants(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductVariantsByProductId(int $productId): void
    {
        $this->repository->deleteAllProductVariantsByProductId($this->factory->createProductId($productId));
    }
}