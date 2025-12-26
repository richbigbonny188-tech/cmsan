<?php
/* --------------------------------------------------------------
   CreationOfWithdrawalFailedException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Services\Exceptions;

use Exception;

/**
 * Class CreationOfWithdrawalFailedException
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services\Exceptions
 */
class CreationOfWithdrawalFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfWithdrawalFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfWithdrawalFailedException
    {
        return new self('Could not create withdrawal because of previous error.', 0, $exception);
    }
}