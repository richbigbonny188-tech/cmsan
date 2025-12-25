<?php
/*--------------------------------------------------------------
   OperationHasNotBeenPermittedException.php 2021-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\ImageListOperationPermitter;

/**
 * Class OperationHasNotBeenPermittedException
 * @package Gambio\Admin\Modules\ImageList\Services\Exceptions
 */
class OperationHasNotBeenPermittedException extends Exception
{
    /**
     * @param ImageListOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forStorageByPermitter(
        ImageListOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        
        $message = 'Storage operation has not been permitted by "%s".';
        $message = sprintf($message, get_class($permitter));
        
        return new self($message, 3);
    }
    
    /**
     * @param ImageListOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forCreationByPermitter(
        ImageListOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
    
        $message = 'Creation operation has not been permitted by "%s".';
        $message = sprintf($message, get_class($permitter));
    
        return new self($message, 2);
    }
    
    /**
     * @param ImageListOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forDeletetionByPermitter(
        ImageListOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        
        $message = 'Deletion operation has not been permitted by "%s".';
        $message = sprintf($message, get_class($permitter));
        
        return new self($message, 1);
    }
}