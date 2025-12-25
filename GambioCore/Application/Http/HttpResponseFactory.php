<?php
/* --------------------------------------------------------------
   HttpResponseFactory.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Factory\DecoratedResponseFactory;

/**
 * Class HttpResponseFactory
 *
 * @package Gambio\Core\Application\Http
 * @codeCoverageIgnore
 */
class HttpResponseFactory implements ResponseFactoryInterface
{
    /**
     * @var DecoratedResponseFactory
     */
    private $slimResponseFactory;
    
    
    /**
     * HttpResponseFactory constructor.
     *
     * @param DecoratedResponseFactory $slimResponseFactory
     */
    public function __construct(DecoratedResponseFactory $slimResponseFactory)
    {
        $this->slimResponseFactory = $slimResponseFactory;
    }
    
    
    /**
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $internalResponse = $this->slimResponseFactory->createResponse($code, $reasonPhrase);
        
        return new HttpResponse($internalResponse);
    }
}