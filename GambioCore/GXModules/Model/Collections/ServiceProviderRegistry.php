<?php
/* --------------------------------------------------------------
   ServiceProviderRegistry.php 2021-05-14
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
use Gambio\Core\GXModules\Model\ValueObjects\ServiceProvider;
use IteratorAggregate;
use Traversable;

/**
 * Class ServiceProviderRegistry
 *
 * @package Gambio\Core\GXModules\Model
 */
class ServiceProviderRegistry implements ComponentsRegistry, IteratorAggregate
{
    /**
     * @var ServiceProvider[]
     */
    private $components;
    
    
    /**
     * ServiceProviderRegistry constructor.
     *
     * @param ServiceProvider ...$components
     */
    private function __construct(ServiceProvider ...$components)
    {
        $this->components = $components;
    }
    
    
    /**
     * @param ServiceProvider ...$components
     *
     * @return ServiceProviderRegistry
     */
    public static function create(ServiceProvider ...$components): ServiceProviderRegistry
    {
        return new self(...$components);
    }
    
    
    /**
     * @return ServiceProvider[]
     */
    public function components(): array
    {
        return $this->components;
    }
    
    
    /**
     * @return Traversable|ServiceProvider[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->components);
    }
}