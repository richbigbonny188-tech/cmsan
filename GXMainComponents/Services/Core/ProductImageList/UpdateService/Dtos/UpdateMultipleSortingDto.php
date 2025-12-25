<?php
/* --------------------------------------------------------------
  UpdateMultipleSortingDto.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Dtos;

use Gambio\ProductImageList\UpdateService\Interfaces\UpdateMultipleSortingDtoInterface;
use Gambio\ProductImageList\UpdateService\Interfaces\UpdateSortingDtoInterface;

/**
 * Class UpdateMultipleSortingDto
 * @package Gambio\ProductImageList\UpdateService\Dtos
 */
class UpdateMultipleSortingDto implements UpdateMultipleSortingDtoInterface
{
    /**
     * @var array
     */
    protected $dtos;
    
    
    /**
     * UpdateMultipleSortingDto constructor.
     *
     * @param UpdateSortingDtoInterface[] $dtos
     */
    public function __construct(array $dtos)
    {
        $this->dtos = $dtos;
    }
    
    
    /**
     * @inheritDoc
     */
    public function dtos(): array
    {
        return $this->dtos;
    }
}