<?php
/*--------------------------------------------------------------------
 AdditionalOptionRepository.php 2023-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionDeleter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionInserter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionMapper;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionReader;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Data\AdditionalOptionUpdater;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionCreated;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Events\AdditionalOptionDeleted;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionOperationPermitter;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository as AdditionalOptionRepositoryInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdditionalOptionRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App
 */
class AdditionalOptionRepository extends AbstractEventDispatchingRepository
    implements AdditionalOptionRepositoryInterface
{
    /**
     * @var AdditionalOptionOperationPermitter[]
     */
    private array $permitters = [];
    
    
    /**
     * AdditionalOptionRepository constructor.
     *
     * @param AdditionalOptionMapper   $mapper
     * @param AdditionalOptionReader   $reader
     * @param AdditionalOptionDeleter  $deleter
     * @param AdditionalOptionInserter $inserter
     * @param AdditionalOptionUpdater  $updater
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        private AdditionalOptionMapper   $mapper,
        protected AdditionalOptionReader   $reader,
        private AdditionalOptionDeleter  $deleter,
        private AdditionalOptionInserter $inserter,
        private AdditionalOptionUpdater  $updater,
        EventDispatcherInterface         $dispatcher
    ) {
        $this->setEventDispatcher($dispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionsByProductId(ProductId $productId): AdditionalOptions
    {
        return $this->mapper->mapAdditionalOptions($this->reader->getAdditionalOptionsByProductId($productId->value()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionById(AdditionalOptionId $additionalOptionId): AdditionalOption
    {
        return $this->mapper->mapAdditionalOption($this->reader->getAdditionalOptionById($additionalOptionId->value()));
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterAdditionalOptions(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): AdditionalOptions {
        return $this->mapper->mapAdditionalOptions($this->reader->filterAdditionalOptions($productId,
                                                                                          $filters,
                                                                                          $sorting,
                                                                                          $pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionsTotalCount(ProductId $productId, Filters $filters): int
    {
        return $this->reader->getAdditionalOptionsTotalCount($productId, $filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createAdditionalOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId {
        $creationArguments = [
            $productId,
            $optionAndOptionValueId,
            $imageListId,
            $optionValueCustomization,
            $additionalOptionStock,
            $sortOrder,
        ];
        
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations($creationArguments) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $additionalOptionId = $this->inserter->createAdditionalOption(...$creationArguments);
        $additionalOptionId = $this->mapper->mapAdditionalOptionId($additionalOptionId);
        
        $this->dispatchEvent(AdditionalOptionCreated::create($additionalOptionId));
        
        return $additionalOptionId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleAdditionalOptions(array ...$creationArguments): AdditionalOptionIds
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsCreations(...$creationArguments) === false) {
                throw OperationHasNotBeenPermittedException::forCreationByPermitter($permitter);
            }
        }
        
        $additionalOptionIds = $this->inserter->createMultipleAdditionalOptions(...$creationArguments);
        $additionalOptionIds = $this->mapper->mapAdditionalOptionIds($additionalOptionIds);
        
        foreach ($additionalOptionIds as $additionalOptionId) {
            $this->dispatchEvent(AdditionalOptionCreated::create($additionalOptionId));
        }
        
        return $additionalOptionIds;
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeAdditionalOptions(AdditionalOption ...$additionalOptions): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsStorages(...$additionalOptions) === false) {
                throw OperationHasNotBeenPermittedException::forStorageByPermitter($permitter);
            }
        }
        
        $this->updater->storeAdditionalOptions(...$additionalOptions);
        
        foreach ($additionalOptions as $additionalOption) {
            $this->dispatchEntityEvents($additionalOption);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAdditionalOptions(AdditionalOptionId ...$ids): void
    {
        foreach ($this->permitters as $permitter) {
            if ($permitter->permitsDeletions(...$ids) === false) {
                throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
            }
        }
        
        $this->deleter->deleteAdditionalOptions(...$ids);
        
        foreach ($ids as $id) {
            $this->dispatchEvent(AdditionalOptionDeleted::create($id));
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllAdditionalOptionsByProductId(ProductId $productId): void
    {
        $additionalOptionIds = $this->reader->getAdditionalOptionIdsByProductId($productId->value());
        
        if (count($additionalOptionIds)) {
            $additionalOptionIds = $this->mapper->mapAdditionalOptionIds($additionalOptionIds);
            
            foreach ($this->permitters as $permitter) {
                foreach ($additionalOptionIds as $additionalOptionId) {
                    if ($permitter->permitsDeletions($additionalOptionId) === false) {
                        throw OperationHasNotBeenPermittedException::forDeletionByPermitter($permitter);
                    }
                }
            }
            
            $this->deleter->deleteAllAdditionalOptionsByProductId($productId);
            
            foreach ($additionalOptionIds as $additionalOptionId) {
                $this->dispatchEvent(AdditionalOptionDeleted::create($additionalOptionId));
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(AdditionalOptionOperationPermitter $permitter): void
    {
        $this->permitters[$permitter::class] = $permitter;
    }
}