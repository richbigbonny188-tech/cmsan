<?php
/* --------------------------------------------------------------
   PaymentInfo.php 2023-10-18
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
 * Class PaymentInfo
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model
 */
class PaymentInfo implements XmlSerializable
{
    use RequestPriceUtilityTrait, EscapeXmlTrait;
    
    private const DATE_FORMAT = 'd.m.Y H:i:s';
    
    /**
     * @var string|null
     */
    private string $paymentMethod;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $paymentDate;
    
    
    /**
     * @var float|null
     */
    private ?float $alreadyPaid;
    
    
    /**
     * @var float|null
     */
    private ?float $paymentAdditionalCost;
    
    
    /**
     * @var bool|null
     */
    private ?bool $sendPaymentMail;
    
    
    /**
     * @param string                 $paymentMethod
     * @param DateTimeImmutable|null $paymentDate
     * @param float|null             $alreadyPaid
     * @param float|null             $paymentAdditionalCost
     * @param bool|null              $sendPaymentMail
     */
    public function __construct(
        string             $paymentMethod,
        ?DateTimeImmutable $paymentDate = null,
        float              $alreadyPaid = null,
        float              $paymentAdditionalCost = null,
        bool               $sendPaymentMail = null
    ) {
        $this->paymentMethod         = $this->escapeForXml($paymentMethod);
        $this->paymentDate           = $paymentDate;
        $this->alreadyPaid           = $alreadyPaid;
        $this->paymentAdditionalCost = $paymentAdditionalCost;
        $this->sendPaymentMail       = $sendPaymentMail;
    }
    
    
    /**
     * @return string
     */
    public function toXmlString(): string
    {
        $indent    = $this->indent();
        $optionals = $this->optionalsAsXml();
        
        if (empty($optionals)) {
            return <<<XML
$indent<PaymentInfo>
$indent    <PaymentMethod>$this->paymentMethod</PaymentMethod>
$indent</PaymentInfo>
XML;
        }
        
        return <<<XML
$indent<PaymentInfo>
$indent    <PaymentMethod>$this->paymentMethod</PaymentMethod>
$optionals
$indent</PaymentInfo>
XML;
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
        
        if ($this->paymentDate !== null) {
            $date      = $this->paymentDate->format(self::DATE_FORMAT);
            $optionals .= "$indent<PaymentDate>$date</PaymentDate>\n";
        }
        if ($this->alreadyPaid !== null) {
            $cost      = $this->convertFloatToString($this->alreadyPaid);
            $optionals .= "$indent<AlreadyPaid>$cost</AlreadyPaid>\n";
        }
        if ($this->paymentAdditionalCost !== null) {
            $cost      = $this->convertFloatToString($this->paymentAdditionalCost);
            $optionals .= "$indent<PaymentAadditionalCost>$cost</PaymentAadditionalCost>\n";
        }
        if ($this->sendPaymentMail !== null) {
            $value     = $this->sendPaymentMail ? 'Yes' : 'No';
            $optionals .= "$indent<SendPaymentMail>$value</SendPaymentMail>\n";
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
