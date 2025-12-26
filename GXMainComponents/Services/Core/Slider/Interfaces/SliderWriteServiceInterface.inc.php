<?php

/* --------------------------------------------------------------
   SliderWriteServiceInterface.inc.php 2016-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SliderWriteServiceInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
Interface SliderWriteServiceInterface
{
    /**
     * Saves a Slider to the database and delegates to child-repositories.
     *
     * @param SliderInterface $slider
     *
     * @return SliderInterface The stored Slider instance.
     */
    public function saveSlider(SliderInterface $slider);
    
    
    /**
     * Set the Slider for the start page.
     *
     * @param IdType $sliderId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function setStartPageSlider(IdType $sliderId);
    
    
    /**
     * Saves a Slide to the database and delegates to child-repositories.
     *
     * @param IdType         $sliderId
     * @param SlideInterface $slide
     *
     * @return SlideInterface The stored Slide instance.
     */
    public function addSlide(IdType $sliderId, SlideInterface $slide);
    
    
    /**
     * Saves a SlideImage to the database.
     *
     * @param IdType              $slideId
     * @param SlideImageInterface $slideImage
     *
     * @return SlideImageInterface The stored SlideImage instance.
     */
    public function addSlideImage(IdType $slideId, SlideImageInterface $slideImage);
    
    
    /**
     * Saves a SlideAreaImage to the database.
     *
     * @param IdType                  $slideImageId
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return SlideImageAreaInterface The stored SlideImageArea instance.
     */
    public function addSlideImageArea(IdType $slideImageId, SlideImageAreaInterface $slideImageArea);
    
    
    /**
     * Deletes a Slider by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderById(IdType $sliderId);
    
    
    /**
     * Deletes a Slide by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideById(IdType $slideId);
    
    
    /**
     * Deletes a SlideImage by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageById(IdType $slideImageId);
    
    
    /**
     * Deletes a SlideImageArea by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreaById(IdType $slideImageAreaId);
    
    
    /**
     * Deletes SlideImageAreas by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreasBySlideImageId(IdType $slideImageId);
    
    
    /**
     * Import slide image file.
     *
     * Use this method for importing an uploaded file to the correct slides directory.
     *
     * @param ExistingFile       $sourceFile        The existing file to import.
     * @param FilenameStringType $preferredFilename The preferred filename.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function importSlideImageFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename);
    
    
    /**
     * Import slide image thumbnail file.
     *
     * Use this method for importing an uploaded file to the correct thumbnails directory.
     *
     * @param ExistingFile       $sourceFile        The existing file to import.
     * @param FilenameStringType $preferredFilename The preferred filename.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function importSlideThumbnailImageFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename);
    
    
    /**
     * Rename a slide image file.
     *
     * @param FilenameStringType $oldName The old name of the slide image file.
     * @param FilenameStringType $newName The new name of the slide image file.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function renameSlideImageFile(FilenameStringType $oldName, FilenameStringType $newName);
    
    
    /**
     * Rename a slide image thumbnail file.
     *
     * @param FilenameStringType $oldName The old name of the slide image thumbnail file.
     * @param FilenameStringType $newName The new name of the slide image thumbnail file.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function renameSlideThumbnailImageFile(FilenameStringType $oldName, FilenameStringType $newName);
    
    
    /**
     * Delete a slide image file.
     *
     * @param FilenameStringType $filename The filename of the slide image to be removed.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageFile(FilenameStringType $filename);
    
    
    /**
     * Delete a slide image thumbnail file.
     *
     * @param FilenameStringType $filename The filename of the slide image thumbnail to be removed.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideThumbnailImageFile(FilenameStringType $filename);
    
    
    /**
     * Deletes all slider assignments by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentsBySliderId(IdType $sliderId);
    
    
    /**
     * Deletes a slider assignment by the given category ID.
     *
     * @param IdType $categoryId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentByCategoryId(IdType $categoryId);
    
    
    /**
     * Deletes a slider assignment by the given content ID.
     *
     * @param IdType $contentId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentByContentId(IdType $contentId);
    
    
    /**
     * Deletes a slider assignment by the given product ID.
     *
     * @param IdType $productId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentByProductId(IdType $productId);
    
    
    /**
     * Inserts a slider assignment for the given category ID into the database.
     *
     * @param IdType $sliderId
     * @param IdType $categoryId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function saveSliderAssignmentForCategoryId(IdType $sliderId, IdType $categoryId);
    
    
    /**
     * Inserts a slider assignment for the given content ID into the database.
     *
     * @param IdType $sliderId
     * @param IdType $contentId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function saveSliderAssignmentForContentId(IdType $sliderId, IdType $contentId);
    
    
    /**
     * Inserts a slider assignment for the given product ID into the database.
     *
     * @param IdType $sliderId
     * @param IdType $productId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function saveSliderAssignmentForProductId(IdType $sliderId, IdType $productId);
}