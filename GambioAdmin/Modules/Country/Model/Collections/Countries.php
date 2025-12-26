<?php
/*--------------------------------------------------------------
   Countries.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Country\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Country\Model\Country;
use IteratorAggregate;
use Traversable;

/**
 * Class Countries
 *
 * @package Gambio\Admin\Modules\Country\Model\Collections
 */
class Countries implements IteratorAggregate
{
    /**
     * @var Country[]
     */
    private array $countries;
    
    
    /**
     * Countries constructor.
     *
     * @param Country[] $countries
     */
    private function __construct(array $countries)
    {
        $this->countries = $countries;
    }
    
    
    /**
     * @param Country ...$countries
     *
     * @return Countries
     */
    public static function create(Country ...$countries): Countries
    {
        return new self($countries);
    }
    
    
    /**
     * @return Traversable|Country[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->countries);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(Country $country): array => $country->toArray(), $this->countries);
    }
}