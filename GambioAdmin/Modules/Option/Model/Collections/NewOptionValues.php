<?php
/* --------------------------------------------------------------
   NewOptionValues.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Option\Model\ValueObjects\NewOptionValue;
use IteratorAggregate;
use Traversable;

/**
 * Class NewOptionValues
 *
 * @package Gambio\Admin\Modules\Option\Model\Collections
 * @codeCoverageIgnore
 */
class NewOptionValues implements IteratorAggregate
{
    /**
     * @var NewOptionValue[]
     */
    private $newOptionValues;
    
    
    /**
     * NewOptionValues constructor.
     *
     * @param NewOptionValue[] $newOptionValues
     */
    private function __construct(array $newOptionValues)
    {
        $this->newOptionValues = $newOptionValues;
    }
    
    
    /**
     * @param NewOptionValue ...$newOptionValues
     *
     * @return NewOptionValues
     */
    public static function create(NewOptionValue ...$newOptionValues): NewOptionValues
    {
        return new self($newOptionValues);
    }
    
    
    /**
     * @param NewOptionValue ...$newOptionValues
     *
     * @return NewOptionValues
     */
    public function with(NewOptionValue ...$newOptionValues): NewOptionValues
    {
        return new self(array_merge($this->newOptionValues, $newOptionValues));
    }
    
    
    /**
     * @return Traversable|NewOptionValue[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->newOptionValues);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (NewOptionValue $newValue): array {
            return $newValue->toArray();
        },
            $this->newOptionValues);
    }
}