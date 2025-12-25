<?php
/* --------------------------------------------------------------
   TrackingCodeServiceProvider.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\TrackingCode\App\Data\Filter\TrackingCodeFilterFactory;
use Gambio\Admin\Modules\TrackingCode\App\Data\TrackingCodeMapper;
use Gambio\Admin\Modules\TrackingCode\App\Data\TrackingCodeReader;
use Gambio\Admin\Modules\TrackingCode\App\Data\TrackingCodeWriter;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFactory;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeFilterService;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeReadService;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeRepository;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class TrackingCodeServiceProvider
 *
 * @package Gambio\Admin\Modules\TrackingCode
 * @codeCoverageIgnore
 */
class TrackingCodeServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            TrackingCodeWriteService::class,
            TrackingCodeReadService::class,
            TrackingCodeFilterService::class,
            TrackingCodeRepository::class,
            TrackingCodeFactory::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(TrackingCodeFactory::class);
        
        $this->application->registerShared(TrackingCodeFilterFactory::class);
        
        $this->application->registerShared(TrackingCodeMapper::class)->addArgument(TrackingCodeFactory::class);
        
        $this->application->registerShared(TrackingCodeReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(TrackingCodeWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(LanguageService::class);
        
        $this->application->registerShared(TrackingCodeRepository::class, App\Data\TrackingCodeRepository::class)
            ->addArgument(TrackingCodeMapper::class)
            ->addArgument(TrackingCodeReader::class)
            ->addArgument(TrackingCodeWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(TrackingCodeWriteService::class, App\TrackingCodeWriteService::class)
            ->addArgument(TrackingCodeRepository::class)
            ->addArgument(TrackingCodeFactory::class);
        
        $this->application->registerShared(TrackingCodeReadService::class, App\TrackingCodeReadService::class)
            ->addArgument(TrackingCodeRepository::class)
            ->addArgument(TrackingCodeFactory::class);
        
        $this->application->registerShared(TrackingCodeFilterService::class, App\TrackingCodeFilterService::class)
            ->addArgument(TrackingCodeRepository::class)
            ->addArgument(TrackingCodeFilterFactory::class);
    }
}