<?php
/*--------------------------------------------------------------
   ProductVariantsRepository.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsDeleter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsInserter;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsMapper;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsReader;
use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\ProductVariantsUpdater;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\OptionAndOptionValueIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\ProductVariantCreated;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events\ProductVariantDeleted;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\GenerationOfProductVariantsFailedException;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantCombinationAlreadyExists;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\StorageOfProductVariantsFailed;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository as ProductVariantsRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class ProductVariantsRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App
 */
class ProductVariantsRepository extends AbstractEventDispatchingRepository implements ProductVariantsRepositoryInterface
{
    /**
     * @var ProductVariantOperationPermitter[]
     */
    private array $permitters = [];
    
    
    /**
     * ProductVariantsRepository constructor.
     *
     * @param ProductVariantsReader    $reader
     * @param ProductVariantsDeleter   $deleter
     * @param ProductVariantsInserter  $inserter
     * @param ProductVariantsUpdater   $updater
     * @param ProductVariantsMapper    $mapper
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private ProductVariantsReader    $reader,
        private ProductVariantsDeleter   $deleter,
        private ProductVariantsInserter  $inserter,
        private ProductVariantsUpdater   $updater,
        private ProductVariantsMapper    $mapper,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->setEventDispatcher($eventDispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantById(ProductVariantId $id): ProductVariant
    {
        return $this->mapper->mapProductVariant($this->reader->getProductVariantById($id));
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
        $creationArguments = [
            $productId,
            $combination,
            $imageListId,
            $productCustomization,
            $productIdentificationNumbers,
            $stock,
            $sortOrder,
        ];
        
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations($creationArguments) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        if ($this->reader->getProductVariantIdByProductIdAndCombination($productId, $combination) !== null) {
            throw ProductVariantCombinationAlreadyExists::forProductIdAndCombinationString($productId->value(),
                                                                                           $combination->toString());
        }
        
        $idValue = $this->inserter->createProductVariant(...$creationArguments);
        
        $id = $this->mapper->mapProductVariantId($idValue);
        
        $this->dispatchEvent(ProductVariantCreated::create($id));
        
        return $id;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductVariants(array ...$creationArgs): ProductVariantIds
    {
        Assert::allIsList($creationArgs, 'Provided arguments need to be a list.');
        Assert::allMinCount($creationArgs, 6, 'At least two arguments needed per creation.');
        
        foreach ($creationArgs as $index => $creationArgument) {
            Assert::isInstanceOf($creationArgument[0],
                                 ProductId::class,
                                 'Product ID needs to implement "' . ProductId::class . '" interface. Index: '
                                 . $index);
            Assert::isInstanceOf($creationArgument[1],
                                 OptionAndOptionValueIds::class,
                                 'Combination needs to implement "' . OptionAndOptionValueIds::class
                                 . '" interface. Index: ' . $index);
            Assert::isInstanceOf($creationArgument[2],
                                 ImageListId::class,
                                 'Image list ID needs to implement "' . ImageListId::class . '" interface. Index: '
                                 . $index);
            Assert::isInstanceOf($creationArgument[3],
                                 ProductCustomization::class,
                                 'Product customization needs to implement "' . ProductCustomization::class
                                 . '" interface. Index: ' . $index);
            Assert::isInstanceOf($creationArgument[4],
                                 ProductIdentificationNumbers::class,
                                 'Product identification numbers needs to implement "'
                                 . ProductIdentificationNumbers::class . '" interface. Index: ' . $index);
            Assert::isInstanceOf($creationArgument[5],
                                 ProductVariantStock::class,
                                 'Product variant stock needs to implement "' . ProductVariantStock::class
                                 . '" interface. Index: ' . $index);
            Assert::integer($creationArgument[6] ?? 0, 'Sort order must be an integer. Index: ' . $index);
            
            $creationArguments[$index][6] = $creationArgument[6] ?? 0;
        }
        
        foreach ($creationArgs as $args) {
            /** @var ProductId $productId */
            $productId = $args[0];
            
            /** @var OptionAndOptionValueIds $combination */
            $combination = $args[1];
            
            if ($this->reader->getProductVariantIdByProductIdAndCombination($productId, $combination) !== null) {
                throw ProductVariantCombinationAlreadyExists::forProductIdAndCombinationString($productId->value(),
                                                                                               $combination->toString());
            }
            
            foreach ($this->permitters as $permitter) {
                if ($permitter->permitsCreations($args) === false) {
                    throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
                }
            }
        }
        
        $ids = array_map([$this->mapper, 'mapProductVariantId'],
                         $this->inserter->createMultipleProductVariants(...$creationArgs));
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductVariantCreated::create($id));
        }
        
        return $this->mapper->mapProductVariantIds(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductVariants(ProductVariant ...$productVariants): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsStorages(...$productVariants) === false) {
                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
            }
        }
        
        foreach ($productVariants as $productVariant) {
            $productId            = $this->mapper->mapProductId($productVariant->productId());
            $productVariantRecord = $this->reader->getProductVariantIdByProductIdAndCombination($productId,
                                                                                                $productVariant->combination());
            
            //  result may be null if the combination in the aggregate root changed !!!
            if ($productVariantRecord !== null && $productVariantRecord !== $productVariant->id()) {
                throw StorageOfProductVariantsFailed::becauseCombinationAlreadyExists($productVariant->productId(),
                                                                                      $productVariant->combination()
                                                                                          ->toString());
            }
        }
        
        $this->updater->storeProductVariants(...$productVariants);
        
        foreach ($productVariants as $productVariant) {
            $this->dispatchEntityEvents($productVariant);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductVariants(ProductVariantId ...$ids): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsDeletions(...$ids) === false) {
                throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
            }
        }
        
        $this->deleter->deleteProductVariants(...$ids);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductVariantDeleted::create($id));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductVariantsByProductId(ProductId $productId): void
    {
        foreach ($this->getProductVariantsByProductId($productId) as $variant) {
            foreach ($this->permitters as $permitter) {
                if ($permitter->permitsDeletions(ProductVariantId::create($variant->id())) === false) {
                    throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
                }
            }
        }
        
        $this->deleter->deleteAllProductVariantsByProductId($productId);
        $this->deleter->deleteAdminSelectDataForProduct($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantsByProductId(ProductId $productId): ProductVariants
    {
        return $this->mapper->mapProductVariants($this->reader->getProductVariantsByProductId($productId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function unlinkOptionFromProduct(ProductId $productId, int $optionId): void
    {
        $this->deleter->deleteAdminSelectDataForProductAndOption($productId, $optionId);
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
        return $this->mapper->mapProductVariants($this->reader->filterProductVariants($productId,
                                                                                      $filters,
                                                                                      $sorting,
                                                                                      $pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantsTotalCount(ProductId $productId, Filters $filters): int
    {
        return $this->reader->getProductVariantsTotalCount($productId, $filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateThatOptionValuesBelongToOption(array $optionAndOptionValueIds): void
    {
        if (count($optionAndOptionValueIds)) {
            foreach ($optionAndOptionValueIds as ['optionId' => $optionId, 'optionValueId' => $optionValueId]) {
                $optionId      = (int)$optionId;
                $optionValueId = (int)$optionValueId;
                
                if ($this->reader->getAssignedOptionOfOptionValue($optionValueId) !== $optionId) {
                    throw GenerationOfProductVariantsFailedException::optionValueDoesNotBelongToOption($optionId,
                                                                                                       $optionValueId);
                }
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(ProductVariantOperationPermitter $permitter): void
    {
        $this->permitters[$permitter::class] = $permitter;
    }
}