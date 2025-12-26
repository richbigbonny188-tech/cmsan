<?php

/* --------------------------------------------------------------
   SliderReadService.inc.php 2016-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SliderReadService
 *
 * @category   System
 * @package    Slider
 */
class SliderReadService implements SliderReadServiceInterface
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
     * SliderReadService constructor.
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
     * Returns a SliderCollection with all existing Slider objects.
     *
     * @return SliderCollection
     */
    public function getAllSlider()
    {
        $sliderCollection = $this->sliderRepository->getAll();
        
        return $sliderCollection;
    }
    
    
    /**
     * Returns a Slider instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderInterface
     */
    public function getSliderById(IdType $sliderId)
    {
        $slider = $this->sliderRepository->getById($sliderId);
        
        return $slider;
    }
    
    
    /**
     * Returns a Slide instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideInterface
     */
    public function getSlideById(IdType $slideId)
    {
        $slide = $this->slideRepository->getById($slideId);
        
        return $slide;
    }
    
    
    /**
     * Returns a SlideImage instance by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageInterface
     */
    public function getSlideImageById(IdType $slideImageId)
    {
        $slideImage = $this->slideImageRepository->getById($slideImageId);
        
        return $slideImage;
    }
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SlideCollection
     */
    public function getSlidesBySliderId(IdType $sliderId)
    {
        $slideCollection = $this->slideRepository->getBySliderId($sliderId);
        
        return $slideCollection;
    }
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageCollection
     */
    public function getSlideImagesBySlideId(IdType $slideId)
    {
        $slideImageCollection = $this->slideImageRepository->getBySlideId($slideId);
        
        return $slideImageCollection;
    }
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID and language ID.
     *
     * @param IdType $sliderId
     * @param IdType $languageId
     *
     * @return SlideCollection
     */
    public function getSlidesBySliderIdAndLanguageId(IdType $sliderId, IdType $languageId)
    {
        $slideCollection = $this->slideRepository->getBySliderIdAndLanguageId($sliderId, $languageId);
        
        return $slideCollection;
    }
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID and language ID.
     *
     * @param IdType $slideId
     * @param IdType $languageId
     *
     * @return SlideImageCollection
     */
    public function getSlideImagesBySlideIdAndLanguageId(IdType $slideId, IdType $languageId)
    {
        $slideImageCollection = $this->slideImageRepository->getBySlideIdAndLanguageId($slideId, $languageId);
        
        return $slideImageCollection;
    }
    
    
    /**
     * Returns a SlideImageArea instance by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaInterface
     */
    public function getSlideImageAreaById(IdType $slideImageAreaId)
    {
        $slideImageArea = $this->slideImageAreaRepository->getById($slideImageAreaId);
        
        return $slideImageArea;
    }
    
    
    /**
     * Returns a SlideImageAreaCollection with all existing SlideImageArea objects by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaCollection
     */
    public function getSlideImageAreasBySlideImageId(IdType $slideImageId)
    {
        $slideImageAreaCollection = $this->slideImageAreaRepository->getBySlideImageId($slideImageId);
        
        return $slideImageAreaCollection;
    }
    
    
    /**
     * Returns all slide images from slide image file storage.
     *
     * @return array Slide images.
     */
    public function getAllSlideImagesFromStorage()
    {
        return $this->slideImageFileStorage->getImages();
    }
    
    
    /**
     * Returns all slide thumbnail images from slide image file storage.
     *
     * @return array Slide thumbnail images.
     */
    public function getAllSlideThumbnailImagesFromStorage()
    {
        return $this->slideImageFileStorage->getThumbnailImages();
    }
    
    
    /**
     * Check if an image file is used by another slide image entry.
     *
     * @param FilenameStringType $filename Slide image filename.
     * @param IdType             $slideImageId
     *
     * @return bool
     */
    public function isSlideImageFileUsed(FilenameStringType $filename, IdType $slideImageId)
    {
        return $this->slideImageRepository->isSlideImageFileUsed($filename, $slideImageId);
    }
    
    
    /**
     * Check if an image file is used by another slide entry.
     *
     * @param FilenameStringType $filename Slide thumbnail image filename.
     * @param IdType             $slideId
     *
     * @return bool
     */
    public function isSlideThumbnailImageFileUsed(FilenameStringType $filename, IdType $slideId)
    {
        return $this->slideRepository->isSlideThumbnailImageFileUsed($filename, $slideId);
    }
    
    
    /**
     * Get the Slider for the start page.
     *
     * @return SliderInterface|null Returns the start page slider instance or null if no record was found.
     */
    public function getStartPageSlider()
    {
        return $this->sliderRepository->getStartPageSlider();
    }
    
    
    /**
     * Get the Slider ID for the provided category ID.
     *
     * @param IdType $categoryId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given category id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForCategoryId(IdType $categoryId)
    {
        $entityType = new NonEmptyStringType('category');
        
        return $this->sliderAssignmentRepository->findAssignedSliderIdForEntityTypeAndEntityId($entityType,
                                                                                               $categoryId);
    }
    
    
    /**
     * Get the Slider ID for the provided content ID.
     *
     * @param IdType $contentId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given content id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForContentId(IdType $contentId)
    {
        $entityType = new NonEmptyStringType('content');
        
        return $this->sliderAssignmentRepository->findAssignedSliderIdForEntityTypeAndEntityId($entityType, $contentId);
    }
    
    
    /**
     * Get the Slider ID for the provided product ID.
     *
     * @param IdType $productId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given product id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForProductId(IdType $productId)
    {
        $entityType = new NonEmptyStringType('product');
        
        return $this->sliderAssignmentRepository->findAssignedSliderIdForEntityTypeAndEntityId($entityType, $productId);
    }
}