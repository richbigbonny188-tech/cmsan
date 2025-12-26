<?php

/* --------------------------------------------------------------
  SlideImageRepositoryReaderInterface.inc.php 2016-10-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageRepositoryReaderInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageRepositoryReaderInterface
{
    /**
     * Returns a SlideImageCollection for the given Slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideImageCollection
     */
    public function getBySlideId(IdType $slideId);
    
    
    /**
     * Returns a SlideImage for the given SlideImage ID.
     *
     * @param IdType $slideImageId
     *
     * @return SlideImageInterface
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $slideImageId);
    
    
    /**
     * Returns a SlideImageCollection with all existing SlideImage objects by the given slide ID and language ID.
     *
     * @param IdType $slideId
     * @param IdType $languageId
     *
     * @return SlideImageCollection
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getBySlideIdAndLanguageId(IdType $slideId, IdType $languageId);
    
    
    /**
     * Check if an image file is used by another slide image entry.
     *
     * @param FilenameStringType $filename Slide image filename.
     * @param IdType             $slideImageId
     *
     * @return bool
     */
    public function isSlideImageFileUsed(FilenameStringType $filename, IdType $slideImageId);
}