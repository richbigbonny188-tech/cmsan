<?php
/* --------------------------------------------------------------
 TokenService.php 2021-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Token;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\SecurityToken;
use Webmozart\Assert\Assert;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function md5;
use function serialize;
use function time;
use function unserialize;

/**
 * Class TokenService
 * @package Gambio\Admin\Application\Token
 */
class TokenService
{
    /**
     * @var SecurityToken
     */
    private $securityToken;
    
    /**
     * @var string
     */
    private $cacheDir;
    
    
    /**
     * CacheTokenRepository constructor.
     *
     * @param SecurityToken $securityToken
     * @param Path          $path
     */
    public function __construct(SecurityToken $securityToken, Path $path)
    {
        $cacheDir = "{$path->base()}/cache";
        Assert::directory($cacheDir);
        
        $this->securityToken = $securityToken;
        $this->cacheDir      = $cacheDir;
    }
    
    
    /**
     * Returns the cache token.
     * If the cache token not exists, a new one will be created an returned.
     *
     * @return string
     */
    public function getCacheToken(): string
    {
        $cacheFile = $this->cacheFile();
        if (file_exists($cacheFile)) {
            $data = file_get_contents($cacheFile);
            
            return unserialize($data, ['allowed_classes' => false]);
        }
        
        $token = md5((string)time());
        file_put_contents($cacheFile, serialize($token));
        
        return $token;
    }
    
    
    /**
     * Cache file path.
     *
     * @return string
     */
    private function cacheFile(): string
    {
        $filePrefix = 'persistent_data_cache-';
        
        return "{$this->cacheDir}/cache_token-{$filePrefix}{$this->securityToken->value()}.pdc";
    }
}