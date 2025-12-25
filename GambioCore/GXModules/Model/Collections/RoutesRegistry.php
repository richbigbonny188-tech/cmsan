<?php
/* --------------------------------------------------------------
   RoutesRegistry.php 2021-05-14
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
use Gambio\Core\GXModules\Model\ValueObjects\Routes;
use IteratorAggregate;
use Traversable;

/**
 * Class RoutesRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class RoutesRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var Routes[]
     */
    private $components;
    
    
    /**
     * RoutesRegistry constructor.
     *
     * @param Routes ...$components
     */
    private function __construct(Routes ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param Routes ...$components
     *
     * @return RoutesRegistry
     */
    public static function create(Routes ...$components): RoutesRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return Routes[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|Routes[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}