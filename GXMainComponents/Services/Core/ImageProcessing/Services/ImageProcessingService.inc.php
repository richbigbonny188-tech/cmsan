<?php
/* --------------------------------------------------------------
   ImageProcessingService.inc.php 2018-08-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ImageProcessingService
 *
 * @category   System
 * @package    ImageProcessing
 * @subpackage Services
 */
class ImageProcessingService implements ImageProcessingServiceInterface
{
    /**
     * ImageProcessingService constructor.
     */
    public function __construct()
    {
        
        if (!defined('_VALID_XTC')) {
            define('_VALID_XTC', true);
        }
        
        if (!defined('DIR_FS_CATALOG_IMAGES')) {
            define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
        }
        
        if (!defined('DIR_FS_CATALOG_ORIGINAL_IMAGES')) {
            define('DIR_FS_CATALOG_ORIGINAL_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/original_images/');
        }
        
        if (!defined('DIR_FS_CATALOG_THUMBNAIL_IMAGES')) {
            define('DIR_FS_CATALOG_THUMBNAIL_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/thumbnail_images/');
        }
        
        if (!defined('DIR_FS_CATALOG_INFO_IMAGES')) {
            define('DIR_FS_CATALOG_INFO_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/info_images/');
        }
        
        if (!defined('DIR_FS_CATALOG_POPUP_IMAGES')) {
            define('DIR_FS_CATALOG_POPUP_IMAGES', DIR_FS_CATALOG_IMAGES . 'product_images/popup_images/');
        }
        
        require_once DIR_FS_CATALOG . 'admin/includes/classes/image_manipulator_GD2.php';
    }
    
    
    /**
     * Creates popup, info, thumbnail and gallery images of given original image file if they are missing.
     *
     * @param \ExistingFile $originalImageFilePath
     *
     * @return bool if image processed successfully.
     */
    public function createMissingImages(ExistingFile $originalImageFilePath)
    {
        return $this->createImages(basename($originalImageFilePath->getFilePath()), true);
    }
    
    
    /**
     * Creates popup, info, thumbnail and gallery images of given original image.
     *
     * @param \ExistingFile $originalImageFilePath
     *
     * @return bool if image processed successfully.
     */
    public function recreateAllImages(ExistingFile $originalImageFilePath)
    {
        return $this->createImages(basename($originalImageFilePath->getFilePath()));
    }
    
    
    /**
     * Sets trigger for recreation of all image files.
     *
     * @return void
     */
    public static function setTrigger()
    {
        if (!file_exists(DIR_FS_CATALOG . 'cache/cronjobs')) {
            mkdir(DIR_FS_CATALOG . 'cache/cronjobs');
        }
        
        file_put_contents(DIR_FS_CATALOG . 'cache/cronjobs/trigger_recreation_of_all_images', '');
    }
    
    
    /**
     * Removes the trigger for recreation of all image files.
     *
     * @return void
     */
    public static function removeTrigger()
    {
        if (file_exists(DIR_FS_CATALOG . 'cache/cronjobs/trigger_recreation_of_all_images')) {
            unlink(DIR_FS_CATALOG . 'cache/cronjobs/trigger_recreation_of_all_images');
        }
    }
    
    
    /**
     * Checks if trrigger for recreation of all images is set.
     *
     * @return boolean
     */
    public static function isTriggerSet()
    {
        return file_exists(DIR_FS_CATALOG . 'cache/cronjobs/trigger_recreation_of_all_images');
    }
    
    
    /**
     * Creates images in different sizes of given filename.
     *
     * @param string $imageFilename      filename without path
     * @param bool   $skipExistingImages only create missing images
     *
     * @return bool Returns true, if no error occurred.
     */
    protected function createImages($imageFilename, $skipExistingImages = false)
    {
        // do not rename this variables, because included files need them
        $products_image_name  = $imageFilename;
        $image_error          = false;
        $skip_existing_images = $skipExistingImages;
        
        @include(DIR_FS_CATALOG . 'admin/includes/product_popup_images.php');
        @include(DIR_FS_CATALOG . 'admin/includes/product_info_images.php');
        @include(DIR_FS_CATALOG . 'admin/includes/product_thumbnail_images.php');
        @include(DIR_FS_CATALOG . 'admin/includes/product_gallery_images.php');
        
        return !$image_error;
    }
}