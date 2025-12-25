<?php
/*--------------------------------------------------------------
   OperationHasNotBeenPermittedException.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadOperationPermitter;

/**
 * Class OperationHasNotBeenPermittedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Exceptions
 */
class OperationHasNotBeenPermittedException extends Exception
{
    /**
     * @param ProductDownloadOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forCreationByPermitter(
        ProductDownloadOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new static('Creation operation has not been permitted by "' . $permitter::class . '".');
    }
    
    
    /**
     * @param ProductDownloadOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forStorageByPermitter(
        ProductDownloadOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new static('Storage operation has not been permitted by "' . $permitter::class . '".');
    }
    
    
    /**
     * @param ProductDownloadOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forDeletionByPermitter(
        ProductDownloadOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new static('Deletion operation has not been permitted by "' . $permitter::class . '".');
    }
}