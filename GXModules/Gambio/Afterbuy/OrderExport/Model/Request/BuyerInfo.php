<?php
/* --------------------------------------------------------------
   BuyerInfo.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model\Request;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\XmlSerializable;

/**
 * Class BuyerInfo
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model\Request
 */
class BuyerInfo implements XmlSerializable
{
    /**
     * @var ShippingAddress
     */
    private ShippingAddress $shippingAddress;
    
    
    /**
     * @param ShippingAddress $shippingAddress
     */
    public function __construct(ShippingAddress $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toXmlString(): string
    {
        $shippingAddress = $this->shippingAddress->toXmlString();
        $indent          = $this->indent();
        
        return <<<XML
$indent<BuyerInfo>
$shippingAddress
$indent</BuyerInfo>
XML;
    }
    
    
    /**
     * @inheritDoc
     */
    public function indent(): string
    {
        return str_repeat(' ', 12);
    }
}