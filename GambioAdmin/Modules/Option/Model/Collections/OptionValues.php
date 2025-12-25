<?php
/* --------------------------------------------------------------
   OptionValues.php 2020-04-07
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
use Gambio\Admin\Modules\Option\Model\Entities\OptionValue;
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionValueId;
use IteratorAggregate;
use Traversable;

/**
 * Class OptionValues
 *
 * @package Gambio\Admin\Modules\Option\Model\Collections
 * @codeCoverageIgnore
 */
class OptionValues implements IteratorAggregate
{
    /**
     * @var OptionValue[]
     */
    private $optionValues;
    
    
    /**
     * OptionValues constructor.
     *
     * @param OptionValue[] $optionValues
     */
    private function __construct(array $optionValues)
    {
        $this->optionValues = [];
        foreach ($optionValues as $optionValue) {
            $this->optionValues[$optionValue->id()] = $optionValue;
        }
    }
    
    
    /**
     * @param OptionValue ...$optionValues
     *
     * @return OptionValues
     */
    public static function create(OptionValue ...$optionValues): OptionValues
    {
        return new self($optionValues);
    }
    
    
    /**
     * @param OptionValueId $optionValueId
     *
     * @return OptionValue|null
     */
    public function getById(OptionValueId $optionValueId): ?OptionValue
    {
        return $this->optionValues[$optionValueId->value()] ?? null;
    }
    
    
    /**
     * @param OptionValue ...$optionValues
     *
     * @return OptionValues
     */
    public function with(OptionValue ...$optionValues): OptionValues
    {
        $items = $this->optionValues;
        foreach ($optionValues as $optionValue) {
            $items[$optionValue->id()] = $optionValue;
        }
        
        return new self($items);
    }
    
    
    /**
     * @param OptionValueId ...$optionValueIds
     *
     * @return OptionValues
     */
    public function without(OptionValueId ...$optionValueIds): OptionValues
    {
        $items = $this->optionValues;
        foreach ($optionValueIds as $optionValueId) {
            unset($items[$optionValueId->value()]);
        }
        
        return new self($items);
    }
    
    
    /**
     * @return OptionValue[]
     */
    public function asArray(): array
    {
        return $this->optionValues;
    }
    
    
    /**
     * @return Traversable|OptionValue[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->optionValues);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (OptionValue $optionValue): array {
            return $optionValue->toArray();
        },
            array_values($this->optionValues));
    }
}