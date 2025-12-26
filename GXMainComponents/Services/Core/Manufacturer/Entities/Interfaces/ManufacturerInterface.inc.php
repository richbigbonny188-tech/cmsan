<?php
/* --------------------------------------------------------------
   ManufacturerInterface.php 2017-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ManufacturerInterface
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Entities
 */
interface ManufacturerInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();
    
    
    /**
     * Sets the id.
     *
     * @param \IdType $manufacturerId Id to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setId(IdType $manufacturerId);
    
    
    /**
     * Returns the manufacturer name.
     *
     * @return string Language specific name value.
     */
    public function getName();
    
    
    /**
     * Sets the manufacturer name.
     *
     * @param \StringType $name Name value to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setName(StringType $name);
    
    
    /**
     * Returns the manufacturer image path.
     *
     * @return string Language specific image Path value.
     */
    public function getImage();
    
    
    /**
     * Sets the image path.
     *
     * @param \StringType $imagePath Image path to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setImage(StringType $imagePath);
    
    
    /**
     * Returns the datetime.
     *
     * @return \DateTime Order purchase datetime.
     */
    public function getDateAdded();
    
    
    /**
     * Sets the datetime.
     *
     * @param \DateTime $date Date to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setDateAdded(\DateTime $date);
    
    
    /**
     * Returns the last modified datetime.
     *
     * @return \DateTime Order purchase datetime.
     */
    public function getLastModified();
    
    
    /**
     * Sets the last modified datetime.
     *
     * @param \DateTime $date Last Date to be set.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setLastModified(\DateTime $date);
    
    
    /**
     * Returns the url.
     *
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return string Language specific name value.
     */
    public function getUrl(LanguageCode $languageCode);
    
    
    /**
     * Sets the url.
     *
     * @param \StringType   $title        Title.
     * @param \LanguageCode $languageCode Language code for language specific name value.
     *
     * @return $this|\ManufacturerInterface Same instance for chained method calls.
     */
    public function setUrl(StringType $title, LanguageCode $languageCode);
    
    
    /**
     * Returns all language specific url.
     *
     * @return  array Language specific url value.
     */
    public function getUrls();
}