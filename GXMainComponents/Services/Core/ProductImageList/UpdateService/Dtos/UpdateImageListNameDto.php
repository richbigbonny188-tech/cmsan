<?php
/* --------------------------------------------------------------
  UpdateImageListNameDto.php 2020-02-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Dtos;

use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListNameDtoInterface;

/**
 * Class UpdateImageListNameDto
 * @package Gambio\ProductImageList\UpdateService\Dtos
 */
class UpdateImageListNameDto implements UpdateImageListNameDtoInterface
{
    /**
     * @var int
     */
    protected $imageListId;
    
    /**
     * @var string
     */
    protected $listName;
    
    
    /**
     * UpdateImageListNameDto constructor.
     *
     * @param int    $imageListId
     * @param string $listName
     */
    public function __construct(int $imageListId, string $listName)
    {
        $this->imageListId = $imageListId;
        $this->listName    = $listName;
    }
    
    
    /**
     * @inheritDoc
     */
    public function listId(): int
    {
        return $this->imageListId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function listName(): string
    {
        return $this->listName;
    }
}