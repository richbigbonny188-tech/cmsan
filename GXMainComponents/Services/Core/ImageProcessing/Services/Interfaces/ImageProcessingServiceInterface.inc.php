<?php
/* --------------------------------------------------------------
   ImageProcessingServiceInterface.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ImageProcessingServiceInterface
 *
 * @category   System
 * @package    ImageProcessing
 * @subpackage Services
 */
interface ImageProcessingServiceInterface
{
    /**
     * Creates popup, info, thumbnail and gallery images of given original image file if they are missing.
     *
     * @param \ExistingFile $originalImageFilePath
     *
     * @return bool if image processed successfully.
     */
    public function createMissingImages(ExistingFile $originalImageFilePath);
    
    
    /**
     * Creates popup, info, thumbnail and gallery images of given original image.
     *
     * @param \ExistingFile $originalImageFilePath
     *
     * @return bool if image processed successfully.
     */
    public function recreateAllImages(ExistingFile $originalImageFilePath);
    
    
    /**
     * Sets trigger for recreation of all image files.
     *
     * @return void
     */
    public static function setTrigger();
    
    
    /**
     * Removes the trigger for recreation of all image files.
     *
     * @return void
     */
    public static function removeTrigger();
    
    
    /**
     * Checks if trrigger for recreation of all images is set.
     *
     * @return boolean
     */
    public static function isTriggerSet();
}