<?php
/* --------------------------------------------------------------
   UserFriendlyErrorPageServiceProvider.php 2022-12-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\UserFriendlyErrorPage;

use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Reader\PageNotFoundErrorPageReader;
use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Reader\UnexpectedErrorErrorPageReader;
use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer\ErrorPageGenerator;
use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer\PageNotFoundErrorPageWriter;
use Gambio\Admin\Modules\UserFriendlyErrorPage\App\Data\Writer\UnexpectedErrorErrorPageWriter;
use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageErrorHandlerService;
use Gambio\Admin\Modules\UserFriendlyErrorPage\Services\UserFriendlyErrorPageService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Logging\LoggerBuilder;

/**
 * Class UserFriendlyErrorPageServiceProvider
 *
 * @package Gambio\Admin\Modules\UserFriendlyErrorPage
 */
class UserFriendlyErrorPageServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            UserFriendlyErrorPageErrorHandlerService::class,
            UserFriendlyErrorPageService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(PageNotFoundErrorPageReader::class)
            ->addArgument(ConfigurationService::class)
            ->addArgument(__DIR__ . '/../../../public/error_pages');
        $this->application->registerShared(UnexpectedErrorErrorPageReader::class)
            ->addArgument(Environment::class)
            ->addArgument(__DIR__ . '/../../../public/error_pages')
            ->addArgument(__DIR__ . '/../../../cache/user_friendly_error_messages_active.flag');
        $this->application->registerShared(ErrorPageGenerator::class)
            ->addArgument(ConfigurationService::class)
            ->addArgument(Url::class)
            ->addArgument(Environment::class)
            ->addArgument(__DIR__ . '/../../../public/theme/styles/system');
        $this->application->registerShared(PageNotFoundErrorPageWriter::class)
            ->addArgument(LoggerBuilder::class)
            ->addArgument(ErrorPageGenerator::class)
            ->addArgument(ConfigurationService::class)
            ->addArgument(__DIR__ . '/../../../public/error_pages');
        $this->application->registerShared(UnexpectedErrorErrorPageWriter::class)
            ->addArgument(LoggerBuilder::class)
            ->addArgument(ErrorPageGenerator::class)
            ->addArgument(__DIR__ . '/../../../public/error_pages')
            ->addArgument(__DIR__ . '/../../../cache/user_friendly_error_messages_active.flag');
        
        $this->application->registerShared(UserFriendlyErrorPageErrorHandlerService::class,
                                           App\UserFriendlyErrorPageErrorHandlerService::class)
            ->addArgument(Url::class)
            ->addArgument(UnexpectedErrorErrorPageReader::class);
        
        $this->application->registerShared(UserFriendlyErrorPageService::class,
            function () {
                $reader = [
                    $this->application->get(PageNotFoundErrorPageReader::class),
                    $this->application->get(UnexpectedErrorErrorPageReader::class),
                ];
                $writer = [
                    $this->application->get(PageNotFoundErrorPageWriter::class),
                    $this->application->get(UnexpectedErrorErrorPageWriter::class),
                ];
                
                return new App\UserFriendlyErrorPageService($reader, $writer);
            });
    }
}