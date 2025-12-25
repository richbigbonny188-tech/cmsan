<?php
/* --------------------------------------------------------------
   OptionIds.php 2021-03-31
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
use Gambio\Admin\Modules\Option\Model\ValueObjects\OptionId;
use IteratorAggregate;
use Traversable;

/**
 * Class OptionIds
 *
 * @package Gambio\Admin\Modules\Option\Model\Collections
 * @codeCoverageIgnore
 */
class OptionIds implements IteratorAggregate
{
    /**
     * @var OptionId[]
     */
    private $optionIds;
    
    
    /**
     * OptionIds constructor.
     *
     * @param OptionId[] $optionIds
     */
    private function __construct(array $optionIds)
    {
        $this->optionIds = $optionIds;
    }
    
    
    /**
     * @param OptionId ...$optionIds
     *
     * @return OptionIds
     */
    public static function create(OptionId ...$optionIds): OptionIds
    {
        return new self($optionIds);
    }
    
    
    /**
     * @return Traversable|OptionId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->optionIds);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (OptionId $optionId): int {
            return $optionId->value();
        },
            $this->optionIds);
    }
}