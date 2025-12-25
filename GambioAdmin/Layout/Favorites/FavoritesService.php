<?php
/* --------------------------------------------------------------
 FavoritesService.php 2020-07-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Favorites;

use Gambio\Admin\Layout\Favorites\Exceptions\FavoritesPersistenceException;

/**
 * Interface FavoritesService
 * @package Gambio\Admin\Layout\Favorites
 */
interface FavoritesService
{
    /**
     * Checks if $menuItemId is a favorite.
     * The currently logged in user is used to perform this action.
     *
     * @param string $menuItemId
     *
     * @return bool
     */
    public function has(string $menuItemId): bool;
    
    
    /**
     * Adds $menuItemId as new favorite menu item.
     * The currently logged in user is used to perform this action.
     *
     * @param string $menuItemId
     *
     * @throws FavoritesPersistenceException
     */
    public function add(string $menuItemId): void;
    
    
    /**
     * Removes $menuItemId from favorites.
     * The currently logged in user is used to perform this action.
     *
     * @param string $menuItemId
     *
     * @throws FavoritesPersistenceException
     */
    public function delete(string $menuItemId): void;
}