<?php
/* --------------------------------------------------------------
   WithdrawalNotFoundException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Services\Exceptions;

use Exception;

/**
 * Class WithdrawalNotFoundException
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services\Exceptions
 */
class WithdrawalNotFoundException extends Exception
{
    /**
     * Creates a new WithdrawalNotFoundException instance for a provided ID.
     *
     * @param int $id
     *
     * @return static
     */
    public static function forId(int $id): self
    {
        return new self('Could no found withdrawal with ID "' . $id . '".');
    }
}