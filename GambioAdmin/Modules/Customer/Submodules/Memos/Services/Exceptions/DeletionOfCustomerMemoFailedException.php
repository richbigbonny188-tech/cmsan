<?php
/*--------------------------------------------------------------
   DeletionOfCustomerMemoFailedException.php 2021-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions;

use Exception;

/**
 * Class DeletionOfCustomerMemoFailedException
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions
 */
class DeletionOfCustomerMemoFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfCustomerMemoFailedException
     */
    final public static function becauseOfException(Exception $exception): DeletionOfCustomerMemoFailedException
    {
        $message = 'Could not delete customer memo because of previous error (%s).';
        
        return new self(sprintf($message, get_class($exception)), 0, $exception);
    }
}