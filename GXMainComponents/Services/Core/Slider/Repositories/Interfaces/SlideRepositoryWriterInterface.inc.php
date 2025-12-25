<?php

/* --------------------------------------------------------------
  SlideRepositoryWriterInterface.inc.php 2016-08-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideRepositoryWriterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideRepositoryWriterInterface
{
    /**
     * Inserts a slide to the database.
     *
     * @param IdType         $sliderId
     * @param SlideInterface $slide
     *
     * @return int ID of inserted slide or the given slide ID if the slide had an ID already.
     */
    public function store(IdType $sliderId, SlideInterface $slide);
    
    
    /**
     * Unset the thumbnail filename references in other slide thumbnail entry by the given filename.
     *
     * @param FilenameStringType $filename Slide image filename.
     *
     * @return SlideRepositoryWriterInterface Same instance for method chaining.
     */
    public function unsetSlideThumbnailReference(FilenameStringType $filename);
}