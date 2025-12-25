<?php
/*--------------------------------------------------------------
   SentryBootstrapper.php 2023-05-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Core\ErrorHandling;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\ErrorHandling\App\Data\SentryBeforeSendCallback;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Class SentryBootstrapper
 *
 * @package Gambio\Core\ErrorHandling
 */
class SentryBootstrapper implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $shopDirectory = $this->path($application)->base();
        $configPath    = $shopDirectory . '/GXModules/Gambio/ErrorReporting/configuration.json';
        
        if (!file_exists($configPath)) {
            return;
        }
        
        $sentryConfig = json_decode(file_get_contents($configPath), true);
        
        if (!$sentryConfig['active']) {
            return;
        }
        
        $env = $this->env($application);
        
        $environmentName = $env->isDev()
                           || str_contains(Application::VERSION,
                                           'develop') ? 'development' : 'production';
        $sentryOptions   = ['dsn' => $sentryConfig['dsn'], 'before_send' => new SentryBeforeSendCallback];
        
        if ($env->isCloud()) {
            
            $sentryOptions = array_merge($sentryOptions, [
                'environment'   => $environmentName,
                'release'       => Application::VERSION,
                'prefixes'      => [$shopDirectory, $shopDirectory],
                'error_types'   => E_ALL & ~E_NOTICE & ~E_USER_NOTICE & ~E_CORE_ERROR & ~E_CORE_WARNING & ~E_STRICT
                                   & ~E_DEPRECATED,
                'send_attempts' => 1,
            ]);
        } else {
            //  turning of automatic reporting
            $sentryOptions = array_merge($sentryOptions, ['default_integrations' => false]);
        }
        
        \Sentry\init($sentryOptions);
    }
    
    
    /**
     * @param Application $app
     *
     * @return Environment
     */
    private function env(Application $app): Environment
    {
        try {
            return $app->get(Environment::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
            throw new RuntimeException(Environment::class . ' is unavailable in container.');
        }
    }
    
    
    /**
     * @param Application $app
     *
     * @return Path
     */
    private function path(Application $app): Path
    {
        try {
            return $app->get(Path::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
            throw new RuntimeException(Path::class . ' is unavailable in container.');
        }
    }
}