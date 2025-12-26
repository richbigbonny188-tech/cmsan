<?php
/* --------------------------------------------------------------
 MenuUrl.php 2022-07-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cached;

use function array_intersect;
use function count;
use function parse_str;
use function parse_url;

/**
 * Class MenuUrl
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class MenuUrl
{
    /**
     * @var string
     */
    private $path;
    
    /**
     * @var array
     */
    private $params;
    
    
    /**
     * MenuUrl constructor.
     *
     * @param string      $path
     * @param array       $params
     */
    private function __construct(string $path, array $params)
    {
        $this->path   = $path;
        $this->params = $params;
    }
    
    
    /**
     * Factory method for Url.
     *
     * @param string $requestUri
     *
     * @return static
     */
    public static function fromUri(string $requestUri): self
    {
        $parts     = parse_url($requestUri);
        $getParams = [];
        if (isset($parts['query'])) {
            parse_str($parts['query'], $getParams);
        }
        
        return new static($parts['path'], $getParams);
    }
    
    
    /**
     * Checks if the url information matches.
     *
     * @param MenuUrl $other
     *
     * @return bool
     */
    public function equals(self $other): bool
    {
        $pathMatches  = $this->path === $other->path;
        $intersection = array_map("unserialize", array_intersect($this->serialize_array_values($this->params), $this->serialize_array_values($other->params)));
        
        return $pathMatches && count($this->params) === count($intersection);
    }
    
    
    /**
     * Serializes array values for {@link array_intersect} to not throw a warning in PHP >= 8.0.
     *
     * @param $arr
     *
     * @return mixed
     */
    private function serialize_array_values($arr)
    {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                sort($val);
            }
            $arr[$key] = serialize($val);
        }
        
        return $arr;
    }
    
}