<?php
/*--------------------------------------------------------------
   OptionWriteService.php 2023-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Option\Services;

use Gambio\Admin\Modules\Option\Model\Collections\NewOptionValues;
use Gambio\Admin\Modules\Option\Model\Collections\OptionDetails;
use Gambio\Admin\Modules\Option\Model\Collections\OptionIds;
use Gambio\Admin\Modules\Option\Model\Option;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionType;
use Gambio\Admin\Modules\Option\Services\Exceptions\CreationOfOptionsFailedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\DeletionOfOptionsFailedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\Option\Services\Exceptions\StorageOfOptionsFailedException;

/**
 * Interface OptionWriteService
 *
 * @package    Gambio\Admin\Modules\Option\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains will be refactored into
 *             submodules too. All important changes will be documented in the developer journal as soon as they are
 *             implemented.
 */
interface OptionWriteService
{
    /**
     * Creates and stores a new option.
     *
     * @param OptionDetails   $details
     * @param NewOptionValues $newOptionValues
     * @param OptionType      $type
     * @param int             $sortOrder
     *
     * @return OptionId
     *
     * @throws CreationOfOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createOption(
        OptionDetails   $details,
        NewOptionValues $newOptionValues,
        OptionType      $type,
        int             $sortOrder
    ): OptionId;
    
    
    /**
     * Creates and stores multiple new options.
     *
     * @param array ...$creationArgs
     *
     * @return OptionIds
     *
     * @throws CreationOfOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function createMultipleOptions(array ...$creationArgs): OptionIds;
    
    
    /**
     * Stores one or more existing options.
     *
     * @param Option ...$options
     *
     * @throws StorageOfOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeOptions(Option ...$options): void;
    
    
    /**
     * Deletes one or more existing options.
     *
     * @param int ...$optionIds
     *
     * @throws DeletionOfOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function deleteOptions(int ...$optionIds): void;
    
    
    /**
     * Updates the sort order of the Options
     *
     * @param Option ...$options
     *
     * @throws StorageOfOptionsFailedException
     * @throws OperationHasNotBeenPermittedException
     */
    public function storeOptionsSortOrder(Option ...$options): void;
}