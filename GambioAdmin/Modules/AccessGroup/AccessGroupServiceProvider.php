<?php
/* --------------------------------------------------------------
   AccessGroupServiceProvider.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\AccessGroup\App\Data\AccessGroupMapper;
use Gambio\Admin\Modules\AccessGroup\App\Data\AccessGroupReader;
use Gambio\Admin\Modules\AccessGroup\App\Data\AccessGroupRepository;
use Gambio\Admin\Modules\AccessGroup\App\Data\AccessGroupWriter;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupFactory;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupReadService;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Language\Services\LanguageService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AccessGroupServiceProvider
 *
 * @package Gambio\Admin\Modules\AccessGroup
 */
class AccessGroupServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AccessGroupFactory::class,
            AccessGroupReadService::class,
            AccessGroupWriteService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AccessGroupMapper::class)->addArgument(AccessGroupFactory::class);
        
        $this->application->registerShared(AccessGroupReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(AccessGroupWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(LanguageService::class);
        
        $this->application->registerShared(AccessGroupRepository::class)
            ->addArgument(AccessGroupMapper::class)
            ->addArgument(AccessGroupReader::class)
            ->addArgument(AccessGroupWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(AccessGroupFactory::class);
        
        $this->application->registerShared(AccessGroupReadService::class,
                                           App\AccessGroupReadService::class)
            ->addArgument(AccessGroupRepository::class)
            ->addArgument(AccessGroupFactory::class);
        
        $this->application->registerShared(AccessGroupWriteService::class,
                                           App\AccessGroupWriteService::class)
            ->addArgument(AccessGroupRepository::class)
            ->addArgument(AccessGroupFactory::class);
    }
}