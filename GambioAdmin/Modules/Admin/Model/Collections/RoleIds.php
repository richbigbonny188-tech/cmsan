<?php
/* --------------------------------------------------------------
   RoleIds.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Admin\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Admin\Model\ValueObjects\RoleId;
use IteratorAggregate;
use Traversable;

/**
 * Class RoleIds
 *
 * @package Gambio\Admin\Modules\Admin\Model\Collections
 */
class RoleIds implements IteratorAggregate
{
    /**
     * @var RoleId[]
     */
    private $ids;
    
    
    /**
     * RoleIds constructor.
     *
     * @param RoleId ...$ids
     */
    private function __construct(RoleId ...$ids)
    {
        $this->ids = [];
        foreach ($ids as $id) {
            $this->ids[$id->value()] = $id;
        }
    }
    
    
    /**
     * @param RoleId ...$ids
     *
     * @return RoleIds
     */
    public static function create(RoleId ...$ids): RoleIds
    {
        return new self(...$ids);
    }
    
    
    /**
     * @return Traversable|RoleId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
    
    
    /**
     * @param RoleId $roleId
     *
     * @return RoleIds
     */
    public function withRoleId(RoleId $roleId): RoleIds
    {
        $ids                   = $this->ids;
        $ids[$roleId->value()] = $roleId;
        
        return new self(...$ids);
    }
    
    
    /**
     * @param RoleId $roleId
     *
     * @return RoleIds
     */
    public function withoutRoleId(RoleId $roleId): RoleIds
    {
        $ids = $this->ids;
        unset($ids[$roleId->value()]);
        
        return new self(...$ids);
    }
}