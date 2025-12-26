<?php

/* --------------------------------------------------------------
  SlideRepositoryReaderInterface.inc.php 2016-09-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideRepositoryReaderInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideRepositoryReaderInterface
{
    /**
     * Returns a SlideCollection for the given Slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SlideCollection All slides found by the slider ID as a SlideCollection.
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getBySliderId(IdType $sliderId);
    
    
    /**
     * Returns a Slide instance by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
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
     * Check if an image file is used by another slide entry.
     *
     * @param FilenameStringType $filename Slide thumbnail image filename.
     * @param IdType             $slideId
     *
     * @return bool
     */
    public function isSlideThumbnailImageFileUsed(FilenameStringType $filename, IdType $slideId);
}