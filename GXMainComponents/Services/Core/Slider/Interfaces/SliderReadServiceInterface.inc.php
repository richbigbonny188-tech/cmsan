<?php

/* --------------------------------------------------------------
   SliderReadServiceInterface.inc.php 2016-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SliderReadServiceInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
Interface SliderReadServiceInterface
{
    /**
     * Returns a SliderCollection with all existing Slider objects.
     *
     * @return SliderCollection
     */
    public function getAllSlider();
    
    
    /**
     * Returns a Slider instance by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderInterface
     */
    public function getSliderById(IdType $sliderId);
    
    
    /**
     * Returns a Slide instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideInterface
     */
    public function getSlideById(IdType $slideId);
    
    
    /**
     * Returns a SlideImage instance by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageInterface
     */
    public function getSlideImageById(IdType $slideImageId);
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SlideCollection
     */
    public function getSlidesBySliderId(IdType $sliderId);
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageCollection
     */
    public function getSlideImagesBySlideId(IdType $slideId);
    
    
    /**
     * Returns a SlideCollection with all existing Slide objects by the given slider ID and language ID.
     *
     * @param IdType $sliderId
     * @param IdType $languageId
     *
     * @return SlideCollection
     */
    public function getSlidesBySliderIdAndLanguageId(IdType $sliderId, IdType $languageId);
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID and language ID.
     *
     * @param IdType $slideId
     * @param IdType $languageId
     *
     * @return SlideImageCollection
     */
    public function getSlideImagesBySlideIdAndLanguageId(IdType $slideId, IdType $languageId);
    
    
    /**
     * Returns a SlideImageArea instance by the given slide image area ID.
     *
     * @param IdType $slideImageAreaId
     *
     * @return SlideImageAreaInterface
     */
    public function getSlideImageAreaById(IdType $slideImageAreaId);
    
    
    /**
     * Returns a SlideImageAreaCollection with all existing SlideImageArea objects by the given slide image ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageAreaCollection
     */
    public function getSlideImageAreasBySlideImageId(IdType $slideImageId);
    
    
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
     * Check if an image file is used by another slide entry.
     *
     * @param FilenameStringType $filename Slide thumbnail image filename.
     * @param IdType             $slideId
     *
     * @return bool
     */
    public function isSlideThumbnailImageFileUsed(FilenameStringType $filename, IdType $slideId);
    
    
    /**
     * Get the Slider for the start page.
     *
     * @return SliderInterface|null Returns the start page slider instance or null if no record was found.
     */
    public function getStartPageSlider();
    
    
    /**
     * Get the Slider ID for the provided category ID.
     *
     * @param IdType $categoryId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given category id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForCategoryId(IdType $categoryId);
    
    
    /**
     * Get the Slider ID for the provided content ID.
     *
     * @param IdType $contentId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given content id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForContentId(IdType $contentId);
    
    
    /**
     * Get the Slider ID for the provided product ID.
     *
     * @param IdType $productId
     *
     * @return int|null Returns the ID of the slider which is assigned for the given product id or null if no record
     *                  was found.
     */
    public function findAssignedSliderIdForProductId(IdType $productId);
}