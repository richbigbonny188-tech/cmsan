<?php
/* --------------------------------------------------------------
   AfterbuyOrderIdMapping.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\ShopApi\Model;

/**
 * Class AfterbuyOrderIdMapping
 *
 * @package GXModules\Gambio\Afterbuy\ShopApi\Model
 */
class AfterbuyOrderIdMapping
{
    /**
     * @var int
     */
    private int $orderId;
    
    
    /**
     * @var int
     */
    private int $afterbuyOrderId;
    
    
    /**
     * @var bool|null
     */
    private ?bool $transmitted;
    
    
    /**
     * AfterbuyOrderIdMapping constructor.
     *
     * @param int       $orderId
     * @param int       $afterbuyOrderId
     * @param bool|null $transmitted
     */
    public function __construct(int $orderId, int $afterbuyOrderId, bool $transmitted = null)
    {
        $this->orderId         = $orderId;
        $this->afterbuyOrderId = $afterbuyOrderId;
        $this->transmitted     = $transmitted;
    }
    
    
    /**
     * @return int
     */
    public function orderId(): int
    {
        return $this->orderId;
    }
    
    
    /**
     * @return int
     */
    public function afterbuyOrderId(): int
    {
        return $this->afterbuyOrderId;
    }
    
    
    /**
     * @return bool|null
     */
    public function transmitted(): ?bool
    {
        return $this->transmitted;
    }
}