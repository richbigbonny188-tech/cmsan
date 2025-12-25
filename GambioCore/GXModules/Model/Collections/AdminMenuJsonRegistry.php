<?php
/* --------------------------------------------------------------
   AdminMenuJsonRegistry.php 2021-05-14
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
use Gambio\Core\GXModules\Model\ValueObjects\AdminMenuJson;
use IteratorAggregate;
use Traversable;

/**
 * Class AdminMenuJsonRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class AdminMenuJsonRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var AdminMenuJson[]
     */
    private $components;
    
    
    /**
     * AdminMenuJsonRegistry constructor.
     *
     * @param AdminMenuJson ...$components
     */
    private function __construct(AdminMenuJson ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param AdminMenuJson ...$components
     *
     * @return AdminMenuJsonRegistry
     */
    public static function create(AdminMenuJson ...$components): AdminMenuJsonRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return AdminMenuJson[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|AdminMenuJson[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}