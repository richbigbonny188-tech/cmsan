<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeOrderId.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeOrderId
 */
class ParcelTrackingCodeOrderId
{
    /**
     * @var int
     */
    protected $id;
    
    
    /**
     * TrackingCodesOrderId constructor.
     * Private to enforce usage of named constructor.
     *
     * @param int $id Order id of tracking code.
     */
    protected function __construct($id)
    {
        new IdType($id);
        $this->id = $id;
    }
    
    
    /**
     * Named constructor of tracking code's order id.
     *
     * @param int $id Order id of tracking code.
     *
     * @return ParcelTrackingCodeOrderId New instance.
     */
    public static function create($id)
    {
        return new static($id);
    }
    
    
    /**
     * Returns the order id of the tracking code.
     *
     * @return int $id Order id of tracking code.
     */
    public function is()
    {
        return $this->id;
    }
}