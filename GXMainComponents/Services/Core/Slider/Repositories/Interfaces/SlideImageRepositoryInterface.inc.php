<?php

/* --------------------------------------------------------------
  SlideImageRepositoryInterface.inc.php 2016-09-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageRepositoryInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageRepositoryInterface
{
    /**
     * Returns a SlideImageCollection instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageCollection
     */
    public function getBySlideId(IdType $slideId);
    
    
    /**
     * Returns a SlideImage instance by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageInterface
     */
    public function getById(IdType $slideImageId);
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID and language ID.
     *
     * @param IdType $slideId
     * @param IdType $languageId
     *
     * @return SlideImageCollection
     */
    public function getBySlideIdAndLanguageId(IdType $slideId, IdType $languageId);
    
    
    /**
     * Stores a SlideImage to the database.
     *
     * @param IdType              $slideId
     * @param SlideImageInterface $slideImage
     *
     * @return SlideImageRepositoryInterface Same instance for method chaining.
     */
    public function store(IdType $slideId, SlideImageInterface $slideImage);
    
    
    /**
     * Deletes a SlideImage by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageRepositoryInterface Same instance for method chaining.
     */
    public function deleteSlideImageById(IdType $slideImageId);
    
    
    /**
     * Check if an image file is used by another slide image entry.
     *
     * @param FilenameStringType $filename Slide image filename.
     * @param IdType             $slideImageId
     *
     * @return bool
     */
    public function isSlideImageFileUsed(FilenameStringType $filename, IdType $slideImageId);
    
    
    /**
     * Unset the image filename references in other slide image entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideImageRepositoryInterface Same instance for method chaining.
     */
    public function unsetSlideImageReference(FilenameStringType $filename);
}