<?php

/* --------------------------------------------------------------
   SliderWriteService.inc.php 2016-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SliderWriteService
 *
 * @category   System
 * @package    Slider
 */
class SliderWriteService implements SliderWriteServiceInterface
{
    /**
     * @var SliderRepositoryInterface
     */
    protected $sliderRepository;
    
    /**
     * @var SlideRepositoryInterface
     */
    protected $slideRepository;
    
    /**
     * @var SlideImageRepositoryInterface
     */
    protected $slideImageRepository;
    
    /**
     * @var SlideImageAreaRepositoryInterface
     */
    protected $slideImageAreaRepository;
    
    /**
     * @var SliderAssignmentRepositoryInterface
     */
    protected $sliderAssignmentRepository;
    
    /**
     * @var SlideImageFileStorage
     */
    protected $slideImageFileStorage;
    
    
    /**
     * SliderWriteService constructor.
     *
     * @param SliderRepositoryInterface           $sliderRepository
     * @param SlideRepositoryInterface            $slideRepository
     * @param SlideImageRepositoryInterface       $slideImageRepository
     * @param SlideImageAreaRepositoryInterface   $slideImageAreaRepository
     * @param SliderAssignmentRepositoryInterface $sliderAssignmentRepository
     * @param SlideImageFileStorage               $slideImageFileStorage
     */
    public function __construct(
        SliderRepositoryInterface $sliderRepository,
        SlideRepositoryInterface $slideRepository,
        SlideImageRepositoryInterface $slideImageRepository,
        SlideImageAreaRepositoryInterface $slideImageAreaRepository,
        SliderAssignmentRepositoryInterface $sliderAssignmentRepository,
        SlideImageFileStorage $slideImageFileStorage
    ) {
        $this->sliderRepository           = $sliderRepository;
        $this->slideRepository            = $slideRepository;
        $this->slideImageRepository       = $slideImageRepository;
        $this->slideImageAreaRepository   = $slideImageAreaRepository;
        $this->sliderAssignmentRepository = $sliderAssignmentRepository;
        $this->slideImageFileStorage      = $slideImageFileStorage;
    }
    
    
    /**
     * Saves a Slider to the database and delegates to child-repositories.
     *
     * @param SliderInterface $slider
     *
     * @return SliderInterface The stored Slider instance.
     */
    public function saveSlider(SliderInterface $slider)
    {
        $this->sliderRepository->store($slider);
        
        return $slider;
    }
    
    
    /**
     * Set the Slider for the start page.
     *
     * @param IdType $sliderId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function setStartPageSlider(IdType $sliderId)
    {
        $this->sliderRepository->setStartPageSlider($sliderId);
        
        return $this;
    }
    
    
    /**
     * Saves a Slide to the database and delegates to child-repositories.
     *
     * @param IdType         $sliderId
     * @param SlideInterface $slide
     *
     * @return SlideInterface The stored Slide instance.
     */
    public function addSlide(IdType $sliderId, SlideInterface $slide)
    {
        $this->slideRepository->store($sliderId, $slide);
        
        return $slide;
    }
    
    
    /**
     * Saves a SlideImage to the database.
     *
     * @param IdType              $slideId
     * @param SlideImageInterface $slideImage
     *
     * @return SlideImageInterface The stored SlideImage instance.
     */
    public function addSlideImage(IdType $slideId, SlideImageInterface $slideImage)
    {
        $this->slideImageRepository->store($slideId, $slideImage);
        
        return $slideImage;
    }
    
    
    /**
     * Saves a SlideImageArea to the database.
     *
     * @param IdType                  $slideImageId
     * @param SlideImageAreaInterface $slideImageArea
     *
     * @return SlideImageAreaInterface The stored SlideImageArea instance.
     */
    public function addSlideImageArea(IdType $slideImageId, SlideImageAreaInterface $slideImageArea)
    {
        $this->slideImageAreaRepository->store($slideImageId, $slideImageArea);
        
        return $slideImageArea;
    }
    
    
    /**
     * Deletes a Slider by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderById(IdType $sliderId)
    {
        $this->sliderRepository->deleteSliderById($sliderId);
        $this->sliderAssignmentRepository->deleteBySliderId($sliderId);
        
        return $this;
    }
    
    
    /**
     * Deletes a Slide by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideById(IdType $slideId)
    {
        $this->slideRepository->deleteSlideById($slideId);
        
        return $this;
    }
    
    
    /**
     * Deletes a SlideImage by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageById(IdType $slideImageId)
    {
        $this->slideImageRepository->deleteSlideImageById($slideImageId);
        
        return $this;
    }
    
    
    /**
     * Deletes a SlideImageArea by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreaById(IdType $slideImageAreaId)
    {
        $this->slideImageAreaRepository->deleteSlideImageAreaById($slideImageAreaId);
        
        return $this;
    }
    
    
    /**
     * Deletes SlideImageAreas by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageAreasBySlideImageId(IdType $slideImageId)
    {
        $this->slideImageAreaRepository->deleteSlideImageAreasBySlideImageId($slideImageId);
        
        return $this;
    }
    
    
    /**
     * Import slide image file.
     *
     * Use this method for importing an uploaded file to the correct slides directory.
     *
     * @param ExistingFile       $sourceFile        The existing file to import.
     * @param FilenameStringType $preferredFilename The preferred filename.
     *
     * @return string New filename.
     *
     * @throws InvalidArgumentException Through SlideImageFileStorage
     */
    public function importSlideImageFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename)
    {
        $newFilename = $this->slideImageFileStorage->importImage($sourceFile, $preferredFilename);
        
        return $newFilename;
    }
    
    
    /**
     * Import slide thumbnail image file.
     *
     * Use this method for importing an uploaded file to the correct thumbnails directory.
     *
     * @param ExistingFile       $sourceFile        The existing file to import.
     * @param FilenameStringType $preferredFilename The preferred filename.
     *
     * @return string New filename.
     *
     * @throws InvalidArgumentException Through SlideImageFileStorage
     */
    public function importSlideThumbnailImageFile(ExistingFile $sourceFile, FilenameStringType $preferredFilename)
    {
        $newFilename = $this->slideImageFileStorage->importThumbnailImage($sourceFile, $preferredFilename);
        
        return $newFilename;
    }
    
    
    /**
     * Rename a slide image file.
     *
     * @param FilenameStringType $oldName The old name of the slide image file.
     * @param FilenameStringType $newName The new name of the slide image file.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     *
     * @throws InvalidArgumentException Through SlideImageFileStorage
     */
    public function renameSlideImageFile(FilenameStringType $oldName, FilenameStringType $newName)
    {
        $this->slideImageFileStorage->renameImage($oldName, $newName);
        
        return $this;
    }
    
    
    /**
     * Rename a slide thumbnail image file.
     *
     * @param FilenameStringType $oldName The old name of the slide image thumbnail file.
     * @param FilenameStringType $newName The new name of the slide image thumbnail file.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     *
     * @throws InvalidArgumentException Through SlideImageFileStorage
     */
    public function renameSlideThumbnailImageFile(FilenameStringType $oldName, FilenameStringType $newName)
    {
        $this->slideImageFileStorage->renameThumbnailImage($oldName, $newName);
        
        return $this;
    }
    
    
    /**
     * Delete a slide image file.
     *
     * @param FilenameStringType $filename The filename of the slide image to be removed.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideImageFile(FilenameStringType $filename)
    {
        $this->slideImageFileStorage->deleteImage($filename);
        $this->slideImageRepository->unsetSlideImageReference($filename);
        
        return $this;
    }
    
    
    /**
     * Delete a slide thumbnail image file.
     *
     * @param FilenameStringType $filename The filename of the slide image thumbnail to be removed.
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSlideThumbnailImageFile(FilenameStringType $filename)
    {
        $this->slideImageFileStorage->deleteThumbnailImage($filename);
        $this->slideRepository->unsetSlideThumbnailReference($filename);
        
        return $this;
    }
    
    
    /**
     * Deletes all slider assignments by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentsBySliderId(IdType $sliderId)
    {
        $this->sliderAssignmentRepository->deleteBySliderId($sliderId);
        
        return $this;
    }
    
    
    /**
     * Deletes a slider assignment by the given category ID.
     *
     * @param IdType $categoryId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentByCategoryId(IdType $categoryId)
    {
        $entityType = new NonEmptyStringType('category');
        
        $this->sliderAssignmentRepository->deleteByEntityTypeAndEntityId($entityType, $categoryId);
        
        return $this;
    }
    
    
    /**
     * Deletes a slider assignment by the given content ID.
     *
     * @param IdType $contentId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentByContentId(IdType $contentId)
    {
        $entityType = new NonEmptyStringType('content');
        
        $this->sliderAssignmentRepository->deleteByEntityTypeAndEntityId($entityType, $contentId);
        
        return $this;
    }
    
    
    /**
     * Deletes a slider assignment by the given product ID.
     *
     * @param IdType $productId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function deleteSliderAssignmentByProductId(IdType $productId)
    {
        $entityType = new NonEmptyStringType('product');
        
        $this->sliderAssignmentRepository->deleteByEntityTypeAndEntityId($entityType, $productId);
        
        return $this;
    }
    
    
    /**
     * Inserts a slider assignment for the given category ID into the database.
     *
     * @param IdType $sliderId
     * @param IdType $categoryId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function saveSliderAssignmentForCategoryId(IdType $sliderId, IdType $categoryId)
    {
        $entityType = new NonEmptyStringType('category');
        
        $this->sliderAssignmentRepository->store($sliderId, $entityType, $categoryId);
        
        return $this;
    }
    
    
    /**
     * Inserts a slider assignment for the given content ID into the database.
     *
     * @param IdType $sliderId
     * @param IdType $contentId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function saveSliderAssignmentForContentId(IdType $sliderId, IdType $contentId)
    {
        $entityType = new NonEmptyStringType('content');
        
        $this->sliderAssignmentRepository->store($sliderId, $entityType, $contentId);
        
        return $this;
    }
    
    
    /**
     * Inserts a slider assignment for the given product ID into the database.
     *
     * @param IdType $sliderId
     * @param IdType $productId
     *
     * @return SliderWriteServiceInterface Same instance for method chaining.
     */
    public function saveSliderAssignmentForProductId(IdType $sliderId, IdType $productId)
    {
        $entityType = new NonEmptyStringType('product');
        
        $this->sliderAssignmentRepository->store($sliderId, $entityType, $productId);
        
        return $this;
    }
}