<?php
/*--------------------------------------------------------------
   ImageDoesNotExistException.php 2021-06-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services\Exceptions;

use Exception;
use Gambio\Admin\Modules\ImageList\Model\Entities\Image;
use Gambio\Admin\Modules\ImageList\Model\ValueObjects\ImagePath;

/**
 * Class ImageDoesNotExistException
 * @package Gambio\Admin\Modules\ImageList\Services\Exceptions
 */
class ImageDoesNotExistException extends Exception
{
    /**
     * @param ImagePath $imageLocalPath
     *
     * @return ImageDoesNotExistException
     */
    public static function forImageLocalPath(ImagePath $imageLocalPath): ImageDoesNotExistException
    {
        $message = 'Image with the relative path "%s" does not exists in this image list.';
        
        return new self(sprintf($message, $imageLocalPath->relativePath()));
    }
    
    
    /**
     * @param Image $image
     *
     * @return ImageDoesNotExistException
     */
    public static function forImage(Image $image): ImageDoesNotExistException
    {
        $message = 'Image with the relative path "%s" does not exists in this image list.';
    
        return new self(sprintf($message, $image->relativePath()));
    }
}