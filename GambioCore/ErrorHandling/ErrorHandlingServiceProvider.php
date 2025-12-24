<?php
/* --------------------------------------------------------------
   ErrorHandlingServiceProvider.php 2023-05-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\ErrorHandling;

use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageErrorHandlerService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\ErrorHandling\App\Data\SentryEventHintFactory;
use Gambio\Core\ErrorHandling\App\Data\SentryExceptionTransmitter;
use Gambio\Core\ErrorHandling\App\Data\TransmissionCache;
use Gambio\Core\ErrorHandling\App\Data\TransmissionConsentStorage;
use Gambio\Core\ErrorHandling\App\DebugDataProvider;
use Gambio\Core\ErrorHandling\App\ExceptionTransmitter;
use Gambio\Core\ErrorHandling\Services\DefaultErrorHandler;
use Gambio\Core\ErrorHandling\Services\ExceptionTransmitter as ExceptionTransmitterInterface;
use Gambio\Core\Logging\LoggerBuilder;

/**
 * Class ErrorHandlingServiceProvider
 *
 * @package Gambio\Core\ErrorHandling
 */
class ErrorHandlingServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            DefaultErrorHandler::class,
            ExceptionTransmitterInterface::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(DebugDataProvider::class);
        
        $this->application->registerShared(DefaultErrorHandler::class, App\DefaultErrorHandler::class)
            ->addArgument(LoggerBuilder::class)
            ->addArgument(UserFriendlyErrorPageErrorHandlerService::class)
            ->addArgument(Environment::class)
            ->addArgument(DebugDataProvider::class);
        
        $this->registerExceptionTransmitter();
    }
    
    
    /**
     * @return void
     */
    private function registerExceptionTransmitter(): void
    {
        $this->application->register(TransmissionConsentStorage::class)->addArgument(ConfigurationService::class);
        $this->application->register(TransmissionCache::class)->addArgument(CacheFactory::class);
        
        $this->application->register(SentryEventHintFactory::class);
        $this->application->register(SentryExceptionTransmitter::class)->addArgument(SentryEventHintFactory::class);
        
        $this->application->registerShared(ExceptionTransmitterInterface::class, ExceptionTransmitter::class)
            ->addArgument(TransmissionConsentStorage::class)
            ->addArgument(TransmissionCache::class)
            ->addArgument(SentryExceptionTransmitter::class);
    }
}