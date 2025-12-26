<?php
/*--------------------------------------------------------------
   CustomerGroup.php 2022-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Model\ValueObjects;

/**
 * Class CustomerGroup
 *
 * @package Gambio\Admin\Modules\Customer\Model\ValueObjects
 */
class CustomerGroup
{
    private int $id;
    
    
    /**
     * @param int $id
     */
    private function __construct(int $id)
    {
        $this->id = $id;
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