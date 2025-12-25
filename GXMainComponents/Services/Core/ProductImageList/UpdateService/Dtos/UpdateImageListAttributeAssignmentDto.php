<?php
/* --------------------------------------------------------------
  UpdateImageListAttributeAssignmentDto.php 2020-02-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Dtos;

use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListAttributeAssignmentDtoInterface;

/**
 * Class UpdateImageListAttributeAssignmentDto
 * @package Gambio\ProductImageList\UpdateService\Dtos
 */
class UpdateImageListAttributeAssignmentDto implements UpdateImageListAttributeAssignmentDtoInterface
{
    /**
     * @var int
     */
    protected $attributesId;
    
    /**
     * @var int
     */
    protected $listId;
    
    
    /**
     * UpdateImageListAttributeAssignmentDto constructor.
     *
     * @param int $attributesId
     * @param int $listId
     */
    public function __construct(int $attributesId, int $listId)
    {
        $this->attributesId = $attributesId;
        $this->listId       = $listId;
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
    public function attributeId(): int
    {
        return $this->attributesId;
    }
}