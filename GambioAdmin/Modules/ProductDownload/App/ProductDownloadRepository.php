<?php
/*--------------------------------------------------------------------
 ProductDownloadRepository.php 2021-10-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductDownload\App;

use Gambio\Admin\Modules\ProductDownload\App\Data\ProductDownloadDeleter;
use Gambio\Admin\Modules\ProductDownload\App\Data\ProductDownloadInserter;
use Gambio\Admin\Modules\ProductDownload\App\Data\ProductDownloadMapper;
use Gambio\Admin\Modules\ProductDownload\App\Data\ProductDownloadReader;
use Gambio\Admin\Modules\ProductDownload\App\Data\ProductDownloadUpdater;
use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductDownload\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadCreated;
use Gambio\Admin\Modules\ProductDownload\Model\Events\ProductDownloadDeleted;
use Gambio\Admin\Modules\ProductDownload\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ProductDownload\Model\ProductDownload;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductDownloadStock;
use Gambio\Admin\Modules\ProductDownload\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadOperationPermitter;
use Gambio\Admin\Modules\ProductDownload\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ProductDownloadRepository
 *
 * @package    Gambio\Admin\Modules\ProductDownload\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11.
 */
class ProductDownloadRepository extends AbstractEventDispatchingRepository implements ProductDownloadRepositoryInterface
{
    public function __construct(
        private \Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository $repository
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsByProductId(ProductId $productId): ProductDownloads
    {
        return $this->repository->getProductDownloadsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadById(ProductOptionId $productOptionId): ProductDownload
    {
        return $this->repository->getProductDownloadById($productOptionId);
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
        return $this->repository->filterProductDownloads($productId,
                                                         $filters,
                                                         $sorting,
                                                         $pagination);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsTotalCount(ProductId $productId, Filters $filters): int
    {
        return $this->repository->getProductDownloadsTotalCount($productId, $filters);
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
    ): ProductOptionId {
        return $this->repository->createProductDownload($productId,
                                                        $optionAndOptionValueId,
                                                        $imageListId,
                                                        $optionValueCustomization,
                                                        $productDownloadStock,
                                                        $sortOrder);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleProductDownloads(array ...$creationArguments): ProductOptionIds
    {
        return $this->repository->createMultipleProductDownloads(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeProductDownloads(ProductDownload ...$productDownloads): void
    {
        $this->repository->storeProductDownloads(...$productDownloads);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteProductDownloads(ProductOptionId ...$ids): void
    {
        $this->repository->deleteProductDownloads(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteAllProductDownloadsByProductId(ProductId $productId): void
    {
        $this->repository->deleteAllProductDownloadsByProductId($productId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function registerOperationPermitter(ProductDownloadOperationPermitter $permitter): void
    {
        $this->repository->registerOperationPermitter($permitter);
    }
}