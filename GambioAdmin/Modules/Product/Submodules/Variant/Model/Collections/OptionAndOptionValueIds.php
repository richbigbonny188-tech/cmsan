<?php
/*--------------------------------------------------------------
   OptionAndOptionValueIds.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\OptionAndOptionValueId;
use IteratorAggregate;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * Class OptionAndOptionValueIds
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections
 */
class OptionAndOptionValueIds implements IteratorAggregate
{
    /**
     * OptionAndOptionValueIds constructor.
     *
     * @param OptionAndOptionValueId[] $ids
     */
    private function __construct(private array $ids) { }
    
    
    /**
     * @param OptionAndOptionValueId ...$ids
     *
     * @return OptionAndOptionValueIds
     */
    public static function create(OptionAndOptionValueId ...$ids): OptionAndOptionValueIds
    {
        usort($ids,
            static function (OptionAndOptionValueId $a, OptionAndOptionValueId $b) {
                return $a->optionId() - $b->optionId();
            });
        
        $optionIds = array_map(static function (OptionAndOptionValueId $id) {
            return $id->optionId();
        },
            $ids);
        
        Assert::eq($optionIds,
                   array_unique($optionIds),
                   'A product variant can\'t have the multiple option values of the same option. Duplicated: '
                   . implode(', ', array_diff($optionIds, array_unique($optionIds))));
        
        return new self($ids);
    }
    
    
    /**
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return bool
     */
    public function contains(OptionAndOptionValueId $optionAndOptionValueId): bool
    {
        foreach ($this->ids as $id) {
            if ($id->equals($optionAndOptionValueId)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return OptionAndOptionValueIds
     */
    public function without(OptionAndOptionValueId $optionAndOptionValueId): OptionAndOptionValueIds
    {
        $ids = [];
        foreach ($this->ids as $id) {
            if ($id->toString() !== $optionAndOptionValueId->toString()) {
                $ids[] = $id;
            }
        }
        
        return new self($ids);
    }
    
    
    /**
     * @return string
     */
    public function toString(): string
    {
        $strings = array_map(static function (OptionAndOptionValueId $id): string {
            return (string)$id;
        },
            $this->ids);
        
        return implode('|', $strings);
    }
    
    
    /**
     * @param OptionAndOptionValueId $optionAndOptionValueId
     *
     * @return OptionAndOptionValueIds
     */
    public function with(OptionAndOptionValueId $optionAndOptionValueId): OptionAndOptionValueIds
    {
        $ids   = $this->ids;
        $ids[] = $optionAndOptionValueId;
        
        $optionIds = array_map(static function (OptionAndOptionValueId $value): int {
            return $value->optionId();
        }, $ids);
        
        $errormsg = sprintf('Cannot add %s because the option id "%s" is already in this collection (%s)',
                            (string)$optionAndOptionValueId,
                            $optionAndOptionValueId->optionId(),
                            (string)$this);
        Assert::uniqueValues($optionIds, $errormsg);
        
        return new self($ids);
    }
    
    
    /**
     * @return Traversable|OptionAndOptionValueId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (OptionAndOptionValueId $id): array {
            return $id->toArray();
        },
            $this->ids);
    }
    
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}