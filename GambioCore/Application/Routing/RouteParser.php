<?php
/* --------------------------------------------------------------
   RouteParser.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Routing;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * Interface RouteParser
 *
 * @package Gambio\Core\Application\Routing
 */
interface RouteParser
{
    /**
     * Build the path for a named route excluding the base path
     *
     *
     * @param string $routeName   Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function relativeUrlFor(string $routeName, array $data = [], array $queryParams = []): string;
    
    
    /**
     * Build the path for a named route including the base path
     *
     * @param string $routeName   Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function urlFor(string $routeName, array $data = [], array $queryParams = []): string;
    
    
    /**
     * Get fully qualified URL for named route
     *
     * @param UriInterface $uri
     * @param string       $routeName   Route name
     * @param array        $data        Named argument replacement data
     * @param array        $queryParams Optional query string parameters
     *
     * @return string
     */
    public function fullUrlFor(UriInterface $uri, string $routeName, array $data = [], array $queryParams = []): string;
}