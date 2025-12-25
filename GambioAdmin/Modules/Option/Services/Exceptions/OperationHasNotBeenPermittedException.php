<?php
/* --------------------------------------------------------------
   OperationHasNotBeenPermittedException.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\Option\Services\OptionOperationPermitter;
use Throwable;

/**
 * Class OperationHasNotBeenPermittedException
 *
 * @package Gambio\Admin\Modules\Option\Model\Exceptions
 * @codeCoverageIgnore
 */
class OperationHasNotBeenPermittedException extends Exception
{
    /**
     * @param OptionOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forCreationByPermitter(OptionOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new self('Creation operation has not been permitted by "' . get_class($permitter) . '".');
    }
    
    
    /**
     * @param OptionOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forStorageByPermitter(OptionOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new self('Storage operation has not been permitted by "' . get_class($permitter) . '".');
    }
    
    
    /**
     * @param OptionOperationPermitter $permitter
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function forDeletionByPermitter(OptionOperationPermitter $permitter
    ): OperationHasNotBeenPermittedException {
        return new self('Deletion operation has not been permitted by "' . get_class($permitter) . '".');
    }
    
    
    /**
     * @param Throwable $exception
     *
     * @return OperationHasNotBeenPermittedException
     */
    public static function becauseOfPreviousException(Throwable $exception): OperationHasNotBeenPermittedException
    {
        return new self('Deletion operation has not been permitted because of a previous exception.', 0, $exception);
    }
}