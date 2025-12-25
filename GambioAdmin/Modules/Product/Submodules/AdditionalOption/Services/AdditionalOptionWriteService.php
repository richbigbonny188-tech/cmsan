<?php
/*--------------------------------------------------------------------
 AdditionalOptionWriteService.php 2023-06-06
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
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionStock;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\ImageListId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionAndOptionValueId;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\OptionValueCustomization;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\AdditionalOptionAlreadyExistsException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\DeletionOfAdditionalOptionsFailedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\InsertionOfAdditionalOptionsFailedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions\StorageOfAdditionalOptionsFailedException;

/**
 * Interface AdditionalOptionWriteService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services
 */
interface AdditionalOptionWriteService
{
    /**
     * @param int                      $productId
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
     */
    public function createAdditionalOption(
        int                      $productId,
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
     * @param int ...$ids
     *
     * @throws DeletionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAdditionalOptions(int ...$ids): void;
    
    
    /**
     * @param int $productId
     *
     * @throws DeletionOfAdditionalOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteAllAdditionalOptionsByProductId(int $productId): void;
}