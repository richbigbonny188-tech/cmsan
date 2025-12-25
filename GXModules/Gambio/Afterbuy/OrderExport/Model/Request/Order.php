<?php
/* --------------------------------------------------------------
   Order.php 2023-10-18
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
 * Class Order
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Model
 */
class Order implements XmlSerializable
{
    use EscapeXmlTrait;
    
    private const DATE_FORMAT = 'd.m.Y H:i:s';
    
    /**
     * @var int
     */
    private int $orderID;
    
    
    /**
     * @var int
     */
    private int $itemID;
    
    
    /**
     * @var BuyerInfo
     */
    private BuyerInfo $buyerInfo;
    
    
    /**
     * @var PaymentInfo
     */
    private PaymentInfo $paymentInfo;
    
    
    /**
     * @var ShippingInfo
     */
    private ShippingInfo $shippingInfo;
    
    
    /**
     * @var string|null
     */
    private ?string $additionalInfo;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $mailDate;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $reminderMailDate;
    
    
    /**
     * @var string|null
     */
    private ?string $userComment;
    
    
    /**
     * @var string|null
     */
    private ?string $orderMemo;
    
    
    /**
     * @var string|null
     */
    private ?string $invoiceMemo;
    
    
    /**
     * @var int|null
     */
    private ?int $invoiceNumber;
    
    
    /**
     * @var bool|null
     */
    private ?bool $orderExported;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $invoiceDate;
    
    
    /**
     * @var bool|null
     */
    private ?bool $hideOrder;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $reminder1Date;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $reminder2Date;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $feedbackDate;
    
    
    /**
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $xmlDate;
    
    
    /**
     * @param int                    $orderID
     * @param int                    $itemID
     * @param BuyerInfo              $buyerInfo
     * @param PaymentInfo            $paymentInfo
     * @param ShippingInfo           $shippingInfo
     * @param string|null            $additionalInfo
     * @param DateTimeImmutable|null $mailDate
     * @param DateTimeImmutable|null $reminderMailDate
     * @param string|null            $userComment
     * @param string|null            $orderMemo
     * @param string|null            $invoiceMemo
     * @param int|null               $invoiceNumber
     * @param bool|null              $orderExported
     * @param DateTimeImmutable|null $invoiceDate
     * @param bool|null              $hideOrder
     * @param DateTimeImmutable|null $reminder1Date
     * @param DateTimeImmutable|null $reminder2Date
     * @param DateTimeImmutable|null $feedbackDate
     * @param DateTimeImmutable|null $xmlDate
     */
    public function __construct(
        int               $orderID,
        int               $itemID,
        BuyerInfo         $buyerInfo,
        PaymentInfo       $paymentInfo,
        ShippingInfo      $shippingInfo,
        string            $additionalInfo = null,
        DateTimeImmutable $mailDate = null,
        DateTimeImmutable $reminderMailDate = null,
        string            $userComment = null,
        string            $orderMemo = null,
        string            $invoiceMemo = null,
        int               $invoiceNumber = null,
        bool              $orderExported = null,
        DateTimeImmutable $invoiceDate = null,
        bool              $hideOrder = null,
        DateTimeImmutable $reminder1Date = null,
        DateTimeImmutable $reminder2Date = null,
        DateTimeImmutable $feedbackDate = null,
        DateTimeImmutable $xmlDate = null
    ) {
        $this->orderID          = $orderID;
        $this->itemID           = $itemID;
        $this->buyerInfo        = $buyerInfo;
        $this->paymentInfo      = $paymentInfo;
        $this->shippingInfo     = $shippingInfo;
        $this->additionalInfo   = $this->escapeForXml($additionalInfo);
        $this->mailDate         = $mailDate;
        $this->reminderMailDate = $reminderMailDate;
        $this->userComment      = $this->escapeForXml($userComment);
        $this->orderMemo        = $this->escapeForXml($orderMemo);
        $this->invoiceMemo      = $this->escapeForXml($invoiceMemo);
        $this->invoiceNumber    = $invoiceNumber;
        $this->orderExported    = $orderExported;
        $this->invoiceDate      = $invoiceDate;
        $this->hideOrder        = $hideOrder;
        $this->reminder1Date    = $reminder1Date;
        $this->reminder2Date    = $reminder2Date;
        $this->feedbackDate     = $feedbackDate;
        $this->xmlDate          = $xmlDate;
    }
    
    
    /**
     * @inheritDoc
     */
    public function toXmlString(): string
    {
        $indent = $this->indent();
        
        $optionals    = $this->optionalsAsXml();
        $buyerInfo    = $this->buyerInfo->toXmlString();
        $paymentInfo  = $this->paymentInfo->toXmlString();
        $shippingInfo = $this->shippingInfo->toXmlString();
        
        // Implement <ItemID> as soon as possible, but right now its unknown. Move the line below into the xml then
        //$indent    <ItemID>$this->itemID</ItemID>
        
        if (empty($optionals)) {
            return <<<XML
$indent<Order>
$indent    <OrderID>$this->orderID</OrderID>
$buyerInfo
$paymentInfo
$shippingInfo
$indent</Order>
XML;
        }
        
        return <<<XML
$indent<Order>
$indent    <OrderID>$this->orderID</OrderID>
$optionals
$buyerInfo
$paymentInfo
$shippingInfo
$indent</Order>
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
        
        if ($this->additionalInfo !== null) {
            $optionals .= "$indent<AdditionalInfo>$this->additionalInfo</AdditionalInfo>\n";
        }
        if ($this->mailDate !== null) {
            $date      = $this->reminder1Date->format(self::DATE_FORMAT);
            $optionals .= "$indent<MailDate>$date</MailDate>\n";
        }
        if ($this->reminderMailDate !== null) {
            $date      = $this->reminder1Date->format(self::DATE_FORMAT);
            $optionals .= "$indent<ReminderMailDate>$date</ReminderMailDate>\n";
        }
        if ($this->userComment !== null) {
            $optionals .= "$indent<UserComment>$this->userComment</UserComment>\n";
        }
        if ($this->orderMemo !== null) {
            $optionals .= "$indent<OrderMemo>$this->orderMemo</OrderMemo>\n";
        }
        if ($this->invoiceMemo !== null) {
            $optionals .= "$indent<InvoiceMemo>$this->invoiceMemo</InvoiceMemo>\n";
        }
        if ($this->invoiceNumber !== null) {
            $optionals .= "$indent<InvoiceNumber>$this->invoiceNumber</InvoiceNumber>\n";
        }
        if ($this->orderExported !== null) {
            $value     = $this->hideOrder ? 'ja' : 'nein';
            $optionals .= "$indent<OrderExported>$value</OrderExported>\n";
        }
        if ($this->invoiceDate !== null) {
            $date      = $this->invoiceDate->format(self::DATE_FORMAT);
            $optionals .= "$indent<InvoiceDate>$date</InvoiceDate>\n";
        }
        if ($this->hideOrder !== null) {
            $value     = $this->hideOrder ? 'ja' : 'nein';
            $optionals .= "$indent<HideOrder>$value</HideOrder>\n";
        }
        if ($this->reminder1Date !== null) {
            $date      = $this->reminder1Date->format(self::DATE_FORMAT);
            $optionals .= "$indent<Reminder1Date>$date</Reminder1Date>\n";
        }
        if ($this->reminder2Date !== null) {
            $date      = $this->reminder2Date->format(self::DATE_FORMAT);
            $optionals .= "$indent<Reminder2Date>$date</Reminder2Date>\n";
        }
        if ($this->feedbackDate !== null) {
            $date      = $this->feedbackDate->format(self::DATE_FORMAT);
            $optionals .= "$indent<FeedbackDate>$date</FeedbackDate>\n";
        }
        if ($this->xmlDate !== null) {
            $date      = $this->xmlDate->format(self::DATE_FORMAT);
            $optionals .= "$indent<XmlDate>$date</XmlDate>\n";
        }
        
        return rtrim($optionals);
    }
    
    
    /**
     * @inheritDoc
     */
    public function indent(): string
    {
        return str_repeat(' ', 8);
    }
}
