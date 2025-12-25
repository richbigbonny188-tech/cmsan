<?php
/* --------------------------------------------------------------
   RateLimitMiddleware.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Middleware;

use Gambio\Core\Cache\Services\SafeCache;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

/**
 * Class RateLimitMiddleware
 *
 * @package Gambio\Api\Application\Middleware
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    /**
     * Defines the maximum request limit for an authorized client.
     */
    private const DEFAULT_RATE_LIMIT = 5000;
    
    /**
     * Defines the duration of an API session in seconds.
     */
    private const DEFAULT_SESSION_LIFETIME = 15 * 60;
    
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;
    
    /**
     * @var SafeCache
     */
    private $apiSessionsCache;
    
    /**
     * @var array
     */
    private $rateLimit;
    
    
    /**
     * RateLimitMiddleware constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param SafeCache                $apiSessionsCache
     */
    public function __construct(ResponseFactoryInterface $responseFactory, SafeCache $apiSessionsCache)
    {
        $this->responseFactory  = $responseFactory;
        $this->apiSessionsCache = $apiSessionsCache;
    }
    
    
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authorization     = $request->getHeader('Authorization')[0] ?? '';
        $sessionIdentifier = md5($authorization);
        if (empty($authorization) || empty($sessionIdentifier)) {
            throw new RuntimeException('No authorization header provided.');
        }
        
        if ($this->checkRateLimit($sessionIdentifier)) {
            $response = $handler->handle($request);
            
            return $response->withHeader('X-Rate-Limit-Limit', (string)$this->rateLimit['limit'])
                ->withHeader('X-Rate-Limit-Remaining', (string)$this->rateLimit['remaining'])
                ->withHeader('X-Rate-Limit-Reset', (string)$this->rateLimit['reset']);
        }
        
        return $this->responseFactory->createResponse(429)
            ->withHeader('X-Rate-Limit-Limit', (string)$this->rateLimit['limit'])
            ->withHeader('X-Rate-Limit-Remaining', (string)$this->rateLimit['remaining'])
            ->withHeader('X-Rate-Limit-Reset', (string)$this->rateLimit['reset'])
            ->withJson(['errors' => ['Request limit reached.']]);
    }
    
    
    /**
     * Checks the current rate limit.
     *
     * @param string $sessionIdentifier
     *
     * @return bool
     */
    private function checkRateLimit(string $sessionIdentifier): bool
    {
        $this->fetchRateLimit($sessionIdentifier);
        if ($this->rateLimit['remaining'] > 0) {
            $this->reduceRateLimit($sessionIdentifier);
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Fetches the current rate limit from the sessions cache.
     *
     * @param string $sessionIdentifier
     */
    private function fetchRateLimit(string $sessionIdentifier): void
    {
        $defaultRateLimit = [
            'limit'     => self::DEFAULT_RATE_LIMIT,
            'remaining' => self::DEFAULT_RATE_LIMIT,
            'reset'     => time() + self::DEFAULT_SESSION_LIFETIME,
        ];
        
        $this->rateLimit = $this->apiSessionsCache->get($sessionIdentifier, $defaultRateLimit);
    }
    
    
    /**
     * Reduces the current rate limit by one and stores the session.
     *
     * @param string $sessionIdentifier
     */
    private function reduceRateLimit(string $sessionIdentifier): void
    {
        $this->rateLimit['remaining']--;
        
        $this->apiSessionsCache->set($sessionIdentifier, $this->rateLimit, self::DEFAULT_SESSION_LIFETIME);
    }
}