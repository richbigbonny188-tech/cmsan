<?php
/* --------------------------------------------------------------
   HermesHSIShipmentOrder.inc.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

class HermesHSIShipmentOrder implements JsonSerializable
{
    /** @var string */
    protected $mandator;
    
    /** @var string */
    protected $clientReference;
    
    /** @var HermesHSIAddress */
    protected $receiverAddress;
    
    /** @var HermesHSIName */
    protected $receiverName;
    
    /** @var HermesHSIContact */
    protected $receiverContact;
    
    /** @var HermesHSIAddress */
    protected $senderAddress;
    
    /** @var HermesHSIName */
    protected $senderName;
    
    /** @var HermesHSIParcel */
    protected $parcel;
    
    /** @var HermesHSIServiceParameters */
    protected $service;
    
    
    public function __construct(
        HermesHSIAddress $receiverAddress,
        HermesHSIName $receiverName,
        HermesHSIAddress $senderAddress,
        HermesHSIName $senderName,
        HermesHSIParcel $parcel,
        HermesHSIServiceParameters $service
    ) {
        $this->mandator        = '';
        $this->clientReference = '';
        $this->receiverAddress = $receiverAddress;
        $this->receiverName    = $receiverName;
        $this->receiverContact = null;
        $this->senderAddress   = $senderAddress;
        $this->senderName      = $senderName;
        $this->parcel          = $parcel;
        $this->service         = $service;
    }
    
    
    /**
     * @return string
     */
    public function getMandator(): string
    {
        return $this->mandator;
    }
    
    
    /**
     * @param string $mandator
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setMandator(string $mandator): void
    {
        if (preg_match('/^\d{1,11}$/', $mandator) !== 1) {
            throw new HermesHSIInvalidDataException('mandator must be numeric (up to 11 digits)');
        }
        $this->mandator = $mandator;
    }
    
    
    /**
     * @return string
     */
    public function getClientReference(): string
    {
        return $this->clientReference;
    }
    
    
    /**
     * @param string $clientReference
     *
     * @throws HermesHSIInvalidDataException
     */
    public function setClientReference(string $clientReference): void
    {
        if (mb_strlen($clientReference) > 15) {
            throw new HermesHSIInvalidDataException('clientReference maximum length is 15 characters');
        }
        $this->clientReference = $clientReference;
    }
    
    
    /**
     * @return HermesHSIAddress
     */
    public function getReceiverAddress(): HermesHSIAddress
    {
        return $this->receiverAddress;
    }
    
    
    /**
     * @param HermesHSIAddress $receiverAddress
     */
    public function setReceiverAddress(HermesHSIAddress $receiverAddress): void
    {
        $this->receiverAddress = $receiverAddress;
    }
    
    
    /**
     * @return HermesHSIName
     */
    public function getReceiverName(): HermesHSIName
    {
        return $this->receiverName;
    }
    
    
    /**
     * @param HermesHSIName $receiverName
     */
    public function setReceiverName(HermesHSIName $receiverName): void
    {
        $this->receiverName = $receiverName;
    }
    
    
    /**
     * @return HermesHSIAddress
     */
    public function getSenderAddress(): HermesHSIAddress
    {
        return $this->senderAddress;
    }
    
    
    /**
     * @param HermesHSIAddress $senderAddress
     */
    public function setSenderAddress(HermesHSIAddress $senderAddress): void
    {
        $this->senderAddress = $senderAddress;
    }
    
    
    /**
     * @return HermesHSIName
     */
    public function getSenderName(): HermesHSIName
    {
        return $this->senderName;
    }
    
    
    /**
     * @param HermesHSIName $senderName
     */
    public function setSenderName(HermesHSIName $senderName): void
    {
        $this->senderName = $senderName;
    }
    
    
    /**
     * @return HermesHSIParcel
     */
    public function getParcel(): HermesHSIParcel
    {
        return $this->parcel;
    }
    
    
    /**
     * @param HermesHSIParcel $parcel
     */
    public function setParcel(HermesHSIParcel $parcel): void
    {
        $this->parcel = $parcel;
    }
    
    
    /**
     * @return HermesHSIServiceParameters
     */
    public function getService(): HermesHSIServiceParameters
    {
        return $this->service;
    }
    
    
    /**
     * @param HermesHSIServiceParameters $service
     */
    public function setService(HermesHSIServiceParameters $service): void
    {
        $this->service = $service;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'mandator'        => $this->mandator,
            'clientReference' => $this->clientReference,
            'receiverAddress' => $this->receiverAddress,
            'receiverName'    => $this->receiverName,
            'receiverContact' => $this->receiverContact,
            'senderAddress'   => $this->senderAddress,
            'senderName'      => $this->senderName,
            'parcel'          => $this->parcel,
            'service'         => $this->service,
        ]);
    }
    
    
    /**
     * @return HermesHSIContact
     */
    public function getReceiverContact(): HermesHSIContact
    {
        return $this->receiverContact;
    }
    
    
    /**
     * @param HermesHSIContact $receiverContact
     */
    public function setReceiverContact(HermesHSIContact $receiverContact): void
    {
        $this->receiverContact = $receiverContact;
    }
}
