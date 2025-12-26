<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeRepository.inc.php 2018-01-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeRepository
 */
class ParcelTrackingCodeRepository
{
    /**
     * @var \GXParcelTrackingCodeWriter
     */
    protected $writer;
    
    /**
     * @var \ParcelTrackingCodeDeleter
     */
    protected $deleter;
    
    
    /**
     * ParcelTrackingCodeRepository constructor.
     *
     * @param \GXParcelTrackingCodeWriter $writer  Component to write data in orders_parcel_tracking_codes table.
     * @param \ParcelTrackingCodeDeleter  $deleter Component to remove data from orders_parcel_tracking_codes table.
     */
    public function __construct(GXParcelTrackingCodeWriter $writer, ParcelTrackingCodeDeleter $deleter)
    {
        $this->writer  = $writer;
        $this->deleter = $deleter;
    }
    
    
    /**
     * Saves the given parcel tracking code entity in the database.
     *
     * @param \GXParcelTrackingCode $parcelTrackingCode Entity to be saved.
     *
     * @return \ParcelTrackingCodeId Id of new order parcel tracking code.
     */
    public function save(GXParcelTrackingCode $parcelTrackingCode)
    {
        if ($parcelTrackingCode->isLightweight()) {
            return $this->writer->saveLightweight($parcelTrackingCode->orderId(),
                                                  $parcelTrackingCode->parcelServiceName(),
                                                  $parcelTrackingCode->url(),
                                                  $parcelTrackingCode->comment());
        }
        
        return $this->writer->save($parcelTrackingCode->parcelServiceId(),
                                   $parcelTrackingCode->orderId(),
                                   $parcelTrackingCode->trackingCode(),
                                   $parcelTrackingCode->languageId());
    }
    
    
    /**
     * Removes the parcel tracking code entry with the given tracking code id.
     *
     * @param \ParcelTrackingCodeId $id Id of entry to be removed.
     *
     * @return $this|\ParcelTrackingCodeRepository Same instance for chained method calls.
     */
    public function delete(ParcelTrackingCodeId $id)
    {
        $this->deleter->delete($id);
        
        return $this;
    }
}