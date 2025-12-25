<?php

/* --------------------------------------------------------------
   SlideImagePathsSettingsInterface.inc.php 2016-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SlideImagePathsSettingsInterface
 *
 * @category   System
 * @package    Slider
 * @subpackage Interfaces
 */
interface SlideImagePathsSettingsInterface
{
    /**
     * Returns the path to the slide images directory.
     *
     * @return string The path to the slide images directory.
     */
    public function getSlideImagesDirPath();
    
    
    /**
     * Returns the path to the slide thumbnail images directory.
     *
     * @return string The path to the slide thumbnail images directory.
     */
    public function getSlideThumbnailImagesDirPath();
}