<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeServiceName.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeServiceName
 */
class ParcelTrackingCodeServiceName
{
    /**
     * @var string
     */
    protected $name;
    
    
    /**
     * TrackingCodesParcelServiceName constructor.
     * Private to enforce usage of named constructor.
     *
     * @param string $name Name of tracking code's parcel service.
     */
    protected function __construct($name)
    {
        new NonEmptyStringType($name);
        $this->name = $name;
    }
    
    
    /**
     * Named constructor of tracking code's parcel service name.
     *
     * @param string $name Name of tracking code's parcel service.
     *
     * @return ParcelTrackingCodeServiceName New instance.
     */
    public static function name($name)
    {
        return new static($name);
    }
    
    
    /**
     * Returns the comment of a tracking code.
     *
     * @return string $name Name of tracking code's parcel service.
     */
    public function is()
    {
        return $this->name;
    }
}