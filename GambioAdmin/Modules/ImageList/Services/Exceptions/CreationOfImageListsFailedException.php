<?php
/* --------------------------------------------------------------
   CreationOfImageListsFailedException.php 2021-06-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services\Exceptions;


use Exception;

/**
 * Class CreationOfImageListsFailedException
 * @package Gambio\Admin\Modules\ImageList\Services\Exceptions
 */
class CreationOfImageListsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return CreationOfImageListsFailedException
     */
    public static function becauseOfException(Exception $exception): CreationOfImageListsFailedException
    {
        return new self('Could not create image list because of previous exception.', 0, $exception);
    }
}