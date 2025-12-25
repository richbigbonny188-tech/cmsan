<?php
/*--------------------------------------------------------------
   ImageListFilterService.php 2021-05-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\Services;

use Gambio\Admin\Modules\ImageList\Model\Collections\ImageLists;

/**
 * Interface ImageListFilterService
 * @package Gambio\Admin\Modules\ImageList\Services
 */
interface ImageListFilterService
{
    /**
     * Returns a filtered, sorted, paginated collection of image lists.
     *
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return ImageLists
     */
    public function filterImageLists(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): ImageLists;
    
    /**
     * Returns the total count of filtered image lists.
     *
     * @param array $filters
     *
     * @return int
     */
    public function getImageListsTotalCount(array $filters): int;
}