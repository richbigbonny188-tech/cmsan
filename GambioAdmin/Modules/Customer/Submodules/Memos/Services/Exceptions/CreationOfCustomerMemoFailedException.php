<?php
/*--------------------------------------------------------------
   CreationOfCustomerMemoFailedException.php 2022-09-14
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
 * Class CreationOfCustomerMemoFailedException
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions
 */
class CreationOfCustomerMemoFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfCustomerMemoFailedException
     */
    final public static function becauseOfException(Exception $exception): CreationOfCustomerMemoFailedException
    {
        $message = 'Could not create customer memo because of previous error (%s).';
        
        return new self(sprintf($message, get_class($exception)), 0, $exception);
    }
}