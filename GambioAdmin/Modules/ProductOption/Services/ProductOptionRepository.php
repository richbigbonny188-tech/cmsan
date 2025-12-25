<?php
/*--------------------------------------------------------------------
 ProductOptionRepository.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Services;

use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptions;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\DeletionOfProductOptionsFailedException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\InsertionOfProductOptionsFailedException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\ProductOptionAlreadyExistsException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\ProductOptionDoesNotExistException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\StorageOfProductOptionsFailedException;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionStock;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface ProductOptionRepository
 *
 * @package    Gambio\Admin\Modules\ProductOption\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository
 */
interface ProductOptionRepository
{
    /**
     * @param ProductId $productId
     *
     * @return ProductOptions
     */
    public function getProductOptionsByProductId(ProductId $productId): ProductOptions;
    
    
    /**
     * @param ProductOptionId $productOptionId
     *
     * @return ProductOption
     *
     * @throws ProductOptionDoesNotExistException
     */
    public function getProductOptionById(ProductOptionId $productOptionId): ProductOption;
    
    
    /**
     * @param ProductId  $productId
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return ProductOptions
     */
    public function filterProductOptions(
        ProductId  $productId,
        Filters    $filters,
        Sorting    $sorting,
        Pagination $pagination
    ): ProductOptions;
    
    
    /**
     * @param ProductId $productId
     * @param Filters   $filters
     *
     * @return int
     */
    public function getProductOptionsTotalCount(ProductId $productId, Filters $filters): int;
    
    
    /**
     * @param ProductId                $productId
     * @param OptionAndOptionValueId   $optionAndOptionValueId
     * @param ImageListId              $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductOptionStock       $productOptionStock
     * @param int                      $sortOrder
     *
     * @return ProductOptionId
     *
     * @throws InsertionOfProductOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createProductOption(
        ProductId                $productId,
        OptionAndOptionValueId   $optionAndOptionValueId,
        ImageListId              $imageListId,
        OptionValueCustomization $optionValueCustomization,
        ProductOptionStock       $productOptionStock,
        int                      $sortOrder = 0
    ): ProductOptionId;
    
    
    /**
     * @param array $creationArguments
     *
     * @return ProductOptionIds
     *
     * @throws InsertionOfProductOptionsFailedException
     * @throws ProductOptionAlreadyExistsException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleProductOptions(array ...$creationArguments): ProductOptionIds;
    
    
    /**
     * @param ProductOption ...$productOptions
     *
     * @throws StorageOfProductOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeProductOptions(ProductOption ...$productOptions): void;
    
    
    /**
     * @param ProductOptionId ...$ids
     *
     * @throws DeletionOfProductOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteProductOptions(ProductOptionId ...$ids): void;
    
    
    /**
     * @param ProductId $productId
     *
     * @throws DeletionOfProductOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAllProductOptionsByProductId(ProductId $productId): void;
    
    
    /**
     * @param ProductOptionOperationPermitter $permitter
     */
    public function registerOperationPermitter(ProductOptionOperationPermitter $permitter): void;
}