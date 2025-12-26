<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeId.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeId
 */
class ParcelTrackingCodeId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * TrackingCodesId constructor.
     * Private to enforce usage of named constructor.
     *
     * @param int $id Id of tracking code.
     */
    protected function __construct($id)
    {
        new IdType($id);
        $this->id = $id;
    }
    
    
    /**
     * Named constructor of tracking code id.
     *
     * @param int $id Id of tracking code.
     *
     * @return ParcelTrackingCodeId New instance.
     */
    public static function create($id)
    {
        return new static($id);
    }
    
    
    /**
     * Returns the id of the tracking code.
     *
     * @return int $id Id of tracking code.
     */
    public function is()
    {
        return $this->id;
    }
}