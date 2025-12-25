<?php
/* --------------------------------------------------------------
   ShippingInfo.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Model\Request;

use DateTimeImmutable;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\XmlSerializable;

/**
 * Class ShippingInfo
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model
 */
class ShippingInfo implements XmlSerializable
{
    use RequestPriceUtilityTrait, EscapeXmlTrait;
    
    private const DATE_FORMAT = 'd.m.Y H:i:s';
    
    /**
     * @var string
     */
    private string $shippingMethod;
    
    
    /**
     * @var string
     */
    private string $shippingGroup;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $deliveryDate;
    
    
    /**
     * @var float|null
     */
    private ?float $shippingCost;
    
    
    /**
     * @var bool|null
     */
    private ?bool $sendShippingMail;
    
    
    /**
     * @var string|null
     */
    private ?string $shippingReturnMethod;
    
    
    /**
     * @var float|null
     */
    private ?float $eBayShippingCost;
    
    
    /**
     * @param string                 $shippingMethod
     * @param string                 $shippingGroup
     * @param DateTimeImmutable|null $deliveryDate
     * @param float|null             $shippingCost
     * @param bool|null              $sendShippingMail
     * @param string|null            $shippingReturnMethod
     * @param float|null             $eBayShippingCost
     */
    public function __construct(
        string            $shippingMethod,
        string            $shippingGroup,
        DateTimeImmutable $deliveryDate = null,
        float             $shippingCost = null,
        bool              $sendShippingMail = null,
        string            $shippingReturnMethod = null,
        float             $eBayShippingCost = null
    ) {
        $this->shippingMethod       = $this->escapeForXml($shippingMethod);
        $this->shippingGroup        = $this->escapeForXml($shippingGroup);
        $this->deliveryDate         = $deliveryDate;
        $this->shippingCost         = $shippingCost;
        $this->sendShippingMail     = $sendShippingMail;
        $this->shippingReturnMethod = $this->escapeForXml($shippingReturnMethod);
        $this->eBayShippingCost     = $eBayShippingCost;
    }
    
    
    /**
     * @return string
     */
    public function toXmlString(): string
    {
        $indent    = $this->indent();
        $optionals = $this->optionalsAsXml();
        
        $xml = "{$indent}<ShippingInfo>\n";
        if (!empty($this->shippingMethod)) {
            $xml .= "{$indent}    <ShippingMethod>$this->shippingMethod</ShippingMethod>\n";
        }
        if (!empty($this->shippingGroup)) {
            $xml .= "{$indent}    <ShippingGroup>$this->shippingGroup</ShippingGroup>\n";
        }
        $xml .= $optionals;
        $xml .= "{$indent}</ShippingInfo>\n";
        
        return $xml;
    }
    
    
    /**
     * Creates a xml string of the optional values, if they are available.
     *
     * @return string
     */
    private function optionalsAsXml(): string
    {
        $indent    = str_repeat(' ', strlen($this->indent()) + 4);
        $optionals = '';
        
        if ($this->deliveryDate !== null) {
            $date      = $this->deliveryDate->format(self::DATE_FORMAT);
            $optionals .= "$indent<DeliveryDate>$date</DeliveryDate>\n";
        }
        if ($this->shippingCost !== null) {
            $cost      = $this->convertFloatToString($this->shippingCost);
            $optionals .= "$indent<ShippingCost>$cost</ShippingCost>\n";
        }
        if ($this->eBayShippingCost !== null) {
            $cost      = $this->convertFloatToString($this->eBayShippingCost);
            $optionals .= "$indent<eBayShippingCost>$cost</eBayShippingCost>\n";
        }
        if ($this->shippingReturnMethod !== null) {
            $optionals .= "$indent<ShippingReturnMethod>$this->shippingReturnMethod</ShippingReturnMethod>\n";
        }
        if ($this->sendShippingMail !== null) {
            $value     = $this->sendShippingMail ? 'Yes' : 'No';
            $optionals .= "$indent<SendShippingMail>$value</SendShippingMail>\n";
        }
        
        return rtrim($optionals);
    }
    
    
    /**
     * @inheritDoc
     */
    public function indent(): string
    {
        return str_repeat(' ', 12);
    }
}
