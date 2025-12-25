<?php
/* --------------------------------------------------------------
   TrackingCode.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Model;

use DateTime;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Core\Event\Abstracts\AbstractEventRaisingEntity;
use Webmozart\Assert\Assert;

/**
 * Class TrackingCode
 *
 * @package Gambio\Admin\Modules\TrackingCode\Model
 */
class TrackingCode extends AbstractEventRaisingEntity
{
    /**
     * @var TrackingCodeId
     */
    private $id;
    
    /**
     * @var OrderId
     */
    private $orderId;
    
    /**
     * @var string
     */
    private $code;
    
    /**
     * @var ParcelServiceDetails
     */
    private $parcelServiceDetails;
    
    /**
     * @var DateTime
     */
    private $createdOn;
    
    /**
     * @var bool
     */
    private $isReturnDelivery;
    
    
    /**
     * TrackingCode constructor.
     *
     * @param TrackingCodeId       $id
     * @param OrderId              $orderId
     * @param string               $code
     * @param ParcelServiceDetails $parcelServiceDetails
     * @param DateTime             $createdOn
     * @param bool                 $isReturnDelivery
     */
    private function __construct(
        TrackingCodeId       $id,
        OrderId              $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        DateTime             $createdOn,
        bool                 $isReturnDelivery
    ) {
        $this->id                   = $id;
        $this->orderId              = $orderId;
        $this->code                 = $code;
        $this->parcelServiceDetails = $parcelServiceDetails;
        $this->createdOn            = $createdOn;
        $this->isReturnDelivery     = $isReturnDelivery;
    }
    
    
    /**
     * @param TrackingCodeId       $id
     * @param OrderId              $orderId
     * @param string               $code
     * @param ParcelServiceDetails $parcelServiceDetails
     * @param DateTime             $createdOn
     * @param bool                 $isReturnDelivery
     *
     * @return TrackingCode
     */
    public static function create(
        TrackingCodeId       $id,
        OrderId              $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        DateTime             $createdOn,
        bool                 $isReturnDelivery
    ): TrackingCode {
        Assert::notWhitespaceOnly($code, 'Code can not be empty.');
        
        return new self($id, $orderId, $code, $parcelServiceDetails, $createdOn, $isReturnDelivery);
    }
    
    
    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id->value();
    }
    
    
    /**
     * @return int
     */
    public function orderId(): int
    {
        return $this->orderId->value();
    }
    
    
    /**
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
    
    
    /**
     * @return int
     */
    public function parcelServiceId(): int
    {
        return $this->parcelServiceDetails->parcelServiceId();
    }
    
    
    /**
     * @return string
     */
    public function parcelServiceLanguageCode(): string
    {
        return $this->parcelServiceDetails->languageCode();
    }
    
    
    /**
     * @return string
     */
    public function parcelServiceName(): string
    {
        return $this->parcelServiceDetails->name();
    }
    
    
    /**
     * @return string
     */
    public function parcelServiceUrl(): string
    {
        return $this->parcelServiceDetails->url();
    }
    
    
    /**
     * @return string
     */
    public function parcelServiceComment(): string
    {
        return $this->parcelServiceDetails->comment();
    }
    
    
    /**
     * @return string
     */
    public function parcelServiceShipmentType(): string
    {
        return $this->parcelServiceDetails->shipmentType();
    }
    
    
    /**
     * @param string $format
     *
     * @return string
     */
    public function createdOn(string $format = 'Y-m-d H:i:s'): string
    {
        return $this->createdOn->format($format);
    }
    
    
    /**
     * @return bool
     */
    public function isReturnDelivery(): bool
    {
        return $this->isReturnDelivery;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'               => $this->id(),
            'orderId'          => $this->orderId(),
            'code'             => $this->code(),
            'isReturnDelivery' => $this->isReturnDelivery(),
            'parcelService'    => $this->parcelServiceDetails->toArray(),
            'createdOn'        => $this->createdOn(),
        ];
    }
    
}