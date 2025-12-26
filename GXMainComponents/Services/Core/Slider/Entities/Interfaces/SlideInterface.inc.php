<?php

/* --------------------------------------------------------------
   SlideInterface.inc.php 2016-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SlideInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideInterface
{
    /**
     * Set the slide ID for the slide.
     *
     * @param IdType $id Slide ID.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Return the slide ID.
     *
     * @return int Slide ID.
     */
    public function getId();
    
    
    /**
     * Set the language ID for the slide.
     *
     * @param IdType $languageId Language ID.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId);
    
    
    /**
     * Return the language ID.
     *
     * @return int Language ID.
     */
    public function getLanguageId();
    
    
    /**
     * Set the thumbnail for the slide.
     *
     * @param StringType $thumbnail Thumbnail to set.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setThumbnail(StringType $thumbnail);
    
    
    /**
     * Return the thumbnail of the slide.
     *
     * @return string Thumbnail.
     */
    public function getThumbnail();
    
    
    /**
     * Set the title for the slide.
     *
     * @param StringType $title Title for the slide.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setTitle(StringType $title);
    
    
    /**
     * Return the title of the slide.
     *
     * @return string Title.
     */
    public function getTitle();
    
    
    /**
     * Set the alt text for the slide.
     *
     * @param StringType $altText .
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setAltText(StringType $altText);
    
    
    /**
     * Return the alt text of the slide.
     *
     * @return string Alt text of the slide.
     */
    public function getAltText();
    
    
    /**
     * Set the URL for the slide.
     *
     * @param StringType $url URL.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setUrl(StringType $url);
    
    
    /**
     * Return the URL of the slide.
     *
     * @return string URL.
     */
    public function getUrl();
    
    
    /**
     * Set the url target property for the slide.
     *
     * @param StringType $urlTarget URL target e.g. '_blank'.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setUrlTarget(StringType $urlTarget);
    
    
    /**
     * Return the url target property.
     *
     * @return string URL target e.g. '_blank'.
     */
    public function getUrlTarget();
    
    
    /**
     * Set the slide image collection for the slide.
     *
     * @param SlideImageCollection $slideImageCollection
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setSlideImageCollection(SlideImageCollection $slideImageCollection);
    
    
    /**
     * Return the slide image collection of the slide.
     *
     * @return SlideImageCollection Slide image collection.
     */
    public function getSlideImageCollection();
    
    
    /**
     * Adds a slide image to the slide image collection
     *
     * @param SlideImageInterface $slideImage
     *
     * @return SlideInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addSlideImage(SlideImageInterface $slideImage);
    
    
    /**
     * Set the sort order for the slide.
     *
     * @param IntType $sortOrder Slide sort order.
     *
     * @return SlideInterface Same instance for chained method calls.
     */
    public function setSortOrder(IntType $sortOrder);
    
    
    /**
     * Return the slide sort order.
     *
     * @return int sort order.
     */
    public function getSortOrder();
}
