<?php
/* --------------------------------------------------------------
  ImageListImageDto.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Dtos;

use Gambio\ProductImageList\ReadService\Interfaces\ImageListImageDtoInterface;

/**
 * Class ImageListImageDto
 * @package Gambio\ProductImageList\ReadService\Dtos
 */
class ImageListImageDto implements ImageListImageDtoInterface
{
    /**
     * @var int
     */
    protected $imageId;
    
    /**
     * @var int
     */
    protected $listId;
    
    /**
     * @var string
     */
    protected $localPath;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    
    /**
     * ImageListImageDto constructor.
     *
     * @param int    $imageId
     * @param int    $listId
     * @param string $localPath
     * @param int    $sortOrder
     */
    public function __construct(int $imageId, int $listId, string $localPath, int $sortOrder)
    {
        $this->imageId   = $imageId;
        $this->listId    = $listId;
        $this->localPath = $localPath;
        $this->sortOrder = $sortOrder;
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
    public function listId(): int
    {
        return $this->listId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function localPath(): string
    {
        return $this->localPath;
    }
    
    
    /**
     * @inheritDoc
     */
    public function sortOder(): int
    {
        return $this->sortOrder;
    }
}