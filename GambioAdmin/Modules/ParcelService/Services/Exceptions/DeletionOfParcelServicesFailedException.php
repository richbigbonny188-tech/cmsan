<?php
/* --------------------------------------------------------------
   DeletionOfParcelServicesFailedException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Services\Exceptions;

use Exception;

/**
 * Class DeletionOfParcelServicesFailedException
 *
 * @package Gambio\Admin\Modules\ParcelService\Services\Exceptions
 */
class DeletionOfParcelServicesFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfParcelServicesFailedException
     */
    public static function becauseOfException(Exception $exception): DeletionOfParcelServicesFailedException
    {
        return new self('Could not delete parcelServices because of previous error.', 0, $exception);
    }
}