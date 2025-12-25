<?php

/* --------------------------------------------------------------
  SlideImageAreaRepositoryDeleterInterface.inc.php 2016-10-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageAreaRepositoryDeleterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageAreaRepositoryDeleterInterface
{
    /**
     * Deletes a SlideImageArea by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $slideImageAreaId);
    
    
    /**
     * Deletes all SlideImageAreas by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteBySlideImageId(IdType $slideImageId);
}