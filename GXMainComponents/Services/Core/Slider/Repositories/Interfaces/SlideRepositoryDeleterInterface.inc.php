<?php

/* --------------------------------------------------------------
  SlideRepositoryDeleterInterface.inc.php 2016-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SlideRepositoryDeleterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideRepositoryDeleterInterface
{
    /**
     * Deletes a Slide by the given slide ID.
     *
     * @param IdType $slideId
     *
     * @return SlideRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $slideId);
}