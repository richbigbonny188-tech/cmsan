<?php

/* --------------------------------------------------------------
   SliderInterface.inc.php 2016-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SliderInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SliderInterface
{
    /**
     * Set the ID for the slider.
     *
     * @param IdType $sliderId Slider ID.
     *
     * @return SliderInterface Same Instance for chained method calls.
     */
    public function setId(IdType $sliderId);
    
    
    /**
     * Return the ID of the slider.
     *
     * @return int Slider ID.
     */
    public function getId();
    
    
    /**
     * Set the name for the slider.
     *
     * @param StringType $name Name for the slider.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setName(StringType $name);
    
    
    /**
     * Return the Name of the slider.
     *
     * @return string Name.
     */
    public function getName();
    
    
    /**
     * Set the speed for the slider.
     *
     * @param DecimalType $speed The speed in seconds for the slider.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setSpeed(DecimalType $speed);
    
    
    /**
     * Return the speed of the slider.
     *
     * @return double Speed.
     */
    public function getSpeed();
    
    
    /**
     * Return true when the slider is displayed on the start page, false otherwise.
     *
     * @return bool
     */
    public function showOnStartPage();
    
    
    /**
     * Show or hides a slider on the start page.
     *
     * @param BoolType $status True when it should be displayed, false otherwise.
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setShowOnStartPage(BoolType $status);
    
    
    /**
     * Set the slide collection for the slider.
     *
     * @param SlideCollection $slideCollection
     *
     * @return SliderInterface Same instance for chained method calls.
     */
    public function setSlideCollection(SlideCollection $slideCollection);
    
    
    /**
     * Return the slide collection of the slider.
     *
     * @return SlideCollection Slide collection.
     */
    public function getSlideCollection();
    
    
    /**
     * Adds a slide to the slide collection
     *
     * @param SlideInterface $slide
     *
     * @return SliderInterface Same instance for chained method calls.
     * @throws InvalidArgumentException
     *
     */
    public function addSlide(SlideInterface $slide);
}