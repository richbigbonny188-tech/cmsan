<?php
/* --------------------------------------------------------------
   ParcelServiceId.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceId
 */
class ParcelServiceId
{
    /**
     * @var int|null
     */
    protected $id;
    
    
    /**
     * ParcelServiceId constructor.
     *
     * @param \IdType|null $parcelServiceId Parcel service id.
     */
    public function __construct(\IdType $parcelServiceId = null)
    {
        $this->id = $parcelServiceId ? $parcelServiceId->asInt() : null;
    }
    
    
    /**
     * Named constructor of parcel service id.
     *
     * @param int|null $parcelServiceId Parcel service id.
     *
     * @return \ParcelServiceId New instance.
     */
    public static function create($parcelServiceId = null)
    {
        return MainFactory::create(static::class, $parcelServiceId ? new \IdType($parcelServiceId) : null);
    }
    
    
    /**
     * Returns the parcel service id.
     *
     * @return int|null Id of parcel service.
     */
    public function id()
    {
        return $this->id;
    }
}