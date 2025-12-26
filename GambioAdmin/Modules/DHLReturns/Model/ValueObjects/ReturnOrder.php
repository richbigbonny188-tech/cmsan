<?php
/* --------------------------------------------------------------
   ReturnOrder.php 2021-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns\Model\ValueObjects;

class ReturnOrder
{
    /**
     * @var string
     */
    private $receiverId;
    /**
     * @var SimpleAddress
     */
    private $senderAddress;
    /**
     * @var string
     */
    private $customerReference;
    /**
     * @var string
     */
    private $shipmentReference;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $telephoneNumber;
    /**
     * @var int
     */
    private $weightInGrams;
    /**
     * @var float
     */
    private $value;
    /**
     * @var string
     */
    private $returnDocumentType;
    /**
     * @var null|CustomsDocument
     */
    private $customsDocument;
    
    
    /**
     * ReturnOrder constructor.
     *
     * @param string        $receiverId
     * @param SimpleAddress $senderAddress
     */
    public function __construct(string $receiverId, SimpleAddress $senderAddress)
    {
        
        $this->receiverId         = $receiverId;
        $this->senderAddress      = $senderAddress;
        $this->customerReference  = '';
        $this->shipmentReference  = '';
        $this->email              = '';
        $this->telephoneNumber    = '';
        $this->weightInGrams      = 0;
        $this->value              = 0.0;
        $this->customsDocument    = null;
        $this->returnDocumentType = 'SHIPMENT_LABEL';
    }
    
    
    /**
     * @return string
     */
    public function getReceiverId(): string
    {
        return $this->receiverId;
    }
    
    
    /**
     * @param string $receiverId
     */
    public function setReceiverId(string $receiverId): void
    {
        $this->receiverId = $receiverId;
    }
    
    
    /**
     * @return SimpleAddress
     */
    public function getSenderAddress(): SimpleAddress
    {
        return $this->senderAddress;
    }
    
    
    /**
     * @param SimpleAddress $senderAddress
     */
    public function setSenderAddress(SimpleAddress $senderAddress): void
    {
        $this->senderAddress = $senderAddress;
    }
    
    
    /**
     * @return string
     */
    public function getCustomerReference(): string
    {
        return $this->customerReference;
    }
    
    
    /**
     * @param string $customerReference
     */
    public function setCustomerReference(string $customerReference): void
    {
        $this->customerReference = $customerReference;
    }
    
    
    /**
     * @return string
     */
    public function getShipmentReference(): string
    {
        return $this->shipmentReference;
    }
    
    
    /**
     * @param string $shipmentReference
     */
    public function setShipmentReference(string $shipmentReference): void
    {
        $this->shipmentReference = $shipmentReference;
    }
    
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    
    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    
    /**
     * @return string
     */
    public function getTelephoneNumber(): string
    {
        return $this->telephoneNumber;
    }
    
    
    /**
     * @param string $telephoneNumber
     */
    public function setTelephoneNumber(string $telephoneNumber): void
    {
        $this->telephoneNumber = $telephoneNumber;
    }
    
    
    /**
     * @return int
     */
    public function getWeightInGrams(): int
    {
        return $this->weightInGrams;
    }
    
    
    /**
     * @param int $weightInGrams
     */
    public function setWeightInGrams(int $weightInGrams): void
    {
        $this->weightInGrams = $weightInGrams;
    }
    
    
    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
    
    
    /**
     * @param float $value
     */
    public function setValue(float $value): void
    {
        $this->value = $value;
    }
    
    
    /**
     * @return string
     */
    public function getReturnDocumentType(): string
    {
        return $this->returnDocumentType;
    }
    
    
    /**
     * @param string $returnDocumentType
     */
    public function setReturnDocumentType(string $returnDocumentType): void
    {
        $this->returnDocumentType = $returnDocumentType;
    }
    
    
    /**
     * @return CustomsDocument|null
     */
    public function getCustomsDocument(): ?CustomsDocument
    {
        return $this->customsDocument;
    }
    
    
    /**
     * @param CustomsDocument|null $customsDocument
     */
    public function setCustomsDocument(?CustomsDocument $customsDocument): void
    {
        $this->customsDocument = $customsDocument;
    }
}