<?php
/*--------------------------------------------------------------------
 ProductOptionRepository.php 2023-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionDeleter;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionInserter;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionMapper;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionReader;
use Gambio\Admin\Modules\ProductOption\App\Data\ProductOptionUpdater;
use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptions;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionCreated;
use Gambio\Admin\Modules\ProductOption\Model\Events\ProductOptionDeleted;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionStock;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionOperationPermitter;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionRepository as ProductOptionRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductOptionRepository
 *
 * @package    Gambio\Admin\Modules\ProductOption\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository
 */
class ProductOptionRepository implements ProductOptionRepositoryInterface
{
    public function __construct(private AdditionalOptionRepository $repository) { }
    
    
    /**
     * @inheritDoc
     */
    public function getProductOptionsByProductId(ProductId $productId): ProductOptions
    {
        return $this->repository->getAdditionalOptionsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductOptionById(ProductOptionId $productOptionId): ProductOption
    {
        return $this->repository->getAdditionalOptionById($productOptionId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductOptions(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): ProductOptions {
        return $this->repository->filterAdditionalOptions($productId,
                                                          $filters,
                                                          $sorting,
                                                          $pagination);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductOptionsTotalCount(ProductId $productId, Filters $filters): int
    {
        return $this->repository->getAdditionalOptionsTotalCount($productId, $filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createProductOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductOptionStock       $productOptionStock,
        int                      $sortOrder = 0
    ): ProductOptionId {
        return $this->repository->createAdditionalOption($productId,
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
        return $this->repository->createMultipleAdditionalOptions(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductOptions(ProductOption ...$productOptions): void
    {
        $this->repository->storeAdditionalOptions(...$productOptions);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductOptions(ProductOptionId ...$ids): void
    {
        $this->repository->deleteAdditionalOptions(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductOptionsByProductId(ProductId $productId): void
    {
        $this->repository->deleteAllAdditionalOptionsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(ProductOptionOperationPermitter $permitter): void
    {
        $this->repository->registerOperationPermitter($permitter);
    }
}
//class ProductOptionRepository extends AbstractEventDispatchingRepository implements ProductOptionRepositoryInterface
//{
//
//    /**
//     * @var ProductOptionMapper
//     */
//    private $mapper;
//
//    /**
//     * @var ProductOptionReader
//     */
//    private $reader;
//
//    /**
//     * @var ProductOptionDeleter
//     */
//    private $deleter;
//
//    /**
//     * @var ProductOptionInserter
//     */
//    private $inserter;
//
//    /**
//     * @var ProductOptionUpdater
//     */
//    private $updater;
//
//    /**
//     * @var ProductOptionOperationPermitter[]
//     */
//    private $permitters = [];
//
//
//    /**
//     * ProductOptionRepository constructor.
//     *
//     * @param ProductOptionMapper      $mapper
//     * @param ProductOptionReader      $reader
//     * @param ProductOptionDeleter     $deleter
//     * @param ProductOptionInserter    $inserter
//     * @param ProductOptionUpdater     $updater
//     * @param EventDispatcherInterface $dispatcher
//     */
//    public function __construct(
//        ProductOptionMapper $mapper,
//        ProductOptionReader $reader,
//        ProductOptionDeleter $deleter,
//        ProductOptionInserter $inserter,
//        ProductOptionUpdater $updater,
//        EventDispatcherInterface $dispatcher
//    ) {
//        $this->mapper   = $mapper;
//        $this->reader   = $reader;
//        $this->deleter  = $deleter;
//        $this->inserter = $inserter;
//        $this->updater  = $updater;
//
//        $this->setEventDispatcher($dispatcher);
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function getProductOptionsByProductId(ProductId $productId): ProductOptions
//    {
//        return $this->mapper->mapProductOptions($this->reader->getProductOptionsByProductId($productId->value()));
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function getProductOptionById(ProductOptionId $productOptionId): ProductOption
//    {
//        return $this->mapper->mapProductOption($this->reader->getProductOptionById($productOptionId->value()));
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function filterProductOptions(
//        ProductId $productId,
//        Filters $filters,
//        Sorting $sorting,
//        Pagination $pagination
//    ): ProductOptions {
//
//        return $this->mapper->mapProductOptions($this->reader->filterProductOptions($productId,
//                                                                                    $filters,
//                                                                                    $sorting,
//                                                                                    $pagination));
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function getProductOptionsTotalCount(ProductId $productId, Filters $filters): int
//    {
//        return $this->reader->getProductOptionsTotalCount($productId, $filters);
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function createProductOption(
//        ProductId $productId,
//        OptionAndOptionValueId $optionAndOptionValueId,
//        ImageListId $imageListId,
//        OptionValueCustomization $optionValueCustomization,
//        ProductOptionStock $productOptionStock,
//        int $sortOrder = 0
//    ): ProductOptionId {
//
//        $creationArguments = [
//            $productId,
//            $optionAndOptionValueId,
//            $imageListId,
//            $optionValueCustomization,
//            $productOptionStock,
//            $sortOrder,
//        ];
//
//        foreach ($this->permitters as $permitter) {
//
//            if ($permitter->permitsCreations($creationArguments) === false) {
//
//                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
//            }
//        }
//
//        $productOptionId = $this->inserter->createProductOption(...$creationArguments);
//
//        $productOptionId = $this->mapper->mapProductOptionId($productOptionId);
//
//        $this->dispatchEvent(ProductOptionCreated::create($productOptionId));
//
//        return $productOptionId;
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function createMultipleProductOptions(array ...$creationArguments): ProductOptionIds
//    {
//        foreach ($this->permitters as $permitter) {
//
//            if ($permitter->permitsCreations(...$creationArguments) === false) {
//
//                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
//            }
//        }
//
//        $productOptionIds = $this->inserter->createMultipleProductOptions(...$creationArguments);
//        $productOptionIds = $this->mapper->mapProductOptionIds($productOptionIds);
//
//        foreach ($productOptionIds as $productOptionId) {
//
//            $this->dispatchEvent(ProductOptionCreated::create($productOptionId));
//        }
//
//        return $productOptionIds;
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function storeProductOptions(ProductOption ...$productOptions): void
//    {
//        foreach ($this->permitters as $permitter) {
//
//            if ($permitter->permitsStorages(...$productOptions) === false) {
//
//                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
//            }
//        }
//
//        $this->updater->storeProductOptions(...$productOptions);
//
//        foreach ($productOptions as $productOption) {
//
//            $this->dispatchEntityEvents($productOption);
//        }
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function deleteProductOptions(ProductOptionId ...$ids): void
//    {
//        foreach ($this->permitters as $permitter) {
//
//            if ($permitter->permitsDeletions(...$ids) === false) {
//
//                throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
//            }
//        }
//
//        $this->deleter->deleteProductOptions(...$ids);
//
//        foreach ($ids as $id) {
//
//            $this->dispatchEvent(ProductOptionDeleted::create($id));
//        }
//    }
//
//
//    /**
//     * @inheritDoc
//     */
//    public function deleteAllProductOptionsByProductId(ProductId $productId): void
//    {
//        $productOptionIds = $this->reader->getProductOptionIdsByProductId($productId->value());
//
//        if (count($productOptionIds)) {
//
//            $productOptionIds = $this->mapper->mapProductOptionIds($productOptionIds);
//
//            foreach ($this->permitters as $permitter) {
//
//                foreach ($productOptionIds as $productOptionId) {
//
//                    if ($permitter->permitsDeletions($productOptionId) === false) {
//
//                        throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
//                    }
//                }
//            }
//
//            $this->deleter->deleteAllProductOptionsByProductId($productId);
//
//            foreach ($productOptionIds as $productOptionId) {
//
//                $this->dispatchEvent(ProductOptionDeleted::create($productOptionId));
//            }
//        }
//    }
//
//    /**
//     * @inheritDoc
//     */
//    public function registerOperationPermitter(ProductOptionOperationPermitter $permitter): void
//    {
//        $this->permitters[get_class($permitter)] = $permitter;
//    }
//}