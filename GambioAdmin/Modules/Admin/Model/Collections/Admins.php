<?php
/* --------------------------------------------------------------
   Aminds.php 2021-05-14
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
use Gambio\Admin\Modules\Admin\Model\Admin;
use IteratorAggregate;
use Traversable;

/**
 * Class Admins
 *
 * @package Gambio\Admin\Modules\Admin\Model\Collections
 */
class Admins implements IteratorAggregate
{
    /**
     * @var Admin[]
     */
    private $admins;
    
    
    /**
     * Admins constructor.
     *
     * @param Admin ...$admins
     */
    private function __construct(Admin ...$admins)
    {
        $this->admins = $admins;
    }
    
    
    /**
     * @param Admin ...$admins
     *
     * @return Admins
     */
    public static function create(Admin ...$admins): Admins
    {
        return new self(...$admins);
    }
    
    
    /**
     * @return Traversable|Admin[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->admins);
    }
}