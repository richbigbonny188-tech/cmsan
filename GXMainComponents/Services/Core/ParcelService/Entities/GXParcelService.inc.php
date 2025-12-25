<?php
/* --------------------------------------------------------------
   GXParcelService.inc.php 2018-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXParcelService
 */
class GXParcelService implements \ParcelServiceInterface
{
    /**
     * @var int|null
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var bool
     */
    protected $isDefault;
    
    /**
     * @var \ParcelServiceDescriptionCollection
     */
    protected $descriptions;
    
    
    /**
     * GXParcelService constructor.
     *
     * @param \ParcelServiceId                    $id
     * @param \NonEmptyStringType                 $name
     * @param \BoolType                           $isDefault
     * @param \ParcelServiceDescriptionCollection $descriptions
     */
    public function __construct(
        \ParcelServiceId $id,
        \NonEmptyStringType $name,
        \BoolType $isDefault,
        \ParcelServiceDescriptionCollection $descriptions
    ) {
        $this->id           = $id->id();
        $this->name         = $name->asString();
        $this->isDefault    = $isDefault->asBool();
        $this->descriptions = $descriptions;
    }
    
    
    /**
     * @param \ParcelServiceId                    $id
     * @param string                              $name
     * @param bool                                $isDefault
     * @param \ParcelServiceDescriptionCollection $descriptions
     *
     * @return \GXParcelService New instance
     */
    public static function create(
        \ParcelServiceId $id,
        $name,
        $isDefault,
        \ParcelServiceDescriptionCollection $descriptions
    ) {
        return MainFactory::create(static::class,
                                   $id,
                                   new \NonEmptyStringType($name),
                                   new \BoolType($isDefault),
                                   $descriptions);
    }
    
    
    /**
     * Returns the ID of the parcel service if set. Otherwise null will be returned.
     *
     * @return int|null
     */
    public function id()
    {
        return $this->id;
    }
    
    
    /**
     * Returns the name of the parcel service.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
    
    
    /**
     * Checks if the parcel service is set as default.
     *
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }
    
    
    /**
     * Returns the parcel service descriptions as a ParcelServiceDescriptionCollection.
     *
     * @return \ParcelServiceDescriptionCollection
     */
    public function parcelServiceDescriptions()
    {
        return $this->descriptions;
    }
}