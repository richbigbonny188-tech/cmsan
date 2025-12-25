<?php
/*--------------------------------------------------------------------
 AdditionalOptionIds.php 2023-06-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\ValueObjects\AdditionalOptionId;

/**
 * Class AdditionalOptionIds
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections
 */
class AdditionalOptionIds implements IteratorAggregate
{
    /**
     * AdditionalOptionIds constructor.
     *
     * @param AdditionalOptionId[] $ids
     */
    private function __construct(private array $ids){}


    /**
     * @param AdditionalOptionId ...$ids
     *
     * @return AdditionalOptionIds
     */
    public static function create(AdditionalOptionId ...$ids): AdditionalOptionIds
    {
        return new static($ids);
    }


    /**
     * @return Traversable|AdditionalOptionId[]
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
        return array_map(fn(AdditionalOptionId $id): int => $id->value(), $this->ids);
    }
}