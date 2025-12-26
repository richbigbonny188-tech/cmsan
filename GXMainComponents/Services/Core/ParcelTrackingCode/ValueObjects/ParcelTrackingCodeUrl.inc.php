<?php
/* --------------------------------------------------------------
 ParcelTrackingCodeUrl.inc.php 2018-01-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2018 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class ParcelTrackingCodeUrl
 */
class ParcelTrackingCodeUrl
{
    /**
     * @var string
     */
    protected $url;
    
    
    /**
     * TrackingCodesUrl constructor.
     * Private to enforce usage of named constructor.
     *
     * @param string $url Tracking url of parcel service.
     */
    protected function __construct($url)
    {
        new NonEmptyStringType($url);
        if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
            throw new InvalidArgumentException('Parcel service tracking url must begin with whether "http://" or "https://"');
        }
        
        $this->url = $url;
    }
    
    
    /**
     * Named constructor of tracking code url.
     *
     * @param string $url Tracking url of parcel service.
     *
     * @return ParcelTrackingCodeUrl New instance.
     */
    public static function create($url)
    {
        return new static($url);
    }
    
    
    /**
     * Returns the tracking url of a parcel service.
     *
     * @return string Tracking url of parcel service.
     */
    public function is()
    {
        return $this->url;
    }
}