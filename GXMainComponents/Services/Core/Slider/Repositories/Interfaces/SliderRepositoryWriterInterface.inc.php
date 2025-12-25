<?php

/* --------------------------------------------------------------
  SliderRepositoryWriterInterface.inc.php 2016-08-10
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SliderRepositoryWriterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SliderRepositoryWriterInterface
{
    /**
     * Inserts a slider to the database.
     *
     * @param SliderInterface $slider
     *
     * @return int ID of inserted slider or the given slider ID if the slider had an ID already.
     */
    public function store(SliderInterface $slider);
    
    
    /**
     * Set the Slider for the start page.
     *
     * @param IdType $sliderId
     *
     * @return SliderRepositoryWriterInterface Same instance for method chaining.
     */
    public function setStartPageSlider(IdType $sliderId);
}