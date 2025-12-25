<?php
/*--------------------------------------------------------------
   Zones.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Country\Model\Entities\Zone;
use IteratorAggregate;
use Traversable;

/**
 * Class Zones
 *
 * @package Gambio\Admin\Modules\Country\Model\Collections
 */
class Zones implements IteratorAggregate
{
    /**
     * @var Zone[]
     */
    private array $zones;
    
    
    /**
     * Zones constructor.
     *
     * @param Zone[] $zones
     */
    private function __construct(array $zones)
    {
        $this->zones = $zones;
    }
    
    
    /**
     * @param Zone ...$zones
     *
     * @return Zones
     */
    public static function create(Zone ...$zones): Zones
    {
        return new self($zones);
    }
    
    
    /**
     * @return Traversable|Zone[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->zones);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(Zone $zone): array => $zone->toArray(), $this->zones);
    }
}