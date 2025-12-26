<?php
/*--------------------------------------------------------------
   DeletionOfImageListsFailedException.php 2021-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services\Exceptions;

use Exception;

/**
 * Class DeletionOfImageListsFailedException
 * @package Gambio\Admin\Modules\ImageList\Services\Exceptions
 */
class DeletionOfImageListsFailedException extends Exception
{
    /**
     * @param Exception $exception
     *
     * @return DeletionOfImageListsFailedException
     */
    public static function becauseOfException(Exception $exception): DeletionOfImageListsFailedException
    {
        return new self('Deletion of image lists failed because of previous exception.', 0, $exception);
    }
}