<?php
/* --------------------------------------------------------------
   Manufacturer.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Manufacturer
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Entities
 */
class Manufacturer implements ManufacturerInterface
{
    /**
     * @var int
     */
    protected $id = 0;
    
    /**
     * @var string
     */
    protected $name = '';
    
    /**
     * @var string
     */
    protected $image = '';
    
    /**
     * @var \DateTime
     */
    protected $dateAdded;
    
    /**
     * @var \DateTime
     */
    protected $lastModified;
    
    /**
     * @var \EditableKeyValueCollection
     */
    protected $urls;
    
    
    /**
     * Manufacturer constructor.
     *
     * @param \EditableKeyValueCollection $urls
     *
     * @throws Exception
     */
    public function __construct(EditableKeyValueCollection $urls)
    {
        $this->dateAdded = $this->lastModified = new DateTime();
        $this->urls      = $urls;
    }
    
    
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the id.
     *
     * @param \IdType $manufacturerId Id to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setId(IdType $manufacturerId)
    {
        $this->id = $manufacturerId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the manufacturer name.
     *
     * @return string Language specific name value.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Sets the manufacturer name.
     *
     * @param \StringType $name Name value to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setName(StringType $name)
    {
        $this->name = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the manufacturer image path.
     *
     * @return string Language specific image Path value.
     */
    public function getImage()
    {
        return $this->image;
    }
    
    
    /**
     * Sets the image path.
     *
     * @param \StringType $imagePath Image path to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setImage(StringType $imagePath)
    {
        $this->image = $imagePath->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the datetime.
     *
     * @return \DateTime Order purchase datetime.
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
    
    
    /**
     * Sets the datetime.
     *
     * @param \DateTime $date Date to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setDateAdded(\DateTime $date)
    {
        $this->dateAdded = $date;
        
        return $this;
    }
    
    
    /**
     * Returns the last modified datetime.
     *
     * @return \DateTime Order purchase datetime.
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }
    
    
    /**
     * Sets the last modified datetime.
     *
     * @param \DateTime $date Last Date to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setLastModified(\DateTime $date)
    {
        $this->lastModified = $date;
        
        return $this;
    }
    
    
    /**
     * Returns the url.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getUrl(LanguageCode $languageCode)
    {
        return $this->urls->getValue($languageCode->asString());
    }
    
    
    /**
     * Sets the url.
     *
     * @param \StringType   $title        Title.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setUrl(StringType $title, LanguageCode $languageCode)
    {
        $this->urls->setValue($languageCode->asString(), $title->asString());
        
        return $this;
    }
    
    
    /**
     * Returns all language specific title.
     *
     * @return  array Language specific title value.
     */
    public function getUrls()
    {
        return $this->urls->getArray();
    }
}
