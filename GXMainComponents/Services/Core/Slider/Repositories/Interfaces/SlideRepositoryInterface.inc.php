<?php

/* --------------------------------------------------------------
  SlideRepositoryInterface.inc.php 2016-09-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideRepositoryInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideRepositoryInterface
{
    /**
     * Returns a SlideCollection instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SlideCollection
     */
    public function getBySliderId(IdType $sliderId);
    
    
    /**
     * Returns a Slide instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideInterface
     */
    public function getById(IdType $slideId);
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID and language ID.
     *
     * @param IdType $sliderId
     * @param IdType $languageId
     *
     * @return SlideCollection
     */
    public function getBySliderIdAndLanguageId(IdType $sliderId, IdType $languageId);
    
    
    /**
     * Stores a Slide to the database.
     *
     * @param IdType         $sliderId
     * @param SlideInterface $slide
     *
     * @return SlideRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $sliderId, SlideInterface $slide);
    
    
    /**
     * Deletes a Slide by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideById(IdType $slideId);
    
    
    /**
     * Check if an image file is used by another slide entry.
     *
     * @param FilenameStringType $filename Slide thumbnail image filename.
     * @param IdType             $slideId
     *
     * @return bool
     */
    public function isSlideThumbnailImageFileUsed(FilenameStringType $filename, IdType $slideId);
    
    
    /**
     * Unset the thumbnail filename references in other slide thumbnail entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideRepositoryInterface Same instance for method chaining.
     */
    public function unsetSlideThumbnailReference(FilenameStringType $filename);
}