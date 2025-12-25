<?php
/* --------------------------------------------------------------
 FavoritesServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Favorites;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Favorites\Service\FavoritesRepository;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class FavoritesServiceProvider
 * @package Gambio\Admin\Layout\Favorites
 */
class FavoritesServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            FavoritesService::class,
            AddFavorite::class,
            DeleteFavorites::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AddFavorite::class)->addArgument(FavoritesService::class);
        $this->application->registerShared(DeleteFavorites::class)->addArgument(FavoritesService::class);
        $this->application->registerShared(FavoritesService::class, Service\FavoritesService::class)
            ->addArgument(FavoritesRepository::class);
        $this->application->registerShared(FavoritesRepository::class)->addArguments([
                                                                                         Connection::class,
                                                                                         UserPreferences::class
                                                                                     ]);
    }
}