<?php
/* --------------------------------------------------------------
 HttpKernel.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Kernel;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\Http\HttpRequest;
use Gambio\Core\Application\Kernel;
use RuntimeException;
use Slim\App as SlimApp;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Http\ServerRequest as SlimRequest;

/**
 * Class HttpKernel
 * @package Gambio\Core\Application\Kernel
 */
class HttpKernel implements Kernel
{
    /**
     * @var Application
     */
    private $application;
    
    
    /**
     * @inheritDoc
     */
    public function bootstrap(Application $application, Bootstrapper $bootstrapper): void
    {
        $this->application = $application;
        $bootstrapper->boot($application);
    }
    
    
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        if (!$this->application) {
            throw new RuntimeException('The kernel must be bootstrapped first!');
        }
        
        if (!$this->application->has(SlimApp::class)) {
            throw new RuntimeException('Slim must be bootstrapped for the HTTP-Kernel!');
        }
        
        $serverRequestCreator = ServerRequestCreatorFactory::create();
        
        /** @var SlimRequest $slimRequest */
        $slimRequest = $serverRequestCreator->createServerRequestFromGlobals();
        
        /** @var SlimApp $slim */
        $slim = $this->application->get(SlimApp::class);
        $slim->run(new HttpRequest($slimRequest));
    }
}