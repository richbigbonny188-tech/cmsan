<?php
/* --------------------------------------------------------------
   LanguageServiceProvider.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\App\Data\Filter\LanguageFilterFactory;
use Gambio\Admin\Modules\Language\App\Data\LanguageMapper;
use Gambio\Admin\Modules\Language\App\Data\LanguageReader;
use Gambio\Admin\Modules\Language\Services\LanguageFactory;
use Gambio\Admin\Modules\Language\Services\LanguageFilterService;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Admin\Modules\Language\Services\LanguageRepository;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class LanguageServiceProvider
 *
 * @package Gambio\Admin\Modules\Language
 * @codeCoverageIgnore
 */
class LanguageServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            LanguageReadService::class,
            LanguageFilterService::class,
            LanguageRepository::class,
            LanguageFactory::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(LanguageFactory::class);
        
        $this->application->registerShared(LanguageFilterFactory::class);
        
        $this->application->registerShared(LanguageMapper::class)->addArgument(LanguageFactory::class);
        
        $this->application->registerShared(LanguageReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(LanguageRepository::class, App\Data\LanguageRepository::class)
            ->addArgument(LanguageMapper::class)
            ->addArgument(LanguageReader::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(LanguageReadService::class, App\LanguageReadService::class)
            ->addArgument(LanguageRepository::class)
            ->addArgument(LanguageFactory::class);
        
        $this->application->registerShared(LanguageFilterService::class, App\LanguageFilterService::class)
            ->addArgument(LanguageRepository::class)
            ->addArgument(LanguageFilterFactory::class);
    }
}