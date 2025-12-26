<?php
/* --------------------------------------------------------------
   AfterbuyOrderTrackingCode.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderTracking\Model;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;

/**
 * Class AfterbuyOrderTrackingCode
 *
 * @package GXModules\Gambio\Afterbuy\OrderTracking\Model
 */
class AfterbuyOrderTrackingCode
{
    /**
     * @var OrderId
     */
    private OrderId $orderId;
    
    
    /**
     * @var string
     */
    private string $trackingCode;
    
    
    /**
     * @var string
     */
    private string $shippingMethod;
    
    
    /**
     * @var string
     */
    private string $trackingLink;
    
    
    /**
     * AfterbuyOrderTrackingCode constructor.
     *
     * @param OrderId $orderId
     * @param string  $trackingCode
     * @param string  $shippingMethod
     * @param string  $trackingLink
     */
    public function __construct(
        OrderId $orderId,
        string  $trackingCode,
        string  $shippingMethod,
        string  $trackingLink = ''
    ) {
        $this->orderId        = $orderId;
        $this->trackingCode   = $trackingCode;
        $this->shippingMethod = $shippingMethod;
        $this->trackingLink   = $trackingLink;
    }
    
    
    /**
     * @return int
     */
    public function orderId(): int
    {
        return $this->orderId->orderId();
    }
    
    
    /**
     * @return string
     */
    public function trackingCode(): string
    {
        return $this->trackingCode;
    }
    
    
    /**
     * @return string
     */
    public function shippingMethod(): string
    {
        return $this->shippingMethod;
    }
    
    
    /**
     * @return string
     */
    public function trackingLink(): string
    {
        return $this->trackingLink;
    }
    
    
}