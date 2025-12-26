<?php
/* --------------------------------------------------------------
 CoreServiceProviderRegistration.php 2021-07-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Bootstrapper;

use Gambio\Admin\Modules\UserFriendlyErrorPage\UserFriendlyErrorPageServiceProvider;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ServiceProviders\DoctrineQbServiceProvider;
use Gambio\Core\Application\ServiceProviders\HttpActionsServiceProvider;
use Gambio\Core\Auth\AuthenticationServiceProvider;
use Gambio\Core\Cache\CacheServiceProvider;
use Gambio\Core\Configuration\ConfigurationServiceProvider;
use Gambio\Core\ErrorHandling\ErrorHandlingServiceProvider;
use Gambio\Core\Filesystem\FilesystemServiceProvider;
use Gambio\Core\GXModules\GXModulesServiceProvider;
use Gambio\Core\Images\ImagesServiceProvider;
use Gambio\Core\Language\LanguageServiceProvider;
use Gambio\Core\Logging\LoggingServiceProvider;
use Gambio\Core\Permission\PermissionServiceProvider;
use Gambio\Core\TemplateEngine\TemplateEngineServiceProvider;
use Gambio\Core\TextManager\TextManagerServiceProvider;
use Gambio\Core\UserConfiguration\UserConfigurationServiceProvider;
use Gambio\Core\VatValidation\VatValidationServiceProvider;

/**
 * Class CoreServiceProviderRegistration
 *
 * @package Gambio\Core\Application\Bootstrapper
 * @codeCoverageIgnore
 *
 * Here, we register all service providers off the shop's core components.
 */
class CoreServiceProviderRegistration implements Bootstrapper
{
    private const SERVICE_PROVIDERS = [
        LoggingServiceProvider::class,
        AuthenticationServiceProvider::class,
        CacheServiceProvider::class,
        DoctrineQbServiceProvider::class,
        FilesystemServiceProvider::class,
        ImagesServiceProvider::class,
        LanguageServiceProvider::class,
        TextManagerServiceProvider::class,
        HttpActionsServiceProvider::class,
        ConfigurationServiceProvider::class,
        TemplateEngineServiceProvider::class,
        PermissionServiceProvider::class,
        GXModulesServiceProvider::class,
        UserFriendlyErrorPageServiceProvider::class,
        ErrorHandlingServiceProvider::class,
        UserConfigurationServiceProvider::class,
        VatValidationServiceProvider::class,
    ];
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        foreach (self::SERVICE_PROVIDERS as $provider) {
            $application->registerProvider($provider);
        }
    }
}