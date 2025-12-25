<?php
/* --------------------------------------------------------------
 FavouritesHashListRepository.php 2020-03-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Repositories\PostCache;

use Gambio\Admin\Layout\Menu\Models\Cached\FavouritesHashList;

/**
 * Interface FavouritesHashListRepository
 * @package Gambio\Admin\Layout\Menu\Repository
 */
interface FavouritesHashListRepository
{
    /**
     * Returns a list with link hashes of menu items that are marked as favourite.
     *
     * @return FavouritesHashList
     */
    public function favouritesList(): FavouritesHashList;
}