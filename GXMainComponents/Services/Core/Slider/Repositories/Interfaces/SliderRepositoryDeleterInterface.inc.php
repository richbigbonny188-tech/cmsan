<?php

/* --------------------------------------------------------------
  SliderRepositoryDeleterInterface.inc.php 2016-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface SliderRepositoryDeleterInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SliderRepositoryDeleterInterface
{
    /**
     * Deletes a Slider by the given slider ID.
     *
     * @param IdType $sliderId
     *
     * @return SliderRepositoryDeleterInterface Same instance for method chaining.
     */
    public function deleteById(IdType $sliderId);
}