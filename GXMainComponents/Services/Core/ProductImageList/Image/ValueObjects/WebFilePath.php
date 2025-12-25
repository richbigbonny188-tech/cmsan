<?php
/* --------------------------------------------------------------
  WebFilePath.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use Gambio\ProductImageList\Image\Exceptions\PathIsNotAnUrlException;
use JsonSerializable;

/**
 * Class WebFilePath
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
class WebFilePath implements JsonSerializable
{
    /**
     * @var string
     */
    protected $webFilePath;
    
    
    /**
     * WebFilePath constructor.
     *
     * @param string $webFilePath
     *
     * @throws PathIsNotAnUrlException
     */
    public function __construct(string $webFilePath)
    {
        if (filter_var($webFilePath, FILTER_VALIDATE_URL) === false) {
            
            throw new PathIsNotAnUrlException('The url: "' . $webFilePath . '" is not a valid URL');
        }
        
        $this->webFilePath = $webFilePath;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->webFilePath;
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value();
    }
}