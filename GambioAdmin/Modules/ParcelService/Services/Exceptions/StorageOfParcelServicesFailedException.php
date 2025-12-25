<?php
/* --------------------------------------------------------------
   StorageOfParcelServicesFailedException.php 2021-04-07
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
 * Class StorageOfParcelServicesFailedException
 *
 * @package Gambio\Admin\Modules\ParcelService\Services\Exceptions
 */
class StorageOfParcelServicesFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return StorageOfParcelServicesFailedException
     */
    public static function becauseOfException(Exception $exception): StorageOfParcelServicesFailedException
    {
        return new self('Could not store parcelServices because of previous error.', 0, $exception);
    }
}