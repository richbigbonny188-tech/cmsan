<?php
/*--------------------------------------------------------------------
 AdditionalOptionIds.php 2023-06-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\ValueObjects\AdditionalOptionId;
use IteratorAggregate;
use Traversable;

/**
 * Class AdditionalOptionIds
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections
 */
class AdditionalOptionIds implements IteratorAggregate
{
    
    /**
     * AdditionalOptionIds constructor.
     *
     * @param AdditionalOptionId[] $ids
     */
    private function __construct(private array $ids) { }
    
    
    /**
     * @param AdditionalOptionId ...$ids
     *
     * @return AdditionalOptionIds
     */
    public static function create(AdditionalOptionId ...$ids): AdditionalOptionIds
    {
        return new self($ids);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $callback = static fn(AdditionalOptionId $id): int => $id->value();
        
        return array_map($callback, $this->ids);
    }
    
    
    /**
     * @return Traversable|AdditionalOptionId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
}