<?php
/*--------------------------------------------------------------------
 ProductOptionWriteService.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Services;

use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptionIds;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\DeletionOfProductOptionsFailedException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\InsertionOfProductOptionsFailedException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\ProductOptionAlreadyExistsException;
use Gambio\Admin\Modules\ProductOption\Model\Exceptions\StorageOfProductOptionsFailedException;
use Gambio\Admin\Modules\ProductOption\Model\ProductOption;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionId;
use Gambio\Admin\Modules\ProductOption\Model\ValueObjects\ProductOptionStock;

/**
 * Interface ProductOptionWriteService
 * @package Gambio\Admin\Modules\ProductOption\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\Proxies\AdditionalOptionWriteServiceProxy
 */
interface ProductOptionWriteService
{
    /**
     * @param int $productId
     * @param OptionAndOptionValueId $optionAndOptionValueId
     * @param ImageListId $imageListId
     * @param OptionValueCustomization $optionValueCustomization
     * @param ProductOptionStock $productOptionStock
     * @param int $sortOrder
     *
     * @return ProductOptionId
     *
     * @throws InsertionOfProductOptionsFailedException
     *
     */
    public function createProductOption(
        int                      $productId,
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
     */
    public function createMultipleProductOptions(array ...$creationArguments): ProductOptionIds;


    /**
     * @param ProductOption ...$productOptions
     *
     * @throws StorageOfProductOptionsFailedException
     */
    public function storeProductOptions(ProductOption ...$productOptions): void;


    /**
     * @param int ...$ids
     *
     * @throws DeletionOfProductOptionsFailedException
     */
    public function deleteProductOptions(int ...$ids): void;


    /**
     * @param int $productId
     *
     * @throws DeletionOfProductOptionsFailedException
     */
    public function deleteAllProductOptionsByProductId(int $productId): void;
}