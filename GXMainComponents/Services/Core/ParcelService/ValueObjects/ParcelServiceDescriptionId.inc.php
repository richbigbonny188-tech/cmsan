<?php
/* --------------------------------------------------------------
   ParcelServiceDescriptionId.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceDescriptionId
 */
class ParcelServiceDescriptionId
{
    /**
     * @var \ParcelServiceId
     */
    protected $parcelServiceId;
    
    /**
     * @var int
     */
    protected $languageId;
    
    
    /**
     * ParcelServiceDescriptionId constructor.
     *
     * @param \ParcelServiceId $parcelServiceId Parcel service ID
     * @param \IdType          $languageId      Language ID
     */
    public function __construct(\ParcelServiceId $parcelServiceId, \IdType $languageId)
    {
        $this->parcelServiceId = $parcelServiceId;
        $this->languageId      = $languageId->asInt();
    }
    
    
    /**
     * Named constructor of parcel service description id.
     *
     * @param \ParcelServiceId $parcelServiceId Parcel service ID
     * @param int              $languageId      Language ID
     *
     * @return \ParcelServiceDescriptionId New instance.
     */
    public static function create(\ParcelServiceId $parcelServiceId, $languageId)
    {
        return MainFactory::create(static::class, $parcelServiceId, new \IdType($languageId));
    }
    
    
    /**
     * Returns the parcel service ID.
     *
     * @return \ParcelServiceId
     */
    public function parcelServiceId()
    {
        return $this->parcelServiceId;
    }
    
    
    /**
     * Returns the language ID.
     *
     * @return int
     */
    public function languageId()
    {
        return $this->languageId;
    }
}