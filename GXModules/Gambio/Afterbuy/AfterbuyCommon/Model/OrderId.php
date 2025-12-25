<?php
/* --------------------------------------------------------------
   OrderId.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\AfterbuyCommon\Model;

/**
 * Class OrderId
 *
 * This order id represents the order id of the gambio shop system.
 *
 * @package GXModules\Gambio\Afterbuy\AfterbuyCommon\Model
 */
class OrderId
{
    /**
     * @var int
     */
    private int $orderId;
    
    
    /**
     * OrderId constructor.
     *
     * @param int $orderId
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }
    
    
    /**
     * @return int
     */
    public function orderId(): int
    {
        return $this->orderId;
    }
    
    
    /**
     * @return string
     */
    public function asString(): string
    {
        return (string)$this->orderId;
    }
}