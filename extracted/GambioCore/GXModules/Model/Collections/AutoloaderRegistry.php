<?php
/* --------------------------------------------------------------
   AutoloaderRegistry.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\Model\Collections;

use ArrayIterator;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\Autoloader;
use IteratorAggregate;
use Traversable;

/**
 * Class AutoloaderRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class AutoloaderRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var Autoloader[]
     */
    private $components;
    
    
    /**
     * AutoloaderRegistry constructor.
     *
     * @param Autoloader ...$components
     */
    private function __construct(Autoloader ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param Autoloader ...$components
     *
     * @return AutoloaderRegistry
     */
    public static function create(Autoloader ...$components): AutoloaderRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return Autoloader[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|Autoloader[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}