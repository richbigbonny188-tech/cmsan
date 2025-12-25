<?php
/* --------------------------------------------------------------
 OrderParcelTrackingCode.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class OrderParcelTrackingCode
 */
class OrderParcelTrackingCode
{
    /**
     * @var string
     */
    protected $code;
    
    
    /**
     * TrackingCode constructor.
     * Private to enforce usage of named constructor.
     *
     * @param string $trackingCode Parcel tracking code of orders.
     */
    protected function __construct($trackingCode)
    {
        new NonEmptyStringType($trackingCode);
        $this->code = $trackingCode;
    }
    
    
    /**
     * Named constructor of parcel tracking code.
     *
     * @param string $trackingCode Parcel tracking code of orders.
     *
     * @return OrderParcelTrackingCode New instance.
     */
    public static function create($trackingCode)
    {
        return new static($trackingCode);
    }
    
    
    /**
     * Returns the order's parcel tracking code.
     *
     * @return string Parcel tracking code of orders.
     */
    public function is()
    {
        return $this->code;
    }
}