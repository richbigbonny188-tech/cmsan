<?php
/*--------------------------------------------------------------
   ImageListDoesNotExistException.php 2021-05-11
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
 * Class ImageListDoesNotExistException
 * @package Gambio\Admin\Modules\ImageList\Services\Exceptions
 */
class ImageListDoesNotExistException extends Exception
{
    /**
     * @param int $ImageListId
     *
     * @return ImageListDoesNotExistException
     */
    public static function forImageListId(int $ImageListId): ImageListDoesNotExistException
    {
        return new self('Image list with ID ' . $ImageListId . ' does not exist.');
    }
}