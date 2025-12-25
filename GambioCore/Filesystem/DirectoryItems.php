<?php
/* --------------------------------------------------------------
   DirectoryItems.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Filesystem;

use ArrayIterator;
use Gambio\Core\Filesystem\Interfaces\DirectoryItem;

/**
 * Class DirectoryItems
 *
 * @package Gambio\Core\Filesystem
 * @codeCoverageIgnore
 */
class DirectoryItems implements Interfaces\DirectoryItems
{
    /**
     * @var DirectoryItem[]
     */
    private $items;
    
    
    /**
     * Files constructor.
     *
     * @param DirectoryItem ...$items
     */
    private function __construct(DirectoryItem ...$items)
    {
        $this->items = $items;
    }
    
    
    /**
     * @param DirectoryItem ...$items
     *
     * @return DirectoryItems
     */
    public static function create(DirectoryItem ...$items): DirectoryItems
    {
        return new self(...$items);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }
    
    
    /**
     * @inheritDoc
     */
    public function items(): array
    {
        return $this->items;
    }
}