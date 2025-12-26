<?php
/* --------------------------------------------------------------
   WithdrawalServiceProvider.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Withdrawal\App\Data\Filter\WithdrawalFilterFactory;
use Gambio\Admin\Modules\Withdrawal\App\Data\WithdrawalMapper;
use Gambio\Admin\Modules\Withdrawal\App\Data\WithdrawalReader;
use Gambio\Admin\Modules\Withdrawal\App\Data\WithdrawalWriter;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFactory;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalFilterService;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalReadService;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalRepository;
use Gambio\Admin\Modules\Withdrawal\Services\WithdrawalWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class WithdrawalServiceProvider
 *
 * @package Gambio\Admin\Modules\Withdrawal
 * @codeCoverageIgnore
 */
class WithdrawalServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            WithdrawalWriteService::class,
            WithdrawalReadService::class,
            WithdrawalFilterService::class,
            WithdrawalRepository::class,
            WithdrawalFactory::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(WithdrawalFactory::class);
        
        $this->application->registerShared(WithdrawalFilterFactory::class);
        
        $this->application->registerShared(WithdrawalMapper::class)->addArgument(WithdrawalFactory::class);
        
        $this->application->registerShared(WithdrawalReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(WithdrawalWriter::class)->addArgument(Connection::class);
        
        $this->application->registerShared(WithdrawalRepository::class, App\Data\WithdrawalRepository::class)
            ->addArgument(WithdrawalMapper::class)
            ->addArgument(WithdrawalReader::class)
            ->addArgument(WithdrawalWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(WithdrawalWriteService::class, App\WithdrawalWriteService::class)
            ->addArgument(WithdrawalRepository::class)
            ->addArgument(WithdrawalFactory::class);
        
        $this->application->registerShared(WithdrawalReadService::class, App\WithdrawalReadService::class)
            ->addArgument(WithdrawalRepository::class)
            ->addArgument(WithdrawalFactory::class);
        
        $this->application->registerShared(WithdrawalFilterService::class, App\WithdrawalFilterService::class)
            ->addArgument(WithdrawalRepository::class)
            ->addArgument(WithdrawalFilterFactory::class);
    }
}