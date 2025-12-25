<?php
/* --------------------------------------------------------------
   CreationOfTrackingCodesFailedException.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Services\Exceptions;

use Exception;

/**
 * Class CreationOfTrackingCodesFailedException
 *
 * @package Gambio\Admin\Modules\TrackingCode\Services\Exceptions
 */
class CreationOfTrackingCodesFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfTrackingCodesFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfTrackingCodesFailedException
    {
        return new self('Could not create tracking codes because of previous error: ' . $exception->getMessage(),
                        0,
                        $exception);
    }
}