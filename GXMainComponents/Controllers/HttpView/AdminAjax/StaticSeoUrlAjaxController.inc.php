<?php

/* --------------------------------------------------------------
   StaticSeoUrlAjaxController.inc.php 2017-05-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class StaticSeoUrlAjaxController
 *
 * AJAX controller for the staticSeoUrl overview page.
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class StaticSeoUrlAjaxController extends AdminHttpViewController
{
    /**
     * @var StaticSeoUrlWriteService
     */
    protected $staticSeoUrlWriteService;
    
    /**
     * @var StaticSeoUrlReadService
     */
    protected $staticSeoUrlReadService;
    
    
    /**
     * Initializes the Controller
     */
    public function init()
    {
        $this->staticSeoUrlWriteService = StaticGXCoreLoader::getService('StaticSeoUrlWrite');
        $this->staticSeoUrlReadService  = StaticGXCoreLoader::getService('StaticSeoUrlRead');
    }
    
    
    /**
     * Deletes a specific staticSeoUrl.
     *
     * @return HttpControllerResponse
     * @throws AuthenticationException If the user is not the admin.
     */
    public function actionDeleteStaticSeoUrl()
    {
        $staticSeoUrlId = new IdType($this->_getPostData('staticSeoUrlId'));
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        if ($staticSeoUrlId->asInt() > 0) {
            try {
                $this->staticSeoUrlWriteService->deleteStaticSeoUrlById($staticSeoUrlId);
                
                return MainFactory::create('JsonHttpControllerResponse', ['success']);
            } catch (Exception $e) {
                return MainFactory::create('JsonHttpControllerResponse', ['error']);
            }
        }
        
        return MainFactory::create('JsonHttpControllerResponse', ['error']);
    }
    
    
    /**
     * Sets a static seo url state.
     *
     * @throws AuthenticationException If the user is not the admin.
     */
    public function actionActivate()
    {
        $staticSeoUrlId = new IdType($this->_getPostData('staticSeoUrlId'));
        $fieldName      = $this->_getPostData('fieldName');
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        if ($staticSeoUrlId === 0) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
        
        try {
            $staticSeoUrl = $this->staticSeoUrlReadService->getStaticSeoUrlById($staticSeoUrlId);
            switch ($fieldName) {
                case 'sitemap_entry':
                    $staticSeoUrl->setIsInSitemapEntry(new BoolType(true));
                    break;
                case 'robots_disallow_entry':
                    $staticSeoUrl->setIsInRobotsFile(new BoolType(true));
                    break;
            }
            $this->staticSeoUrlWriteService->saveStaticSeoUrl($staticSeoUrl);
            
            return MainFactory::create('JsonHttpControllerResponse', ['success']);
        } catch (Exception $e) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
    }
    
    
    /**
     * Deactivate static seo url state.
     *
     * @throws AuthenticationException If the user is not the admin.
     */
    public function actionDeactivate()
    {
        $staticSeoUrlId = new IdType($this->_getPostData('staticSeoUrlId'));
        $fieldName      = $this->_getPostData('fieldName');
        
        if (!$this->_isAdmin()) {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }
        
        if ($staticSeoUrlId === 0) {
            return MainFactory::create('JsonHttpControllerResponse', ['error']);
        }
        
        try {
            $staticSeoUrl = $this->staticSeoUrlReadService->getStaticSeoUrlById($staticSeoUrlId);
            switch ($fieldName) {
                case 'sitemap_entry':
                    $staticSeoUrl->setIsInSitemapEntry(new BoolType(false));
                    break;
                case 'robots_disallow_entry':
                    $staticSeoUrl->setIsInRobotsFile(new BoolType(false));
                    break;
            }
            $this->staticSeoUrlWriteService->saveStaticSeoUrl($staticSeoUrl);
            
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