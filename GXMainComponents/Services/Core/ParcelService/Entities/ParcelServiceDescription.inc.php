<?php
/* --------------------------------------------------------------
   ParcelServiceDescription.inc.php 2023-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceDescription
 */
class ParcelServiceDescription implements \ParcelServiceDescriptionInterface
{
    /**
     * @var int|null
     */
    protected $parcelServiceId;
    
    /**
     * @var int
     */
    protected $languageId;
    
    /**
     * @var string
     */
    protected $url;
    
    /**
     * @var string
     */
    protected $comment;
    
    
    /**
     * ParcelServiceDescription constructor.
     *
     * @param \ParcelServiceDescriptionId $id      Parcel service description ID
     * @param \NonEmptyStringType         $url     Parcel service url
     * @param \StringType                 $comment Parcel serivce comment
     */
    public function __construct(\ParcelServiceDescriptionId $id, \StringType $url, \StringType $comment)
    {
        $this->parcelServiceId = $id->parcelServiceId()->id();
        $this->languageId      = $id->languageId();
        $this->url             = $url->asString();
        $this->comment         = $comment->asString();
    }
    
    
    /**
     * @param \ParcelServiceDescriptionId $id      Parcel service description ID
     * @param string                      $url     Parcel service url
     * @param string                      $comment Parcel serivce comment
     *
     * @return \ParcelServiceDescription New instance
     */
    public static function create($id, $url, $comment)
    {
        return MainFactory::create(static::class, $id, new \StringType($url), new \StringType($comment));
    }
    
    
    /**
     * Returns the parcel service ID.
     *
     * @return int|null
     */
    public function parcelServiceId()
    {
        return $this->parcelServiceId;
    }
    
    
    /**
     * Returns the language ID as int.
     *
     * @return int
     */
    public function languageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Returns the parcel service url.
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }
    
    
    /**
     * Returns the parcel service comment.
     *
     * @return string
     */
    public function comment()
    {
        return $this->comment;
    }
}