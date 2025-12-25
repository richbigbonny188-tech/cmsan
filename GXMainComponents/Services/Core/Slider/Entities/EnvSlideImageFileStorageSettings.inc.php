<?php

/* --------------------------------------------------------------
   EnvSlideImageFileStorageSettings.inc.php 2016-11-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EnvSlideImageFileStorageSettings
 *
 * @category   System
 * @package    Slider
 * @subpackage Entities
 *
 * @codeCoverageIgnore
 */
class EnvSlideImageFileStorageSettings implements SlideImagePathsSettingsInterface
{
    /**
     * Path to slider images (relative path from '/images').
     *
     * @var string
     */
    protected $pathToSliderImagesDir = 'slider_images';
    
    /**
     * Path to slider thumbnail images (relative path from '/images').
     *
     * @var string
     */
    protected $pathToSliderThumbnailImagesDir = 'slider_images/thumbnails';
    
    
    /**
     * Returns the path to the slide images directory.
     *
     * @return string The path to the slide images directory.
     * @throws UnknownEnvironmentException if folder is not found.
     *
     */
    public function getSlideImagesDirPath()
    {
        if (defined('DIR_FS_CATALOG_IMAGES')) {
            return DIR_FS_CATALOG_IMAGES . $this->pathToSliderImagesDir;
        } elseif (defined('DIR_WS_IMAGES')) {
            return DIR_FS_CATALOG . DIR_WS_IMAGES . $this->pathToSliderImagesDir;
        }
        
        throw new UnknownEnvironmentException();
    }
    
    
    /**
     * Returns the path to the slide thumbnail images directory.
     *
     * @return string The path to the slide thumbnail images directory.
     * @throws UnknownEnvironmentException if folder is not found.
     *
     */
    public function getSlideThumbnailImagesDirPath()
    {
        if (defined('DIR_FS_CATALOG_IMAGES')) {
            return DIR_FS_CATALOG_IMAGES . $this->pathToSliderThumbnailImagesDir;
        } elseif (defined('DIR_WS_IMAGES')) {
            return DIR_FS_CATALOG . DIR_WS_IMAGES . $this->pathToSliderThumbnailImagesDir;
        }
        
        throw new UnknownEnvironmentException();
    }
}