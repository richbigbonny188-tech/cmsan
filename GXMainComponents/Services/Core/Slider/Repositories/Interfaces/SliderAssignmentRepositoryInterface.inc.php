<?php

/* --------------------------------------------------------------
  SliderAssignmentRepositoryInterface.inc.php 2016-11-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SliderAssignmentRepositoryInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SliderAssignmentRepositoryInterface
{
    /**
     * Get the Slider ID for the provided entity type (category, content, product) and entity ID.
     *
     * @param NonEmptyStringType $entityType
     * @param IdType             $entityId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given entity id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForEntityTypeAndEntityId(NonEmptyStringType $entityType, IdType $entityId);
    
    
    /**
     * Inserts a slider assignment into the database.
     *
     * @param IdType             $sliderId
     * @param NonEmptyStringType $entityType
     * @param IdType             $entityId
     *
     * @return SliderAssignmentRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $sliderId, NonEmptyStringType $entityType, IdType $entityId);
    
    
    /**
     * Deletes all slider assignments by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderAssignmentRepositoryInterface Same instance for method chaining.
     */
    public function deleteBySliderId(IdType $sliderId);
    
    
    /**
     * Deletes a slider assignment by the given entity type and entity ID.
     *
     * @param NonEmptyStringType $entityType
     * @param IdType             $entityId
     *
     * @return SliderAssignmentRepositoryInterface Same instance for method chaining.
     */
    public function deleteByEntityTypeAndEntityId(NonEmptyStringType $entityType, IdType $entityId);
}