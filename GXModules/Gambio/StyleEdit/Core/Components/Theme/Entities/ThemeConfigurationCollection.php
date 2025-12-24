<?php
/* --------------------------------------------------------------
   ThemeConfigurationCollection.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components\Theme\Entities;

use ArrayIterator;
use Countable;
use Gambio\StyleEdit\Core\Components\Theme\Exceptions\ThemeIdNotSuppliedException;
use Gambio\StyleEdit\Core\TranslatedException;
use IteratorAggregate;

/**
 * Class ThemeConfigurationCollection
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class ThemeConfigurationCollection implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    protected $elements = [];
    
    
    /**
     * @param ThemeConfiguration $themeConfiguration
     */
    public function add(ThemeConfiguration $themeConfiguration)
    {
        $this->elements[$themeConfiguration->id()] = $themeConfiguration;
    }
    
    
    /**
     * @param string $id
     *
     * @return ThemeConfiguration
     * @throws TranslatedException | \Exception
     */
    public function get($id)
    {
        if (!isset($this->elements[$id])) {
            throw new ThemeIdNotSuppliedException([$id]);
        }
        
        return $this->elements[$id];
    }
    
    
    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasKey(string $id)
    {
        return array_key_exists($id, $this->elements);
    }
    
    
    /**
     * @param ThemeConfiguration $themeConfiguration
     */
    public function remove(ThemeConfiguration $themeConfiguration)
    {
        if (array_key_exists($themeConfiguration->id(), $this->elements)) {
            unset($this->elements[$themeConfiguration->id()]);
        }
    }
    
    
    /**
     * @return array
     */
    public function elements()
    {
        return $this->elements;
    }
    
    
    /**
     * Get collection item count.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }
    
    
    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->elements);
    }
    
    
}