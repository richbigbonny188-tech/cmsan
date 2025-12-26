<?php
/* --------------------------------------------------------------
   SlidersDetailsAjaxController.inc.php 2016-12-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class SlidersDetailsAjaxController
 *
 * AJAX controller the teaser slider edit page.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class SlidersDetailsAjaxController extends AdminHttpViewController
{
    /**
     * @var SliderReadService
     */
    protected $sliderReadService;
    
    /**
     * @var SliderWriteService
     */
    protected $sliderWriteService;
    
    
    /**
     * Initialize Controller
     */
    public function init()
    {
        $this->sliderReadService  = StaticGXCoreLoader::getService('SliderRead');
        $this->sliderWriteService = StaticGXCoreLoader::getService('SliderWrite');
    }
    
    
    /**
     * Determines whether an image file is already in use.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionCheckImageUsage()
    {
        $filename    = new FilenameStringType($this->_getQueryParameter('filename'));
        $isThumbnail = $this->_getQueryParameter('is_thumbnail') === "true";
        
        if ($isThumbnail) {
            $slideId = new IdType($this->_getQueryParameter('slide_id'));
            $isUsed  = $this->sliderReadService->isSlideThumbnailImageFileUsed($filename, $slideId);
        } else {
            $slideImageId = new IdType($this->_getQueryParameter('slide_image_id'));
            $isUsed       = $this->sliderReadService->isSlideImageFileUsed($filename, $slideImageId);
        }
        
        $response = ['isUsed' => (bool)$isUsed];
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Performs the storing process of uploaded images file that have been sent via AJAX.
     *
     * @return JsonHttpControllerResponse Success response.
     * @throws InvalidArgumentException On invalid arguments.
     */
    public function actionUploadImages()
    {
        if (!isset($_FILES) || count($_FILES) === 0) {
            return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
        }
        
        foreach ($_FILES as $key => $values) {
            for ($i = 0, $imagesCount = count($values['tmp_name']); $i < $imagesCount; $i++) {
                $existingFile      = new ExistingFile(new NonEmptyStringType($values['tmp_name'][$i]));
                $preferredFilename = new FilenameStringType($values['name'][$i]);
                
                if ($key === 'thumbnail_file_upload') {
                    $this->sliderWriteService->importSlideThumbnailImageFile($existingFile, $preferredFilename);
                } else {
                    $this->sliderWriteService->importSlideImageFile($existingFile, $preferredFilename);
                }
            }
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
    
    
    /**
     * Deletes slider images.
     *
     * @return JsonHttpControllerResponse
     */
    public function actionDeleteImages()
    {
        if ($this->_getPostData('file_names') !== null && count($this->_getPostData('file_names'))) {
            $fileNames = array_unique($this->_getPostData('file_names'));
        } else {
            $fileNames = [];
        }
        
        if ($this->_getPostData('thumbnail_names') !== null && count($this->_getPostData('thumbnail_names'))) {
            $thumbnailNames = array_unique($this->_getPostData('thumbnail_names'));
        } else {
            $thumbnailNames = [];
        }
        
        foreach ($fileNames as $fileName) {
            $this->sliderWriteService->deleteSlideImageFile(new FilenameStringType($fileName));
        }
        
        foreach ($thumbnailNames as $thumbnailName) {
            $this->sliderWriteService->deleteSlideThumbnailImageFile(new FilenameStringType($thumbnailName));
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['success' => true]);
    }
}