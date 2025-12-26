<?php
/* --------------------------------------------------------------
   CreationOfParcelServicesFailedException.php 2021-04-07
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
 * Class CreationOfParcelServicesFailedException
 *
 * @package Gambio\Admin\Modules\ParcelService\Services\Exceptions
 */
class CreationOfParcelServicesFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfParcelServicesFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfParcelServicesFailedException
    {
        return new self('Could not create parcelService because of previous error.', 0, $exception);
    }
}