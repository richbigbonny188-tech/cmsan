<?php
/* --------------------------------------------------------------
   CustomerGroup.php 2023-11-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Newsletter\Model\ValueObjects;

/**
 * Class CustomerGroup
 *
 * @package Gambio\Admin\Modules\Newsletter\Model\ValueObjects
 */
class CustomerGroup
{
    /**
     * @param int $id
     */
    private function __construct(private int $id)
    {
    }
    
    
    /**
     * @param int $id
     *
     * @return CustomerGroup
     */
    public static function create(int $id): CustomerGroup
    {
        return new static($id);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}