<?php
/* --------------------------------------------------------------
   StorageOfLanguagesFailed.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Model\Exceptions;

use Exception;

/**
 * Class StorageOfLanguagesFailed
 *
 * @package Gambio\Admin\Modules\Language\Model\Exceptions
 */
class StorageOfLanguagesFailed extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfLanguagesFailed
     */
    public static function becauseOfException(Exception $exception): StorageOfLanguagesFailed
    {
        return new self('Could not store languages because of previous error.', 0, $exception);
    }
}