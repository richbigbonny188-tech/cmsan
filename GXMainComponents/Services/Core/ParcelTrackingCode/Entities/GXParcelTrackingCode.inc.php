<?php
/* --------------------------------------------------------------
 GXParcelTrackingCode.inc.php 2018-01-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class GXParcelTrackingCode
 */
class GXParcelTrackingCode
{
    /**
     * @var \ParcelTrackingCodeServiceId
     */
    protected $parcelServiceId;
    
    /**
     * @var \ParcelTrackingCodeOrderId
     */
    protected $orderId;
    
    /**
     * @var \OrderParcelTrackingCode
     */
    protected $trackingCode;
    
    /**
     * @var \ParcelTrackingCodeLanguageId
     */
    protected $languageId;
    
    /**
     * @var bool
     */
    protected $isLightweight;
    
    /**
     * @var \ParcelTrackingCodeServiceName
     */
    protected $name;
    
    /**
     * @var \ParcelTrackingCodeUrl
     */
    protected $url;
    
    /**
     * @var \ParcelTrackingCodeComment
     */
    protected $comment;
    
    
    /**
     * OrderParcelTrackingCode constructor.
     * Private to enforce usage of named constructors.
     */
    private function __construct()
    {
    }
    
    
    /**
     * Creates a new order parcel tracking code entity.
     * This entity version will fetch the corresponding data from related tables.
     *
     * @param \ParcelTrackingCodeServiceId  $parcelServiceId Id of related parcel service.
     * @param \ParcelTrackingCodeOrderId    $orderId         Id of related order.
     * @param \OrderParcelTrackingCode      $trackingCode    Tracking code for order.
     * @param \ParcelTrackingCodeLanguageId $languageId      Language id (for language specific comment).
     *
     * @return GXParcelTrackingCode New instance.
     */
    public static function create(
        ParcelTrackingCodeServiceId $parcelServiceId,
        ParcelTrackingCodeOrderId $orderId,
        OrderParcelTrackingCode $trackingCode,
        ParcelTrackingCodeLanguageId $languageId
    ) {
        $parcelTrackingCode                  = new static();
        $parcelTrackingCode->parcelServiceId = $parcelServiceId;
        $parcelTrackingCode->orderId         = $orderId;
        $parcelTrackingCode->trackingCode    = $trackingCode;
        $parcelTrackingCode->languageId      = $languageId;
        $parcelTrackingCode->isLightweight   = false;
        
        return $parcelTrackingCode;
    }
    
    
    /**
     * Creates a new order parcel tracking code entity.
     * The lightweight version renounce on related data.
     *
     * @param \ParcelTrackingCodeOrderId      $orderId Id of related order.
     * @param \ParcelTrackingCodeServiceName  $name    Name of parcel service-
     * @param \ParcelTrackingCodeUrl          $url     Tracking code url.
     * @param \ParcelTrackingCodeComment|null $comment Comment of tracking code for order.
     *
     * @return GXParcelTrackingCode New instance.
     */
    public static function createLightweight(
        ParcelTrackingCodeOrderId $orderId,
        ParcelTrackingCodeServiceName $name,
        ParcelTrackingCodeUrl $url,
        ParcelTrackingCodeComment $comment = null
    ) {
        $parcelTrackingCode                = new static();
        $parcelTrackingCode->orderId       = $orderId;
        $parcelTrackingCode->name          = $name;
        $parcelTrackingCode->url           = $url;
        $parcelTrackingCode->comment       = $comment;
        $parcelTrackingCode->isLightweight = true;
        
        return $parcelTrackingCode;
    }
    
    
    public function isLightweight()
    {
        return $this->isLightweight;
    }
    
    
    /**
     * Returns the parcels service id.
     *
     * @return \ParcelTrackingCodeServiceId
     */
    public function parcelServiceId()
    {
        return $this->parcelServiceId;
    }
    
    
    /**
     * Returns the order id.
     *
     * @return \ParcelTrackingCodeOrderId
     */
    public function orderId()
    {
        return $this->orderId;
    }
    
    
    /**
     * Returns the tracking code.
     *
     * @return \OrderParcelTrackingCode
     */
    public function trackingCode()
    {
        return $this->trackingCode;
    }
    
    
    /**
     * Returns the language id.
     *
     * @return \ParcelTrackingCodeLanguageId
     */
    public function languageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Returns the name of the parcel service name.
     *
     * @return \ParcelTrackingCodeServiceName
     */
    public function parcelServiceName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the tracking code url.
     *
     * @return \ParcelTrackingCodeUrl
     */
    public function url()
    {
        return $this->url;
    }
    
    
    /**
     * Returns the tracking code comment.par
     *
     * @return \ParcelTrackingCodeComment
     */
    public function comment()
    {
        return $this->comment;
    }
}