<?php
/* --------------------------------------------------------------
   GX4ModuleRegistry.php 2021-05-14
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
use Gambio\Core\GXModules\Model\ValueObjects\GX4Module;
use IteratorAggregate;
use Traversable;

/**
 * Class GX4ModuleRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class GX4ModuleRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var GX4Module[]
     */
    private $components;
    
    
    /**
     * GX4ModuleRegistry constructor.
     *
     * @param GX4Module ...$components
     */
    private function __construct(GX4Module ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param GX4Module ...$components
     *
     * @return GX4ModuleRegistry
     */
    public static function create(GX4Module ...$components): GX4ModuleRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return GX4Module[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|GX4Module[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}