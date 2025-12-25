<?php
/* --------------------------------------------------------------
   HttpRouteParser.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Routing;

use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class HttpRouteParser
 *
 * @package Gambio\Core\Application\Routing
 * @codeCoverageIgnore
 */
class HttpRouteParser implements RouteParser
{
    /**
     * @var RouteParserInterface
     */
    private $internal;
    
    
    /**
     * HttpRouteParser constructor.
     *
     * @param RouteParserInterface $internal
     */
    public function __construct(RouteParserInterface $internal)
    {
        $this->internal = $internal;
    }
    
    
    /**
     * @inheritDoc
     */
    public function relativeUrlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->internal->relativeUrlFor($routeName, $data, $queryParams);
    }
    
    
    /**
     * @inheritDoc
     */
    public function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->internal->urlFor($routeName, $data, $queryParams);
    }
    
    
    /**
     * @inheritDoc
     */
    public function fullUrlFor(UriInterface $uri, string $routeName, array $data = [], array $queryParams = []): string
    {
        return $this->internal->fullUrlFor($uri, $routeName, $data, $queryParams);
    }
}