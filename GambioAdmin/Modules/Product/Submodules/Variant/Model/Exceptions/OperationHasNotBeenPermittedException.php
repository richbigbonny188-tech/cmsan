<?php
/*--------------------------------------------------------------
   OperationHasNotBeenPermittedException.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantOperationPermitter;

/**
 * Class OperationHasNotBeenPermittedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions
 */
class OperationHasNotBeenPermittedException extends Exception
{
    /**
     * @param ProductVariantOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forCreationByPermitter(
        ProductVariantOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new self('Creation operation has not been permitted by "' . $permitter::class . '".');
    }
    
    
    /**
     * @param ProductVariantOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forStorageByPermitter(
        ProductVariantOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new self('Storage operation has not been permitted by "' . $permitter::class . '".');
    }
    
    
    /**
     * @param ProductVariantOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forDeletionByPermitter(
        ProductVariantOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new self('Deletion operation has not been permitted by "' . $permitter::class . '".');
    }
}