<?php
/*--------------------------------------------------------------------
 ProductDownloadRepository.php 2023-06-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadDeleter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadInserter;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadMapper;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadReader;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\ProductDownloadUpdater;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Events\ProductDownloadCreated;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Events\ProductDownloadDeleted;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ProductDownload;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductDownloadRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App
 */
class ProductDownloadRepository extends AbstractEventDispatchingRepository implements ProductDownloadRepositoryInterface
{
    /**
     * @var ProductDownloadOperationPermitter[]
     */
    private array $permitters = [];
    
    
    /**
     * ProductDownloadRepository constructor.
     *
     * @param ProductDownloadMapper    $mapper
     * @param ProductDownloadReader    $reader
     * @param ProductDownloadDeleter   $deleter
     * @param ProductDownloadInserter  $inserter
     * @param ProductDownloadUpdater   $updater
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        private ProductDownloadMapper   $mapper,
        protected ProductDownloadReader $reader,
        private ProductDownloadDeleter  $deleter,
        private ProductDownloadInserter $inserter,
        private ProductDownloadUpdater  $updater,
        EventDispatcherInterface        $dispatcher
    ) {
        $this->setEventDispatcher($dispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsByProductId(ProductId $productId): ProductDownloads
    {
        return $this->mapper->mapProductDownloads($this->reader->getProductOptionsByProductId($productId->value()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadById(AdditionalOptionId $productOptionId): ProductDownload
    {
        return $this->mapper->mapProductDownload($this->reader->getProductOptionById($productOptionId->value()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductDownloads(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): ProductDownloads {
        return $this->mapper->mapProductDownloads($this->reader->filterProductDownloads($productId,
                                                                                        $filters,
                                                                                        $sorting,
                                                                                        $pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsTotalCount(ProductId $productId, Filters $filters): int
    {
        return $this->reader->getProductDownloadsTotalCount($productId, $filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createProductDownload(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductDownloadStock     $productDownloadStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId {
        $creationArguments = [
            $productId,
            $optionAndOptionValueId,
            $imageListId,
            $optionValueCustomization,
            $productDownloadStock,
            $sortOrder,
        ];
        
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations(...$creationArguments) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $productOptionId = $this->inserter->createProductDownload($productId,
                                                                  $optionAndOptionValueId,
                                                                  $imageListId,
                                                                  $optionValueCustomization,
                                                                  $productDownloadStock,
                                                                  $sortOrder);
        
        $productOptionId = $this->mapper->mapProductOptionId($productOptionId);
        
        $this->dispatchEvent(ProductDownloadCreated::create($productOptionId));
        
        return $productOptionId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductDownloads(array ...$creationArguments): AdditionalOptionIds
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations(...$creationArguments) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $productOptionIds = $this->inserter->createMultipleProductDownloads(...$creationArguments);
        $productOptionIds = $this->mapper->mapProductOptionIds($productOptionIds);
        
        foreach ($productOptionIds as $productOptionId) {
            $this->dispatchEvent(ProductDownloadCreated::create($productOptionId));
        }
        
        return $productOptionIds;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsStorages(...$productDownloads) === false) {
                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
            }
        }
        
        $this->updater->storeProductDownloads(...$productDownloads);
        
        foreach ($productDownloads as $productDownload) {
            $this->dispatchEntityEvents($productDownload);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductDownloads(AdditionalOptionId ...$ids): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsDeletions(...$ids) === false) {
                throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
            }
        }
        
        $this->deleter->deleteProductOptions(...$ids);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(ProductDownloadDeleted::create($id));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductDownloadsByProductId(ProductId $productId): void
    {
        $additionalOptionIds = $this->reader->getProductOptionIdsByProductId($productId->value());
        
        if (count($additionalOptionIds)) {
            $additionalOptionIds = $this->mapper->mapProductOptionIds($additionalOptionIds);
            
            foreach ($this->permitters as $permitter) {
                foreach ($additionalOptionIds as $additionalOptionId) {
                    if ($permitter->permitsDeletions($additionalOptionId) === false) {
                        throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
                    }
                }
            }
            
            $this->deleter->deleteAllProductDownloadsByProductId($productId);
            
            foreach ($additionalOptionIds as $additionalOptionId) {
                $this->dispatchEvent(ProductDownloadDeleted::create($additionalOptionId));
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(ProductDownloadOperationPermitter $permitter): void
    {
        $this->permitters[$permitter::class] = $permitter;
    }
}