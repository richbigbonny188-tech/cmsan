<?php
/* --------------------------------------------------------------
   Groups.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessGroup\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\AccessGroup\Model\AccessGroup;
use IteratorAggregate;
use Traversable;

/**
 * Class AccessGroups
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Collections
 */
class AccessGroups implements IteratorAggregate
{
    /**
     * @var AccessGroup[]
     */
    private $groups;
    
    
    /**
     * AccessGroups constructor.
     *
     * @param AccessGroup ...$groups
     */
    private function __construct(AccessGroup ...$groups)
    {
        $this->groups = $groups;
    }
    
    
    /**
     * @param AccessGroup[] $groups
     *
     * @return AccessGroups
     */
    public static function create(AccessGroup ...$groups): AccessGroups
    {
        return new self(...$groups);
    }
    
    
    /**
     * @return Traversable|AccessGroup[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->groups);
    }
}