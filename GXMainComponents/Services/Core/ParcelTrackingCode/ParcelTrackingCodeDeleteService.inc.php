<?php
/* --------------------------------------------------------------
 TrackingCodesDeleteService.inc.php 2018-01-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeDeleteService
 */
class ParcelTrackingCodeDeleteService
{
    /**
     * @var \ParcelTrackingCodeRepository
     */
    protected $repository;
    
    
    /**
     * ParcelTrackingCodeDeleteService constructor.
     *
     * @param \ParcelTrackingCodeRepository $repository Repository for parcel tracking codes.
     */
    public function __construct(ParcelTrackingCodeRepository $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Removes the parcel tracking codes entry with the given id.
     *
     * @param \ParcelTrackingCodeId $id Id of parcel tracking code entity to be removed.
     *
     * @return $this|\ParcelTrackingCodeDeleteService Same instance for chained method calls.
     */
    public function delete(ParcelTrackingCodeId $id)
    {
        $this->repository->delete($id);
        
        return $this;
    }
}