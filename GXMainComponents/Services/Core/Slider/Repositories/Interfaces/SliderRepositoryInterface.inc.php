<?php

/* --------------------------------------------------------------
  SliderRepositoryInterface.inc.php 2016-08-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SliderRepositoryInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SliderRepositoryInterface
{
    /**
     * Returns a SliderCollection with all existing Slider objects.
     *
     * @return SliderCollection
     */
    public function getAll();
    
    
    /**
     * Returns a Slider instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderInterface
     */
    public function getById(IdType $sliderId);
    
    
    /**
     * Stores a Slider to the database.
     *
     * @param SliderInterface $slider
     *
     * @return SliderRepositoryInterface Same instance for method chaining.
     */
    public function store(SliderInterface $slider);
    
    
    /**
     * Set the Slider for the start page.
     *
     * @param IdType $sliderId
     *
     * @return SliderRepositoryInterface Same instance for method chaining.
     */
    public function setStartPageSlider(IdType $sliderId);
    
    
    /**
     * Deletes a Slider by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderRepositoryInterface Same instance for method chaining.
     */
    public function deleteSliderById(IdType $sliderId);
    
    
    /**
     * Get the Slider for the start page.
     *
     * @return SliderInterface|null Returns the start page slider instance or null if no record was found.
     */
    public function getStartPageSlider();
}