<?php
/* --------------------------------------------------------------
   AfterbuyOrderShippingInfoMapper.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

use GXModules\Gambio\Afterbuy\OrderExport\Model\Request\ShippingInfo;

/**
 * Class AfterbuyOrderShippingInfoMapper
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderShippingInfoMapper
{
    private const FIELD_ORDER_SHIPPING_METHOD = 'shipping_method';
    private const FIELD_ORDER_SHIPPING_CLASS  = 'shipping_class';
    
    /**
     * @var AfterbuyOrderShippingReader
     */
    private AfterbuyOrderShippingReader $reader;
    
    
    /**
     * AfterbuyOrderShippingInfoMapper constructor.
     *
     * @param AfterbuyOrderShippingReader $reader
     */
    public function __construct(AfterbuyOrderShippingReader $reader)
    {
        $this->reader = $reader;
    }
    
    
    /**
     * @param array $data
     * @param float $shippingTaxRate
     *
     * @return ShippingInfo
     */
    public function map(array $data, float $shippingTaxRate = 0): ShippingInfo
    {
        $shippingMethod = $this->getShippingMethod($data);
        $shippingMethod = preg_replace('/ \(.*?\).*/', '', $shippingMethod);
        $shippingGroup  = 'standard';
        $shippingCost   = $this->reader->getShippingCost($data);
        $shippingCost   += ($shippingTaxRate / 100) * $shippingCost;
        $deliveryDate   = $this->reader->getDeliveryDate($data);
        
        return new ShippingInfo($shippingMethod,
                                $shippingGroup,
                                $deliveryDate,
                                $shippingCost,
                                $sendShippingMail = null,
                                $shippingReturnMethod = null,
                                $ebayShippingCost = null);
    }
    
    
    /**
     * Returns the shipping method from the given data.
     *
     * @param array $data
     *
     * @return string
     */
    private function getShippingMethod(array $data): string
    {
        $shippingMethod = $data[self::FIELD_ORDER_SHIPPING_METHOD] ?? '';
        if (empty($shippingMethod)) {
            $shippingClass = $data[self::FIELD_ORDER_SHIPPING_CLASS] ?? '';
            if (empty($shippingClass)) {
                return '';
            }
            
            return $this->reader->getShippingMethodFromClass($shippingClass);
        }
        
        return $shippingMethod;
    }
}