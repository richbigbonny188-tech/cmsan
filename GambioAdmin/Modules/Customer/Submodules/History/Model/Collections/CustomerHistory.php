<?php
/*--------------------------------------------------------------
   CustomerHistory.php 2022-07-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\History\Model\CustomerHistoryEntry;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerHistory
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\History\Model\Collections
 */
class CustomerHistory implements IteratorAggregate
{
    /** @var CustomerHistoryEntry[] */
    private array $entries;
    
    
    /**
     * @param CustomerHistoryEntry[] $entries
     */
    private function __construct(array $entries)
    {
        $this->entries = $this->sortAscending(...$entries);
    }
    
    
    /**
     * @param CustomerHistoryEntry ...$entries
     *
     * @return CustomerHistory
     */
    public static function create(CustomerHistoryEntry ...$entries): CustomerHistory
    {
        return new self($entries);
    }
    
    
    /**
     * @return Traversable|CustomerHistoryEntry[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->entries);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return array_map(fn(CustomerHistoryEntry $entry): array => $entry->toArray($datetimeFormat), $this->entries);
    }
    
    
    /**
     * @param CustomerHistory $history
     *
     * @return CustomerHistory
     */
    public function merge(CustomerHistory $history): CustomerHistory
    {
        $entries = $this->entries;
    
        foreach ($history as $entry) {
            
            $entries[] = $entry;
        }
        
        return new self($entries);
    }
    
    /**
     * @param CustomerHistoryEntry ...$entries
     *
     * @return array
     */
    private function sortAscending(CustomerHistoryEntry ...$entries): array
    {
        $format = 'YmdHis';
        $fn     = static function (CustomerHistoryEntry $a, CustomerHistoryEntry $b) use ($format): int {
            return (int)$b->date($format) <=> (int)$a->date($format);
        };
    
        usort($entries, $fn);
    
        return $entries;
    }
}