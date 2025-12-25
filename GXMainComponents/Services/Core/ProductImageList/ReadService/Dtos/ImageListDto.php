<?php
/* --------------------------------------------------------------
  ImageListDto.php 2020-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\ProductImageList\ReadService\Dtos;

use Gambio\ProductImageList\ImageList\ValueObjects\ListId;
use Gambio\ProductImageList\ImageList\ValueObjects\ListName;
use Gambio\ProductImageList\ReadService\Interfaces\ImageListDtoInterface;

/**
 * Class ImageListDto
 * @package Gambio\ProductImageList\ReadService\Dtos
 */
class ImageListDto implements ImageListDtoInterface
{
    /**
     * @var int
     */
    protected $listId;
    
    /**
     * @var string
     */
    protected $listName;
    
    
    /**
     * ImageListDto constructor.
     *
     * @param int    $listId
     * @param string $listName
     */
    public function __construct(int $listId, string $listName)
    {
        $this->listId   = $listId;
        $this->listName = $listName;
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
    public function listName(): string
    {
        return $this->listName;
    }
}