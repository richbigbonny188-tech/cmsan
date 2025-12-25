<?php

/* --------------------------------------------------------------
  SliderRepositoryReaderInterface.inc.php 2016-08-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SliderRepositoryReaderInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SliderRepositoryReaderInterface
{
    /**
     * Returns a Slider instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderInterface
     */
    public function getById(IdType $sliderId);
    
    
    /**
     * Returns a SliderCollection with all existing Slider objects.
     *
     * @return SliderCollection
     */
    public function getAll();
    
    
    /**
     * Get the Slider for the start page.
     *
     * @return SliderInterface|null Returns the start page slider instance or null if no record was found.
     */
    public function getStartPageSlider();
}