<?php

/* --------------------------------------------------------------
  SlideImageRepositoryDeleterInterface.inc.php 2016-08-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageRepositoryDeleterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageRepositoryDeleterInterface
{
    /**
     * Deletes a SlideImage by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $slideImageId);
    
    
    /**
     * Deletes all SlideImage by the given Slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteBySlideId(IdType $slideId);
}