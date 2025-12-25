<?php
/* --------------------------------------------------------------
   AdminIds.php 2021-05-14
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
use Gambio\Admin\Modules\Admin\Model\ValueObjects\AdminId;
use IteratorAggregate;
use Traversable;

/**
 * Class AdminIds
 *
 * @package Gambio\Admin\Modules\Admin\Model\Collections
 */
class AdminIds implements IteratorAggregate
{
    /**
     * @var AdminId[]
     */
    private $ids;
    
    
    /**
     * AdminIds constructor.
     *
     * @param AdminId ...$ids
     */
    private function __construct(AdminId ...$ids)
    {
        $this->ids = $ids;
    }
    
    
    /**
     * @param AdminId ...$ids
     *
     * @return AdminIds
     */
    public static function create(AdminId ...$ids): AdminIds
    {
        return new self(...$ids);
    }
    
    
    /**
     * @return Traversable|AdminId[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->ids);
    }
}