<?php

/* --------------------------------------------------------------
   SlideImageInterface.inc.php 2016-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SlideImageInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageInterface
{
    /**
     * Set the slide image ID.
     *
     * @param IdType $id Slide image ID.
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Return the ID of the slide image.
     *
     * @return int Slide image ID.
     */
    public function getId();
    
    
    /**
     * Set the language ID for the slide image.
     *
     * @param IdType $languageId Language ID.
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setLanguageId(IdType $languageId);
    
    
    /**
     * Return the language ID of the slide image.
     *
     * @return int Slide image language ID.
     */
    public function getLanguageId();
    
    
    /**
     * Set the breakpoint for the slide image.
     *
     * @param NonEmptyStringType $breakpoint
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setBreakpoint(NonEmptyStringType $breakpoint);
    
    
    /**
     * Return the breakpoint of the slide image.
     *
     * @return string Breakpoint of the slide image.
     */
    public function getBreakpoint();
    
    
    /**
     * Set the image for the image slider.
     *
     * @param StringType $image The image to set.
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setImage(StringType $image);
    
    
    /**
     * Return the image of the image slider.
     *
     * @return string Image of the image slider.
     */
    public function getImage();
    
    
    /**
     * Set the slide image area collection for the slide image.
     *
     * @param SlideImageAreaCollection $slideImageAreaCollection
     *
     * @return SlideImageInterface Same instance for chained method calls.
     */
    public function setSlideImageAreaCollection(SlideImageAreaCollection $slideImageAreaCollection);
    
    
    /**
     * Return the slide image area collection of the slide image.
     *
     * @return SlideImageAreaCollection Slide image area collection.
     */
    public function getSlideImageAreaCollection();
    
    
    /**
     * Adds a slide image area to the slide image area collection
     *
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return SlideImageInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addSlideImageArea(SlideImageAreaInterface $slideImageArea);
}