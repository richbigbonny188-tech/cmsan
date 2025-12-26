<?php
/* --------------------------------------------------------------
  InvalidLocalFilePath.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use Gambio\ProductImageList\Image\Exceptions\PathIsNotAnUrlException;

class InvalidWebFilePath extends WebFilePath
{
    protected const INVALID_IMAGE = 'images/product_images/original_images/invalid_image.png';
    /**
     * @var string
     */
    protected $baseWebPath;

    /**
     * InvalidWebFilepath constructor.
     *
     * @param string $baseWebPath
     * @param string $relativeWebPath
     */
    public function __construct(string $baseWebPath, string $relativeWebPath)
    {
        $this->webFilePath = $baseWebPath.$relativeWebPath;
        $this->baseWebPath = $baseWebPath;
    }

    #[\ReturnTypeWillChange]

    public function jsonSerialize()
    {
        return   $this->baseWebPath.static::INVALID_IMAGE;
    }

}