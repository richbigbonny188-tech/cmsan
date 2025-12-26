<?php
/*--------------------------------------------------------------------
 AdditionalOptionRepository.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\AdditionalOption;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptionIds;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionDoesNotExistException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\DeletionOfAdditionalOptionsFailedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\InsertionOfAdditionalOptionsFailedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\StorageOfAdditionalOptionsFailedException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;
use Gambio\Core\Filter\SqlFilters;
use Gambio\Core\Filter\SqlPagination;
use Gambio\Core\Filter\SqlSorting;

/**
 * Interface AdditionalOptionRepository
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services
 */
interface AdditionalOptionRepository
{
    /**
     * @param ProductId $productId
     *
     * @return AdditionalOptions
     */
    public function getAdditionalOptionsByProductId(ProductId $productId): AdditionalOptions;
    
    
    /**
     * @param AdditionalOptionId $additionalOptionId
     *
     * @return AdditionalOption
     *
     * @throws AdditionalOptionDoesNotExistException
     */
    public function getAdditionalOptionById(AdditionalOptionId $additionalOptionId): AdditionalOption;
    
    
    /**
     * @param ProductId                $productId
     * @param SqlFilters|Filters       $filters
     * @param SqlSorting|Sorting       $sorting
     * @param SqlPagination|Pagination $pagination
     *
     * @return AdditionalOptions
     */
    public function filterAdditionalOptions(
        ProductId                $productId,
        SqlFilters|Filters       $filters,
        SqlSorting|Sorting       $sorting,
        SqlPagination|Pagination $pagination
    ): AdditionalOptions;
    
    
    /**
     * @param ProductId          $productId
     * @param SqlFilters|Filters $filters
     *
     * @return int
     */
    public function getAdditionalOptionsTotalCount(ProductId $productId, SqlFilters|Filters $filters): int;
    
    
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param AdditionalOptionStock    $additionalOptionStock
     * @param int                      $sortOrder
     *
     * @return AdditionalOptionId
     *
     * @throws InsertionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     * @throws AdditionalOptionAlreadyExistsException
     */
    public function createAdditionalOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        AdditionalOptionStock    $additionalOptionStock,
        int                      $sortOrder = 0
    ): AdditionalOptionId;
    
    
    /**
     * @param array $creationArguments
     *
     * @return AdditionalOptionIds
     *
     * @throws InsertionOfAdditionalOptionsFailedException
     * @throws AdditionalOptionAlreadyExistsException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleAdditionalOptions(array ...$creationArguments): AdditionalOptionIds;
    
    
    /**
     * @param AdditionalOption ...$additionalOptions
     *
     * @throws StorageOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeAdditionalOptions(AdditionalOption ...$additionalOptions): void;
    
    
    /**
     * @param AdditionalOptionId ...$ids
     *
     * @throws DeletionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAdditionalOptions(AdditionalOptionId ...$ids): void;
    
    
    /**
     * @param ProductId $productId
     *
     * @throws DeletionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAllAdditionalOptionsByProductId(ProductId $productId): void;
    
    
    /**
     * @param AdditionalOptionOperationPermitter $permitter
     */
    public function registerOperationPermitter(AdditionalOptionOperationPermitter $permitter): void;
}