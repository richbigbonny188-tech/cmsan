<?php
/* --------------------------------------------------------------
   AccessRoleIds.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use IteratorAggregate;
use Traversable;

/**
 * Class AccessRoleIds
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Collections
 */
class AccessRoleIds implements IteratorAggregate
{
    /**
     * @var AccessRoleId[]
     */
    private $ids;
    
    
    /**
     * AccessRoleIds constructor.
     *
     * @param AccessRoleId ...$ids
     */
    private function __construct(AccessRoleId ...$ids)
    {
        $this->ids = [];
        foreach ($ids as $id) {
            $this->ids[$id->value()] = $id;
        }
    }
    
    
    /**
     * @param AccessRoleId ...$ids
     *
     * @return AccessRoleIds
     */
    public static function create(AccessRoleId ...$ids): AccessRoleIds
    {
        return new self(...$ids);
    }
    
    
    /**
     * @return Traversable|AccessRoleId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
}