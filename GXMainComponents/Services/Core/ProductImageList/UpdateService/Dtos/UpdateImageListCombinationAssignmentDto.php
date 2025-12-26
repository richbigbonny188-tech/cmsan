<?php
/* --------------------------------------------------------------
  UpdateImageListCombinationAssignmentDto.php 2020-02-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Dtos;

use Gambio\ProductImageList\UpdateService\Interfaces\UpdateImageListCombinationAssignmentDtoInterface;

/**
 * Class UpdateImageListCombinationAssignmentDto
 * @package Gambio\ProductImageList\UpdateService\Dtos
 */
class UpdateImageListCombinationAssignmentDto implements UpdateImageListCombinationAssignmentDtoInterface
{
    /**
     * @var int
     */
    protected $combiId;
    
    /**
     * @var int
     */
    protected $listId;
    
    
    /**
     * UpdateImageListCombinationAssignmentDto constructor.
     *
     * @param int $combiId
     * @param int $listId
     */
    public function __construct(int $combiId, int $listId)
    {
        $this->combiId = $combiId;
        $this->listId  = $listId;
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
    public function combiId(): int
    {
        return $this->combiId;
    }
}