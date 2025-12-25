<?php
/* --------------------------------------------------------------
 SlimAppRegistration.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\Http\HttpResponseFactory;
use Gambio\Core\Application\Routing\HttpRouteCollector;
use Gambio\Core\Application\Routing\HttpRouteParser;
use Gambio\Core\Application\Routing\RouteCollector;
use Gambio\Core\Application\Routing\RouteParser;
use Gambio\Core\Application\ValueObjects\Url;
use Psr\Http\Message\ResponseFactoryInterface;
use RuntimeException;
use Slim\App as SlimApp;
use Slim\Factory\AppFactory as SlimAppFactory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Interfaces\CallableResolverInterface;

/**
 * Class SlimAppRegistration
 *
 * @package Gambio\Core\Application\Bootstrapper
 */
class SlimAppRegistration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $url = $this->getUrlValueObject($application);
        
        $responseFactory = SlimAppFactory::determineResponseFactory();
        if (($responseFactory instanceof HttpResponseFactory) === false) {
            // TODO: This work around can be removed as soon as we don't use the legacy DI container anymore.
            /** @var DecoratedResponseFactory $responseFactory */
            $responseFactory = new HttpResponseFactory($responseFactory);
            SlimAppFactory::setResponseFactory($responseFactory);
        }
        
        $slim = SlimAppFactory::createFromContainer($application);
        $slim->addRoutingMiddleware();
        $slim->setBasePath(rtrim($url->path(), '/'));
        
        $application->registerShared(ResponseFactoryInterface::class, $responseFactory);
        $application->registerShared(SlimApp::class, $slim);
        
        $this->registerRouteCollector($application, $slim);
        $this->registerCallableResolver($application, $slim);
        $this->registerRouteParser($application, $slim);
    }
    
    
    /**
     * @param Application $application
     *
     * @return Url
     */
    private function getUrlValueObject(Application $application): Url
    {
        if ($application->has(Url::class) === false) {
            throw new RuntimeException('Can not boot Slim app without registered component: ' . Url::class);
        }
        
        return $application->get(Url::class);
    }
    
    
    /**
     * @param Application $application
     * @param SlimApp     $slim
     */
    private function registerRouteCollector(Application $application, SlimApp $slim): void
    {
        $application->registerShared(RouteCollector::class,
            static function () use ($slim) {
                return new HttpRouteCollector($slim);
            });
    }
    
    
    /**
     * @param Application $application
     * @param SlimApp     $slim
     */
    private function registerRouteParser(Application $application, SlimApp $slim): void
    {
        $application->registerShared(RouteParser::class,
            static function () use ($slim) {
                $slimRouteParser = $slim->getRouteCollector()->getRouteParser();
                
                return new HttpRouteParser($slimRouteParser);
            });
    }
    
    
    /**
     * @param Application $application
     * @param SlimApp     $slim
     */
    private function registerCallableResolver(Application $application, SlimApp $slim): void
    {
        $application->registerShared(CallableResolverInterface::class,
            static function () use ($slim) {
                return $slim->getCallableResolver();
            });
    }
}