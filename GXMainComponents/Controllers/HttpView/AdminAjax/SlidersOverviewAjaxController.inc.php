<?php

/* --------------------------------------------------------------
   SlidersOverviewAjaxController.inc.php 2016-12-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class SlidersOverviewAjaxController
 *
 * AJAX controller for the teaser slider overview page.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class SlidersOverviewAjaxController extends AdminHttpViewController
{
    /**
     * @var SliderWriteService
     */
    protected $sliderWriteService;
    
    /**
     * @var SliderReadService
     */
    protected $sliderReadService;
    
    
    /**
     * Initializes the Controller
     */
    public function init()
    {
        $this->sliderWriteService = StaticGXCoreLoader::getService('SliderWrite');
        $this->sliderReadService  = StaticGXCoreLoader::getService('SliderRead');
    }
    
    
    /**
     * Deletes a specific slider.
     *
     * @return HttpControllerResponse
     * @throws AuthenticationException If the user is not the admin.
     */
    public function actionDeleteSlider()
    {
        $sliderId = new IdType($this->_getPostData('sliderId'));
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        if ($sliderId->asInt() > 0) {
            try {
                $this->sliderWriteService->deleteSliderById($sliderId);
                
                return MainFactory::create('JsonHttpControllerResponse', ['success']);
            } catch (Exception $e) {
                return MainFactory::create('JsonHttpControllerResponse', ['error']);
            }
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['error']);
    }
    
    
    /**
     * Sets a specific slider as the start page slider.
     *
     * @throws AuthenticationException If the user is not the admin.
     */
    public function actionSetStartPageSlider()
    {
        $sliderId = (int)$this->_getPostData('sliderId');
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        if ($sliderId === 0) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
        
        try {
            $this->sliderWriteService->setStartPageSlider(new IdType($sliderId));
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $e) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Deactivate the start page slider.
     *
     * @throws AuthenticationException If the user is not the admin.
     */
    public function actionDeactivateStartPageSlider()
    {
        $sliderId = (int)$this->_getPostData('sliderId');
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        if ($sliderId === 0) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
        
        try {
            $slider = $this->sliderReadService->getSliderById(new IdType($sliderId));
            $slider->setShowOnStartPage(new BoolType(false));
            $this->sliderWriteService->saveSlider($slider);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $e) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Check if the customer is the admin.
     *
     * @return bool Is the customer the admin?
     */
    protected function _isAdmin()
    {
        try {
            $this->validateCurrentAdminStatus();
            
            return true;
        } catch (LogicException $exception) {
            return false;
        }
    }
    
    
    /**
     * Returns the expected $_POST value by the given key name.
     * This method is the object oriented layer for $_POST[$keyName].
     *
     * @param string $keyName Expected key of post parameter.
     *
     * @return string|null Either the expected value or null, of not found.
     */
    protected function _getPostData($keyName)
    {
        if (!array_key_exists($keyName, $this->postDataArray)) {
            return null;
        }
        
        return $this->postDataArray[$keyName];
    }
}