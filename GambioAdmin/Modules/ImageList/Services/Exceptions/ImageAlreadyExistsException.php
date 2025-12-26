<?php
/*--------------------------------------------------------------
   ImageAlreadyExistsException.php 2021-06-16
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
 * Class ImageAlreadyExistsException
 * @package Gambio\Admin\Modules\ImageList\Services\Exceptions
 */
class ImageAlreadyExistsException extends Exception
{
    /**
     * @param string $path
     *
     * @return static
     */
    final public static function forPath(string $path): self
    {
        $message = "Could not store image at the relative path '%s' since the file already exists.";
        
        return new self(sprintf($message, $path));
    }
}