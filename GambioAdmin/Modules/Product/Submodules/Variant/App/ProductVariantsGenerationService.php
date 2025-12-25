<?php
/* --------------------------------------------------------------
   ProductVariantsGenerationService.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsGenerationService as ProductVariantsGenerationServiceInterface;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class ProductVariantsGenerationService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 */
class ProductVariantsGenerationService implements ProductVariantsGenerationServiceInterface
{
    /**
     * ProductVariantsGenerationService constructor.
     *
     * @param ProductVariantsRepository          $repository
     * @param ProductVariantFactory              $factory
     * @param ProductVariantCombinationGenerator $generator
     * @param ConfigurationService               $configurationService
     */
    public function __construct(
        private ProductVariantsRepository          $repository,
        private ProductVariantFactory              $factory,
        private ProductVariantCombinationGenerator $generator,
        private ConfigurationService               $configurationService
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function generateProductVariants(
        int   $productId,
        array $optionAndOptionValueIds,
        int   $limit = 100,
        int   $offset = 0
    ): ProductVariantIds {
        $productIdObj                 = $this->factory->createProductId($productId);
        $imageListId                  = $this->factory->createImageListId();
        $productCustomization         = $this->factory->createProductCustomization($this->getDefaultShippingStatusId());
        $productIdentificationNumbers = $this->factory->createProductIdentificationNumbers();
        $stock                        = $this->factory->createProductVariantStock();
        
        $this->repository->validateThatOptionValuesBelongToOption($optionAndOptionValueIds);
        
        $combinations = $this->generator->getCombinations($optionAndOptionValueIds, $limit, $offset);
        $ids          = [];
        
        foreach ($combinations as $sortOrder => $combination) {
            try {
                $ids[] = $this->repository->createProductVariant($productIdObj,
                                                                 $combination,
                                                                 $imageListId,
                                                                 $productCustomization,
                                                                 $productIdentificationNumbers,
                                                                 $stock,
                                                                 $sortOrder + $offset + 1);
            } catch (ProductVariantCombinationAlreadyExists $e) {
                unset($e);
            }
        }
        
        return $this->factory->createProductsVariantIds(...$ids);
    }
    
    
    /**
     * @return int
     */
    private function getDefaultShippingStatusId(): int
    {
        $shippingStatus = $this->configurationService->find('configuration/DEFAULT_SHIPPING_STATUS_ID');
        
        return ($shippingStatus === null) ? 1 : (int)$shippingStatus->value();
    }
    
    
    /**
     * @inheritDoc
     */
    public function addOptionToExistingProductVariants(
        int   $productId,
        int   $optionId,
        array $optionValueIds
    ): ProductVariantIds {
        $variantsToStore             = $variantsToCreate = [];
        $variants                    = $this->repository->getProductVariantsByProductId($this->factory->createProductId($productId));
        $firstOptionAndOptionValueId = $this->factory->createOptionAndOptionValueId($optionId,
                                                                                    array_shift($optionValueIds));
        $sortOrder                   = $this->getHighestSortOrderFromProductVariants($variants);
        
        foreach ($variants as $variant) {
            if ($variant->combination()->contains($firstOptionAndOptionValueId) === true) {
                throw StorageOfProductVariantsFailed::becauseOptionAllreadyExists($optionId, $variant->id());
            }
            
            $originalCombination = $variant->combination();
            $variant->changeCombination($originalCombination->with($firstOptionAndOptionValueId));
            $variantsToStore[] = $variant;
            
            if (empty($optionValueIds) === false) {
                foreach ($optionValueIds as $optionValueId) {
                    $optionAndOptionValueId = $this->factory->createOptionAndOptionValueId($optionId, $optionValueId);
                    
                    $variantsToCreate[] = [
                        $this->factory->createProductId($productId),
                        $originalCombination->with($optionAndOptionValueId),
                        $this->factory->createImageListId($variant->imageListId()),
                        $this->factory->createProductCustomization($variant->deliveryTimeId(),
                                                                   $variant->priceType(),
                                                                   $variant->price(),
                                                                   $variant->weightType(),
                                                                   $variant->weight(),
                                                                   $variant->vpeScalarValue(),
                                                                   $variant->vpeUnitId()),
                        $this->factory->createProductIdentificationNumbers($variant->modelNumber(),
                                                                           $variant->ean(),
                                                                           $variant->gtin(),
                                                                           $variant->asin()),
                        $this->factory->createProductVariantStock($variant->stock(), $variant->stockType()),
                        ++$sortOrder,
                    ];
                }
            }
        }
        
        $this->repository->storeProductVariants(...$variantsToStore);
        
        return $this->repository->createMultipleProductVariants(...$variantsToCreate);
    }
    
    
    /**
     * @param ProductVariants $productVariants
     *
     * @return int
     */
    protected function getHighestSortOrderFromProductVariants(ProductVariants $productVariants): int
    {
        $sortOrders = array_map(static function (array $variant): int {
            return $variant['sortOrder'];
        }, $productVariants->toArray());
        
        sort($sortOrders);
        
        return array_pop($sortOrders);
    }
    
    
    /**
     * @inheritDoc
     */
    public function removeOptionFromExistingProductVariants(
        int $productId,
        int $optionId,
        int $retainableOptionValueId
    ): void {
        $variantsToDelete = [];
        $variantsToStore  = [];
        
        $productId = $this->factory->createProductId($productId);
        $variants  = $this->repository->getProductVariantsByProductId($productId);
        
        foreach ($variants as $variant) {
            $optionAndOptionValueId = $this->factory->createOptionAndOptionValueId($optionId, $retainableOptionValueId);
            if ($variant->combination()->contains($optionAndOptionValueId) === false) {
                $variantsToDelete[] = $variant->id();
                continue;
            }
            
            $variant->changeCombination($variant->combination()->without($optionAndOptionValueId));
            $variantsToStore[] = $variant;
        }
        
        $variantsToDelete = array_map([$this->factory, 'createProductVariantId'], $variantsToDelete);
        
        if (empty($variantsToDelete) === false) {
            $this->repository->deleteProductVariants(...$variantsToDelete);
        }
        
        $this->repository->storeProductVariants(...$variantsToStore);
        $this->repository->unlinkOptionFromProduct($productId, $optionId);
    }
}