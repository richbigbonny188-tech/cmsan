<?php
/* --------------------------------------------------------------
   AdminServiceProvider.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Admin\App\Data\AdminMapper;
use Gambio\Admin\Modules\Admin\App\Data\AdminReader;
use Gambio\Admin\Modules\Admin\App\Data\AdminRepository;
use Gambio\Admin\Modules\Admin\App\Data\AdminWriter;
use Gambio\Admin\Modules\Admin\Services\AdminFactory;
use Gambio\Admin\Modules\Admin\Services\AdminReadService;
use Gambio\Admin\Modules\Admin\Services\AdminWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdminServiceProvider
 *
 * @package Gambio\Admin\Modules\Admin
 */
class AdminServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AdminReadService::class,
            AdminWriteService::class,
            AdminFactory::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AdminMapper::class)->addArgument(AdminFactory::class);
        
        $this->application->registerShared(AdminReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(AdminWriter::class)->addArgument(Connection::class);
        
        $this->application->registerShared(AdminRepository::class)
            ->addArgument(AdminMapper::class)
            ->addArgument(AdminReader::class)
            ->addArgument(AdminWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(AdminFactory::class);
        
        $this->application->registerShared(AdminReadService::class, App\AdminReadService::class)
            ->addArgument(AdminRepository::class)
            ->addArgument(AdminFactory::class);
        
        $this->application->registerShared(AdminWriteService::class, App\AdminWriteService::class)
            ->addArgument(AdminRepository::class);
    }
}