<?php
/* --------------------------------------------------------------
  UpdateSortingDtoInterface.php 2020-01-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Interfaces;

/**
 * Interface UpdateSortingDtoInterface
 * @package Gambio\ProductImageList\UpdateService\Interfaces
 */
interface UpdateSortingDtoInterface
{
    /**
     * @return int
     */
    public function imageId(): int;
    
    
    /**
     * @return int
     */
    public function sortIndex(): int;
}