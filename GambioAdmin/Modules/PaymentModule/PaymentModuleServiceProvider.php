<?php
/* --------------------------------------------------------------
   PaymentModuleServiceProvider.php 2022-03-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\PaymentModule;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\PaymentModule\App\Actions\GetDisallowedPaymentMethodsAction;
use Gambio\Admin\Modules\PaymentModule\App\Actions\SetDisallowedPaymentMethodsAction;
use Gambio\Admin\Modules\PaymentModule\App\CustomerDisallowedPaymentMethodsReadService;
use Gambio\Admin\Modules\PaymentModule\App\CustomerDisallowedPaymentMethodsRepository;
use Gambio\Admin\Modules\PaymentModule\App\CustomerDisallowedPaymentMethodsWriteService;
use Gambio\Admin\Modules\PaymentModule\App\Data\CustomerDisallowedPaymentMethodsMapper;
use Gambio\Admin\Modules\PaymentModule\App\Data\CustomerDisallowedPaymentMethodsReader;
use Gambio\Admin\Modules\PaymentModule\App\Data\CustomerDisallowedPaymentMethodsWriter;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsReadService as CustomerDisallowedPaymentMethodsReadServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsRepository as CustomerDisallowedPaymentMethodsRepositoryInterface;
use Gambio\Admin\Modules\PaymentModule\Services\CustomerDisallowedPaymentMethodsWriteService as CustomerDisallowedPaymentMethodsWriteServiceInterface;
use Gambio\Admin\Modules\PaymentModule\Services\PaymentMethodFactory;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class PaymentModuleServiceProvider
 *
 * @package Gambio\Admin\Modules\PaymentModule
 */
class PaymentModuleServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            CustomerDisallowedPaymentMethodsReadServiceInterface::class,
            CustomerDisallowedPaymentMethodsWriteServiceInterface::class,
            GetDisallowedPaymentMethodsAction::class,
            SetDisallowedPaymentMethodsAction::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(PaymentMethodFactory::class);
        $this->application->registerShared(CustomerDisallowedPaymentMethodsMapper::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(CustomerDisallowedPaymentMethodsReader::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerDisallowedPaymentMethodsWriter::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(CustomerDisallowedPaymentMethodsRepositoryInterface::class,
                                           CustomerDisallowedPaymentMethodsRepository::class)
            ->addArgument(CustomerDisallowedPaymentMethodsReader::class)
            ->addArgument(CustomerDisallowedPaymentMethodsWriter::class)
            ->addArgument(CustomerDisallowedPaymentMethodsMapper::class);
        
        $this->application->registerShared(CustomerDisallowedPaymentMethodsReadServiceInterface::class,
                                           CustomerDisallowedPaymentMethodsReadService::class)
            ->addArgument(CustomerDisallowedPaymentMethodsRepositoryInterface::class);
        
        $this->application->registerShared(CustomerDisallowedPaymentMethodsWriteServiceInterface::class,
                                           CustomerDisallowedPaymentMethodsWriteService::class)
            ->addArgument(CustomerDisallowedPaymentMethodsRepositoryInterface::class)
            ->addArgument(PaymentMethodFactory::class);
        
        $this->application->registerShared(GetDisallowedPaymentMethodsAction::class)
            ->addArgument(CustomerDisallowedPaymentMethodsReadServiceInterface::class);
        
        $this->application->registerShared(SetDisallowedPaymentMethodsAction::class)
            ->addArgument(CustomerDisallowedPaymentMethodsWriteServiceInterface::class);
    }
}
