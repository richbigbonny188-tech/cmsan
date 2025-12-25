<?php

/* --------------------------------------------------------------
  SlideImageAreaRepositoryInterface.inc.php 2016-10-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageAreaRepositoryInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageAreaRepositoryInterface
{
    /**
     * Returns a SlideImageAreaCollection instance by the given slide image ID.
     *
     * @param IdType $slideImageId
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
     */
    public function getById(IdType $slideImageAreaId);
    
    
    /**
     * Stores a SlideImageArea to the database.
     *
     * @param IdType                  $slideImageId
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return SlideImageAreaRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $slideImageId, SlideImageAreaInterface $slideImageArea);
    
    
    /**
     * Deletes a SlideImageArea by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreaById(IdType $slideImageAreaId);
    
    
    /**
     * Deletes SlideImageAreas by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreasBySlideImageId(IdType $slideImageId);
}