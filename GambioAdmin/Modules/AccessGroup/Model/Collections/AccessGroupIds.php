<?php
/* --------------------------------------------------------------
   GroupIds.php 2021-05-14
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
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupId;
use IteratorAggregate;
use Traversable;

/**
 * Class AccessGroupIds
 *
 * @package Gambio\Admin\Modules\AccessGroup\Model\Collections
 */
class AccessGroupIds implements IteratorAggregate
{
    /**
     * @var AccessGroupId[]
     */
    private $ids;
    
    
    /**
     * AccessGroupIds constructor.
     *
     * @param AccessGroupId ...$ids
     */
    private function __construct(AccessGroupId ...$ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param AccessGroupId ...$ids
     *
     * @return AccessGroupIds
     */
    public static function create(AccessGroupId ...$ids): AccessGroupIds
    {
        return new self(...$ids);
    }
    
    
    /**
     * @return Traversable|AccessGroupId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
}