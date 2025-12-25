<?php
/* --------------------------------------------------------------
 FavoritesService.php 2023-06-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Favorites\Service;

use Doctrine\DBAL\Exception;
use Gambio\Admin\Layout\Favorites\Exceptions\FavoritesPersistenceException;
use Gambio\Admin\Layout\Favorites\FavoritesService as FavoritesServiceInterface;

/**
 * Class FavoritesService
 * @package Gambio\Admin\Layout\Favorites\Service
 */
class FavoritesService implements FavoritesServiceInterface
{
    /**
     * @var FavoritesRepository
     */
    private $repository;
    
    
    /**
     * FavoritesService constructor.
     *
     * @param FavoritesRepository $repository
     */
    public function __construct(FavoritesRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function has(string $menuItemId): bool
    {
        return $this->repository->has($menuItemId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function add(string $menuItemId): void
    {
        try {
            $this->repository->add($menuItemId);
        } catch (Exception $e) {
            throw new FavoritesPersistenceException(
                "Could not add menu item with id '$menuItemId' to favorites", 0, $e
            );
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function delete(string $menuItemId): void
    {
        try {
            $this->repository->delete($menuItemId);
        } catch (Exception $e) {
            throw new FavoritesPersistenceException(
                "Could not delete menu item with id '$menuItemId' from favorites", 0, $e
            );
        }
    }
}