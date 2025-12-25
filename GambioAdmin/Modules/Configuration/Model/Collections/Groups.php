<?php
/* --------------------------------------------------------------
   Groups.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Configuration\Model\Entities\Group;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Class Groups
 *
 * @package Gambio\Admin\Modules\Configuration\Model\Collections
 */
class Groups implements JsonSerializable, IteratorAggregate
{
    /**
     * @var Group[]
     */
    private $groups;
    
    
    /**
     * Groups constructor.
     *
     * @param Group[] $groups
     */
    private function __construct(Group ...$groups)
    {
        $this->groups = $groups;
    }
    
    
    /**
     * @param Group ...$groups
     *
     * @return Groups
     */
    public static function create(Group ...$groups): Groups
    {
        return new self(...$groups);
    }
    
    
    /**
     * @return string[]
     */
    public function tags(): array
    {
        $tagIds = array_map(static function (Group $group): array {
            return $group->tags();
        },
            $this->groups);
        
        return array_unique(array_merge([], ...$tagIds));
    }
    
    
    /**
     * @return Group[]
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->groups;
    }
    
    
    /**
     * @return Traversable|Group[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->groups);
    }
}