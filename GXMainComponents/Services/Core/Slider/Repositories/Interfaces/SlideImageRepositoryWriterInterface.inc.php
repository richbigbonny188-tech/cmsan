<?php

/* --------------------------------------------------------------
  SlideImageRepositoryWriterInterface.inc.php 2016-08-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideImageRepositoryWriterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImageRepositoryWriterInterface
{
    /**
     * Inserts a slide image to the database.
     *
     * @param IdType              $slideId
     * @param SlideImageInterface $slideImage
     *
     * @return int ID of inserted slide or the given slide ID if the slide had an ID already.
     */
    public function store(IdType $slideId, SlideImageInterface $slideImage);
    
    
    /**
     * Unset the image filename references in other slide image entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideImageRepositoryWriterInterface Same instance for method chaining.
     */
    public function unsetSlideImageReference(FilenameStringType $filename);
}