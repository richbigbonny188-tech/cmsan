<?php
/* --------------------------------------------------------------
  LocalFilePath.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\Image\ValueObjects;

use Gambio\ProductImageList\Image\Exceptions\FileDoesNotExistException;
use JsonSerializable;

/**
 * Class LocalFilePath
 * @package Gambio\ProductImageList\Image\ValueObjects
 */
class LocalFilePath implements JsonSerializable
{
    protected const WHITESPACE_REGEXP = '/%20/';
    /**
     * @var string
     */
    protected $localFilePath;
    
    
    /**
     * LocalFilePath constructor.
     *
     * @param string $localFilePath
     *
     * @throws FileDoesNotExistException
     */
    public function __construct(string $localFilePath)
    {
        if (file_exists($localFilePath) === false) {
        
            throw new FileDoesNotExistException('File: "' . $localFilePath . '" does not exists');
        }
        
        $this->localFilePath = $localFilePath;
    }
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->localFilePath;
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