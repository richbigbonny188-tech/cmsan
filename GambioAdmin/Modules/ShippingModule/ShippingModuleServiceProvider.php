<?php
/* --------------------------------------------------------------
   ShippingModuleServiceProvider.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ShippingModule;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\ShippingModule\App\Actions\GetDisallowedShippingMethodsAction;
use Gambio\Admin\Modules\ShippingModule\App\Actions\SetDisallowedShippingMethodsAction;
use Gambio\Admin\Modules\ShippingModule\App\CustomerDisallowedShippingMethodsReadService;
use Gambio\Admin\Modules\ShippingModule\App\CustomerDisallowedShippingMethodsRepository;
use Gambio\Admin\Modules\ShippingModule\App\CustomerDisallowedShippingMethodsWriteService;
use Gambio\Admin\Modules\ShippingModule\App\Data\CustomerDisallowedShippingMethodsMapper;
use Gambio\Admin\Modules\ShippingModule\App\Data\CustomerDisallowedShippingMethodsReader;
use Gambio\Admin\Modules\ShippingModule\App\Data\CustomerDisallowedShippingMethodsWriter;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsReadService as CustomerDisallowedShippingMethodsReadServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsRepository as CustomerDisallowedShippingMethodsRepositoryInterface;
use Gambio\Admin\Modules\ShippingModule\Services\CustomerDisallowedShippingMethodsWriteService as CustomerDisallowedShippingMethodsWriteServiceInterface;
use Gambio\Admin\Modules\ShippingModule\Services\ShippingMethodFactory;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class ShippingModuleServiceProvider
 *
 * @package Gambio\Admin\Modules\ShippingModule
 */
class ShippingModuleServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerDisallowedShippingMethodsReadServiceInterface::class,
            CustomerDisallowedShippingMethodsWriteServiceInterface::class,
            GetDisallowedShippingMethodsAction::class,
            SetDisallowedShippingMethodsAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(ShippingMethodFactory::class);
        $this->application->registerShared(CustomerDisallowedShippingMethodsMapper::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(CustomerDisallowedShippingMethodsReader::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerDisallowedShippingMethodsWriter::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerDisallowedShippingMethodsRepositoryInterface::class,
                                           CustomerDisallowedShippingMethodsRepository::class)
            ->addArgument(CustomerDisallowedShippingMethodsReader::class)
            ->addArgument(CustomerDisallowedShippingMethodsWriter::class)
            ->addArgument(CustomerDisallowedShippingMethodsMapper::class);
        
        $this->application->registerShared(CustomerDisallowedShippingMethodsReadServiceInterface::class,
                                           CustomerDisallowedShippingMethodsReadService::class)
            ->addArgument(CustomerDisallowedShippingMethodsRepositoryInterface::class);
        
        $this->application->registerShared(CustomerDisallowedShippingMethodsWriteServiceInterface::class,
                                           CustomerDisallowedShippingMethodsWriteService::class)
            ->addArgument(CustomerDisallowedShippingMethodsRepositoryInterface::class)
            ->addArgument(ShippingMethodFactory::class);
        
        $this->application->registerShared(GetDisallowedShippingMethodsAction::class)
            ->addArgument(CustomerDisallowedShippingMethodsReadServiceInterface::class);
        
        $this->application->registerShared(SetDisallowedShippingMethodsAction::class)
            ->addArgument(CustomerDisallowedShippingMethodsWriteServiceInterface::class);
    }
}
