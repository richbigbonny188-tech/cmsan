<?php
/* --------------------------------------------------------------
  UpdateSortingDto.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Dtos;

use Gambio\ProductImageList\UpdateService\Interfaces\UpdateSortingDtoInterface;

/**
 * Class UpdateSortingDto
 * @package Gambio\ProductImageList\UpdateService\Dtos
 */
class UpdateSortingDto implements UpdateSortingDtoInterface
{
    /**
     * @var int
     */
    protected $imageId;
    
    /**
     * @var int
     */
    protected $sortIndex;
    
    
    /**
     * UpdateSortingDto constructor.
     *
     * @param int $imageId
     * @param int $sortIndex
     */
    public function __construct(int $imageId, int $sortIndex)
    {
        $this->imageId = $imageId;
        $this->sortIndex = $sortIndex;
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
    public function sortIndex(): int
    {
        return $this->sortIndex;
    }
}