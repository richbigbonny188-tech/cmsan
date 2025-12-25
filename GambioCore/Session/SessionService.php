<?php
/* --------------------------------------------------------------
 SessionService.php 2020-07-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

namespace Gambio\Core\Session;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Server;
use Gambio\Core\Application\ValueObjects\Url;
use RuntimeException;

/**
 * Class SessionService
 * @package Gambio\Core\Session
 */
class SessionService
{
    /**
     * @var SessionRepository
     */
    private $repository;
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var Path
     */
    private $path;
    
    /**
     * @var Server
     */
    private $server;
    
    
    /**
     * SessionService constructor.
     *
     * @param SessionRepository $repository
     * @param Url               $url
     * @param Path              $path
     * @param Server            $server
     */
    public function __construct(SessionRepository $repository, Url $url, Path $path, Server $server)
    {
        $this->repository = $repository;
        $this->url        = $url;
        $this->path       = $path;
        $this->server     = $server;
    }
    
    
    public function setupSession(): void
    {
        $sessionStatus = session_status();
        if ($sessionStatus === PHP_SESSION_DISABLED) {
            throw new RuntimeException('PHP sessions MUST be enabled!');
        }
        
        if ($sessionStatus === PHP_SESSION_ACTIVE) {
            return;
        }
        
        $shopHost      = parse_url($this->url->host(), PHP_URL_HOST);
        $shopHostParts = explode('.', $shopHost);
        if (count($shopHostParts) > 1 && $shopHostParts[0] === 'www') {
            unset($shopHostParts[0]);
        }
        $host       = implode('.', $shopHostParts);
        $webPath    = $this->url->path();
        $webPath    = empty($webPath) ? '/' : $webPath;
        $serverPath = $this->path->base();
        
        $this->configureIni();
        $this->configureCookie($host, $webPath);
        $this->configureName($host, $webPath, $serverPath);
        
        [$handler, $path] = $this->determineSaveHandlerAndPath();
        @ini_set('session.save_handler', $handler);
        session_save_path($path);
    }
    
    
    /**
     * PHP ini session configuration.
     */
    private function configureIni(): void
    {
        @ini_set('session.gc_maxlifetime', defined('SESSION_GC_MAXLIFETIME') ? (int)SESSION_GC_MAXLIFETIME : 1440);
        @ini_set('session.gc_probability', defined('SESSION_GC_PROBABILITY') ? (int)SESSION_GC_PROBABILITY : 100);
        @ini_set('session.gc_divisor', defined('SESSION_GC_DIVISOR') ? (int)SESSION_GC_DIVISOR : 1000);
        @ini_set('session.use_trans_sid', false);
        @ini_set('session.use_strict_mode', true);
        @ini_set('session.use_only_cookies', true);
    }
    
    
    /**
     * Session cookie configuration.
     *
     * @param string $host
     * @param string $path
     */
    private function configureCookie(string $host, string $path): void
    {
        $lifetime = 0;
        $secure   = $this->server->sslEnabled();
        $httpOnly = true;
        $sameSite = 'Lax';
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params(
                [
                    'lifetime' => $lifetime,
                    'path'     => $path,
                    'domain'   => $host,
                    'secure'   => $secure,
                    'httponly' => $httpOnly,
                    'sameSite' => $sameSite
                ]
            );
        } else {
            session_set_cookie_params(0, $path, $host, $secure, $httpOnly);
        }
    }
    
    
    /**
     * Session name configuration.
     *
     * @param string $host
     * @param string $webPath
     * @param string $serverPath
     */
    private function configureName(string $host, string $webPath, string $serverPath): void
    {
        $name = "GXsid_{$this->repository->getNamePostfix($host, $webPath, $serverPath)}";
        session_name($name);
    }
    
    
    /**
     * Determines the session save handler type and path.
     *
     * @return array|string[]
     */
    private function determineSaveHandlerAndPath(): array
    {
        if (defined('SESSION_SAVE_HANDLER') && defined('SESSION_SAVE_PATH')) {
            return [SESSION_SAVE_HANDLER, SESSION_SAVE_PATH];
        }
        
        return ['files', "{$this->path->base()}/cache/sessions/"];
    }
}