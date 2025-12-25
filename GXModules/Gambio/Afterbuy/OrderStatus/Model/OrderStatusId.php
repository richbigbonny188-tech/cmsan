<?php
/* --------------------------------------------------------------
   OrderStatusId.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderStatus\Model;

/**
 * Class OrderStatusId
 *
 * @package GXModules\Gambio\Afterbuy\OrderStatus\Model
 */
class OrderStatusId
{
    /**
     * @var int
     */
    private int $orderStatusId;
    
    
    /**
     * OrderStatusId constructor.
     *
     * @param int $orderStatusId
     */
    public function __construct(int $orderStatusId)
    {
        $this->orderStatusId = $orderStatusId;
    }
    
    
    /**
     * @return int
     */
    public function orderStatusId(): int
    {
        return $this->orderStatusId;
    }
    
    
    /**
     * @return string
     */
    public function asString(): string
    {
        return (string)$this->orderStatusId;
    }
}