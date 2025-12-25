<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeServiceId.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeServiceId
 */
class ParcelTrackingCodeServiceId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * TrackingCodesParcelServiceId constructor.
     * Private to enforce usage of named constructor.
     *
     * @param int $id Id of tracking code's parcel service.
     */
    protected function __construct($id)
    {
        new IdType($id);
        $this->id = $id;
    }
    
    
    /**
     * Named constructor of tracking code's parcel service id.
     *
     * @param int $id Id of tracking code's parcel service.
     *
     * @return ParcelTrackingCodeServiceId New instance.
     */
    public static function create($id)
    {
        return new static($id);
    }
    
    
    /**
     * Returns the id of the tracking code's parcel service.
     *
     * @return int $id Id of tracking code's parcel service.
     */
    public function is()
    {
        return $this->id;
    }
}