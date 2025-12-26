<?php
/* --------------------------------------------------------------
  UpdateImageListCombinationAssignmentDtoInterface.php 2020-02-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\ProductImageList\UpdateService\Interfaces;

/**
 * Interface UpdateImageListCombinationAssignmentDtoInterface
 * @package Gambio\ProductImageList\UpdateService\Interfaces
 */
interface UpdateImageListCombinationAssignmentDtoInterface
{
    /**
     * @return int
     */
    public function listId(): int;
    
    
    /**
     * @return int
     */
    public function combiId(): int;
}