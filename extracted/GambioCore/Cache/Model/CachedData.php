<?php
/* --------------------------------------------------------------
   CachedData.php 2023-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Cache\Model;

use Gambio\Core\Cache\Services\Exceptions\InvalidCacheKeyException;
use function json_decode;
use function serialize;
use function unserialize;

/**
 * Class CachedData
 *
 * @package Gambio\Core\Cache\Model
 */
class CachedData
{
    /**
     * @var string
     */
    private $key;
    
    /**
     * @var mixed
     */
    private $cachedValue;
    
    /**
     * @var int|null
     */
    private $expirationTimestamp;
    
    
    /**
     * CachedData constructor.
     *
     * @param string   $key
     * @param mixed    $cachedValue
     * @param int|null $expirationTimestamp
     */
    private function __construct(string $key, $cachedValue, ?int $expirationTimestamp)
    {
        $this->key                 = $key;
        $this->cachedValue         = $cachedValue;
        $this->expirationTimestamp = $expirationTimestamp;
    }
    
    
    /**
     * @param string   $key
     * @param          $cachedValue
     * @param int|null $expirationTimestamp
     *
     * @return CachedData
     *
     * @throws InvalidCacheKeyException
     */
    public static function create(string $key, $cachedValue, ?int $expirationTimestamp = null): CachedData
    {
        if (strlen($key) > 64 || preg_match('/^[A-Za-z0-9_\.]+$/', $key) !== 1) {
            throw InvalidCacheKeyException::forString($key);
        }
        
        return new self($key, $cachedValue, $expirationTimestamp);
    }
    
    
    /**
     * @param string $json
     * @param array  $allowedClasses
     *
     * @return CachedData
     *
     * @throws InvalidCacheKeyException If key in provided JSON string is invalid.
     */
    public static function createFromJson(string $json, array $allowedClasses = []): CachedData
    {
        $data = json_decode($json, true);
        
        return self::create($data['key'],
                            unserialize($data['cachedValue'], ['allowed_classes' => $allowedClasses]),
                            $data['expirationTimestamp']);
    }
    
    
    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }
    
    
    /**
     * @return mixed
     */
    public function cachedValue()
    {
        return $this->cachedValue;
    }
    
    
    /**
     * @return int|null
     */
    public function expirationTimestamp(): ?int
    {
        return $this->expirationTimestamp;
    }
    
    
    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expirationTimestamp !== null && time() > $this->expirationTimestamp;
    }
    
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        $jsonData = [
            'key'                 => $this->key,
            'cachedValue'         => serialize($this->cachedValue),
            'expirationTimestamp' => $this->expirationTimestamp,
        ];
        
        return json_encode($jsonData);
    }
}