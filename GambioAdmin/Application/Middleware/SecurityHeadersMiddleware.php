<?php
/* --------------------------------------------------------------
   SecurityHeadersMiddleware.php 2020-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Application\Middleware;

use Gambio\Core\Configuration\App\Creation\ConfigurationFinderBuilder;
use Gambio\Core\Configuration\App\NamespaceConfigurationFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class SecurityHeadersMiddleware
 *
 * @package Gambio\Admin\Application\Middleware
 */
class SecurityHeadersMiddleware implements MiddlewareInterface
{
    /**
     * @var NamespaceConfigurationFinder
     */
    private $finder;
    
    
    /**
     * SecurityHeadersMiddleware constructor.
     *
     * @param ConfigurationFinderBuilder $builder
     */
    public function __construct(ConfigurationFinderBuilder $builder)
    {
        $this->finder = $builder->buildNamespaceFinder('gm_configuration');
    }
    
    
    /**
     * Add "X-Frame-Options" header if content type is html.
     *
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $config   = 'SEND_X_FRAME_OPTIONS_SAMEORIGIN_HEADER';
        
        if ($this->isConfigEnabled($config)) {
            $contentType = $response->getHeader('Content-Type');
            if (count($contentType) === 0 || strpos($contentType[0], 'text/html') !== false) {
                $response = $response->withHeader('X-Frame-Options', 'SAMEORIGIN');
            }
        }
        
        return $response;
    }
    
    
    /**
     * Checks if given configuration key represents an enabled configuration.
     *
     * @param string $configKey
     *
     * @return bool
     */
    private function isConfigEnabled(string $configKey): bool
    {
        $value = $this->finder->get($configKey);
        
        return $value === 'true' || $value === '1';
    }
}