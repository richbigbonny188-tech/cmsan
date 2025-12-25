<?php

namespace Gambio\Admin\Modules\Session\App\Actions;

use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class SessionKeepAlive
 *
 * @package Gambio\Admin\Modules\Session\App\Actions
 */
class SessionKeepAlive extends AbstractAction
{
    private const DEFAULT_SERVER_SESSION_LIFETIME = 1440;
    private const MILLISECONDS_MULTIPLIER         = 1000;
    private const TIMEOUT_ADJUSTMENT_MILLISECONDS = 500;
    private const CONFIG_KEY_KEEP_ALIVE           = 'configuration/SESSION_KEEP_ALIVE';
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * SessionKeepAlive constructor.
     *
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $timeout = $this->getSessionLifetime() - self::TIMEOUT_ADJUSTMENT_MILLISECONDS;
        $data    = [
            'data' => [
                'timeout'   => $timeout,
                'keepAlive' => $this->shouldKeepAlive(),
            ],
        ];
        
        return $response->withJson($data);
    }
    
    
    /**
     * Checks if session should be kept alive.
     *
     * @return bool
     */
    private function shouldKeepAlive(): bool
    {
        $config = $this->configurationService->find(self::CONFIG_KEY_KEEP_ALIVE);
        
        if ($config !== null && $config->value() !== null) {
            return $config->value() === '1';
        }
        
        return false;
    }
    
    
    /**
     * Returns the session lifetime in milliseconds.
     *
     * @return int
     */
    private function getSessionLifetime(): int
    {
        return $this->getServerSessionLifetime() * self::MILLISECONDS_MULTIPLIER;
    }
    
    
    /**
     * Returns the server session lifetime in seconds.
     *
     * @return int
     */
    private function getServerSessionLifetime(): int
    {
        $sessionMaxLifetime = @(int)ini_get('session.gc_maxlifetime');
        
        return $sessionMaxLifetime !== 0 ? $sessionMaxLifetime : self::DEFAULT_SERVER_SESSION_LIFETIME;
    }
}
