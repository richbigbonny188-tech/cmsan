<?php
/*--------------------------------------------------------------
   UserNavigationHistoryServiceProvider.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\UserNavigationHistory;

use Doctrine\DBAL\Connection;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Shop\UserNavigationHistory\Database\Reader\CategoryDescriptionReader;
use Gambio\Shop\UserNavigationHistory\Database\Reader\MysqlCategoryDescriptionReader;
use Gambio\Shop\UserNavigationHistory\Database\Repository\HistoryRepository;
use Gambio\Shop\UserNavigationHistory\Factories\HistoryFactory;

/**
 * Class UserNavigationHistoryServiceProvider
 *
 * @package Gambio\Shop\UserNavigationHistory
 */
class UserNavigationHistoryServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @var array
     */
    public function provides(): array
    {
        return [UserNavigationHistoryService::class];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
    
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(UserNavigationHistoryService::class)
            ->addArgument(HistoryFactory::class)
            ->addArgument(HistoryRepository::class);
        $this->application->registerShared(HistoryFactory::class);
        $this->application->registerShared(HistoryRepository::class)->addArgument(CategoryDescriptionReader::class);
        $this->application->registerShared(CategoryDescriptionReader::class, MysqlCategoryDescriptionReader::class)
            ->addArgument(Connection::class);
    }
}