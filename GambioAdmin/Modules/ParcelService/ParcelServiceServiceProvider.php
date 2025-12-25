<?php
/* --------------------------------------------------------------
   ParcelServiceServiceProvider.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\ParcelService\App\Data\Filter\ParcelServiceFilterFactory;
use Gambio\Admin\Modules\ParcelService\App\Data\ParcelServiceMapper;
use Gambio\Admin\Modules\ParcelService\App\Data\ParcelServiceReader;
use Gambio\Admin\Modules\ParcelService\App\Data\ParcelServiceWriter;
use Gambio\Admin\Modules\ParcelService\App\ParcelServiceController;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFactory;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceFilterService;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceReadService;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceRepository;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ParcelServiceServiceProvider
 *
 * @package Gambio\Admin\Modules\ParcelService
 * @codeCoverageIgnore
 */
class ParcelServiceServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ParcelServiceWriteService::class,
            ParcelServiceReadService::class,
            ParcelServiceFilterService::class,
            ParcelServiceRepository::class,
            ParcelServiceFactory::class,
            ParcelServiceController::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ParcelServiceFactory::class);
        
        $this->application->registerShared(ParcelServiceFilterFactory::class);
        
        $this->application->registerShared(ParcelServiceMapper::class)->addArgument(ParcelServiceFactory::class);
        
        $this->application->registerShared(ParcelServiceReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(ParcelServiceWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(LanguageService::class);
        
        $this->application->registerShared(ParcelServiceRepository::class, App\Data\ParcelServiceRepository::class)
            ->addArgument(ParcelServiceMapper::class)
            ->addArgument(ParcelServiceReader::class)
            ->addArgument(ParcelServiceWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(ParcelServiceWriteService::class, App\ParcelServiceWriteService::class)
            ->addArgument(ParcelServiceRepository::class)
            ->addArgument(ParcelServiceFactory::class);
        
        $this->application->registerShared(ParcelServiceReadService::class, App\ParcelServiceReadService::class)
            ->addArgument(ParcelServiceRepository::class)
            ->addArgument(ParcelServiceFactory::class);
        
        $this->application->registerShared(ParcelServiceFilterService::class, App\ParcelServiceFilterService::class)
            ->addArgument(ParcelServiceRepository::class)
            ->addArgument(ParcelServiceFilterFactory::class);
        
        $this->application->registerShared(ParcelServiceController::class)
            ->addArgument(ParcelServiceReadService::class)
            ->addArgument(ParcelServiceFilterService::class)
            ->addArgument(ParcelServiceWriteService::class)
            ->addArgument(ParcelServiceFactory::class);
    }
}