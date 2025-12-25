<?php
/* --------------------------------------------------------------
 ActiveRecordParcelTrackingCode.inc.php 2018-01-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ActiveRecordParcelTrackingCode
 */
class ActiveRecordParcelTrackingCode
{
    /**
     * @var \ParcelTrackingCodeRepository
     */
    protected $repository;
    
    /**
     * @var \GXParcelTrackingCode
     */
    protected $trackingCode;
    
    
    /**
     * ActiveRecordTrackingCode constructor.
     *
     * @param \ParcelTrackingCodeRepository $repository   Repository for parcel tracking codes.
     * @param \GXParcelTrackingCode         $trackingCode Parcel tracking code entity.
     */
    public function __construct(ParcelTrackingCodeRepository $repository, GXParcelTrackingCode $trackingCode)
    {
        $this->repository   = $repository;
        $this->trackingCode = $trackingCode;
    }
    
    
    /**
     * Saves the entity data into the database.
     *
     * @return \ParcelTrackingCodeId Id of new order parcel tracking code.
     */
    public function save()
    {
        return $this->repository->save($this->trackingCode);
    }
}