<?php
/* --------------------------------------------------------------
   GXModuleJsonRegistry.php 2021-05-14
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
use Gambio\Core\GXModules\Model\ValueObjects\GXModuleJson;
use IteratorAggregate;
use Traversable;

/**
 * Class GXModuleJsonRegistry
 *
 * @package Gambio\Core\GXModules\Model\Collections
 */
class GXModuleJsonRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var GXModuleJson[]
     */
    private $components;
    
    
    /**
     * GXModuleJsonRegistry constructor.
     *
     * @param GXModuleJson ...$components
     */
    private function __construct(GXModuleJson ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param GXModuleJson ...$components
     *
     * @return GXModuleJsonRegistry
     */
    public static function create(GXModuleJson ...$components): GXModuleJsonRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return GXModuleJson[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|GXModuleJson[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}