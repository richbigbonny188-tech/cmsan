<?php
/* --------------------------------------------------------------
  ImageListImageTextDto.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Dtos;

use Gambio\ProductImageList\ReadService\Interfaces\ImageListImageTextDtoInterface;

/**
 * Class ImageListImageTextDto
 * @package Gambio\ProductImageList\ReadService\Dtos
 */
class ImageListImageTextDto implements ImageListImageTextDtoInterface
{
    /**
     * @var int
     */
    protected $imageId;
    
    /**
     * @var string
     */
    protected $textType;
    
    /**
     * @var string
     */
    protected $textValue;
    
    /**
     * @var int
     */
    protected $languageId;
    
    
    /**
     * ImageListImageTextDto constructor.
     *
     * @param int    $imageId
     * @param string $textType
     * @param string $textValue
     * @param int    $languageId
     */
    public function __construct(int $imageId, string $textType, string $textValue, int $languageId)
    {
        $this->imageId    = $imageId;
        $this->textType   = $textType;
        $this->textValue  = $textValue;
        $this->languageId = $languageId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function imageId(): int
    {
        return $this->imageId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function textType(): string
    {
        return $this->textType;
    }
    
    
    /**
     * @inheritDoc
     */
    public function textValue(): string
    {
        return $this->textValue;
    }
    
    
    /**
     * @inheritDoc
     */
    public function languageId(): int
    {
        return $this->languageId;
    }
}