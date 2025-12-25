<?php

/* --------------------------------------------------------------
  SlideImageAreaRepositoryReaderInterface.inc.php 2016-10-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageAreaRepositoryReaderInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageAreaRepositoryReaderInterface
{
    /**
     * Returns a SlideImageAreaCollection instance by the given slide image ID.
     *
     * @param IdType $slideImageId ID of the slide image to get.
     *
     * @return SlideImageAreaCollection
     */
    public function getBySlideImageId(IdType $slideImageId);
    
    
    /**
     * Returns a SlideImageArea instance by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $slideImageAreaId);
}