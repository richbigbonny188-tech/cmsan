<?php
/* --------------------------------------------------------------
 TextManagerServiceProvider.php 2020-11-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\TextManager;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\ClearCacheService;

/**
 * Class TextManagerServiceProvider
 * @package Gambio\Core\TextManager
 */
class TextManagerServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            Services\TextManager::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(Services\TextManager::class, App\TextManager::class)
            ->addArguments([Services\TextPhraseRepository::class, UserPreferences::class]);
        $this->application->registerShared(Services\TextPhraseRepository::class, App\Data\TextPhraseRepository::class)
            ->addArguments([CacheFactory::class, App\Data\TextPhraseReader::class]);
        $this->application->registerShared(App\Data\TextPhraseReader::class)->addArgument(Connection::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(ClearCacheService::class)->invokeMethod('addNamespaceToTextCaches', ['text_cache']);
    }
}