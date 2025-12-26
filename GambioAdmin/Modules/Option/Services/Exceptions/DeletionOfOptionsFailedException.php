<?php
/* --------------------------------------------------------------
   DeletionOfOptionsFailedException.php 2021-03-31
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

/**
 * Class DeletionOfOptionsFailedException
 *
 * @package Gambio\Admin\Modules\Option\Model\Exceptions
 * @codeCoverageIgnore
 */
class DeletionOfOptionsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfOptionsFailedException
     */
    public static function becauseOfPreviousException(Exception $exception): DeletionOfOptionsFailedException
    {
        return new self('Deletion of options failed because of previous exception.', 0, $exception);
    }
}