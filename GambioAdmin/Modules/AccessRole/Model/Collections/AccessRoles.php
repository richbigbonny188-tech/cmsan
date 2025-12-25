<?php
/* --------------------------------------------------------------
   AccessRoles.php 2021-05-14
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
use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use IteratorAggregate;
use Traversable;

/**
 * Class AccessRoles
 *
 * @package Gambio\Admin\Modules\AccessRole\Model\Collections
 */
class AccessRoles implements IteratorAggregate
{
    /**
     * @var AccessRole[]
     */
    private $roles;
    
    
    /**
     * AccessRoles constructor.
     *
     * @param AccessRole ...$roles
     */
    private function __construct(AccessRole ...$roles)
    {
        $this->roles = $roles;
    }
    
    
    /**
     * @param AccessRole ...$roles
     *
     * @return AccessRoles
     */
    public static function create(AccessRole ...$roles): AccessRoles
    {
        return new self(...$roles);
    }
    
    
    /**
     * @return Traversable|AccessRole[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->roles);
    }
}