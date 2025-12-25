<?php
/*--------------------------------------------------------------
   OperationHasNotBeenPermittedException.php 2023-06-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionOperationPermitter;

/**
 * Class OperationHasNotBeenPermittedException
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\Exceptions
 */
class OperationHasNotBeenPermittedException extends Exception
{
    /**
     * @param AdditionalOptionOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forCreationByPermitter(
        AdditionalOptionOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        $message = 'Creation operation has not been permitted by "%s".';
        
        return new self(sprintf($message, $permitter::class));
    }
    
    
    /**
     * @param AdditionalOptionOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forStorageByPermitter(
        AdditionalOptionOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        $message = 'Storage operation has not been permitted by "%s".';
        
        return new self(sprintf($message, $permitter::class));
    }
    
    
    /**
     * @param AdditionalOptionOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forDeletionByPermitter(
        AdditionalOptionOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        $message = 'Deletion operation has not been permitted by "%s".';
        
        return new self(sprintf($message, $permitter::class));
    }
}