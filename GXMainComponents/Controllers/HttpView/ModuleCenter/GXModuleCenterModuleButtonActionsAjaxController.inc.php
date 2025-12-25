<?php
/* --------------------------------------------------------------
   GXModuleCenterModuleButtonActionsAjaxController.inc.php 2019-05-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXModuleCenterModuleButtonActionsAjaxController
 */
class GXModuleCenterModuleButtonActionsAjaxController extends AdminHttpViewController
{
    /**
     * Handles ajax call of button click and returns json data
     *
     * @return \JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        $return = [];
        
        try {
            $action         = $this->_getPostData('action');
            $controllerName = $this->_getPostData('controller');
            parse_str($this->_getPostData('formData'), $params);
            
            if (class_exists($controllerName)) {
                $controller        = MainFactory::create($controllerName);
                $return['data']    = $controller->{$action}($params);
                $return['success'] = true;
            } else {
                throw new UnexpectedValueException('Controller "' . $controllerName . '" not found');
            }
        } catch (Exception $e) {
            $return['data']    = $e->getMessage();
            $return['success'] = false;
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $return);
    }
    
    
    /**
     * Handles ajax request for modal content if external html is provided
     *
     * @return \HttpControllerResponse
     */
    public function actionModal()
    {
        try {
            $content  = $this->_getPostData('content');
            $template = $this->getTemplateFile($content);
            
            // suppress sending content directly to browser
            ob_start();
            MainFactory::create('AdminLayoutHttpControllerResponse', new NonEmptyStringType('no title'), $template);
            $html = ob_get_clean();
            
            return MainFactory::create('HttpControllerResponse', $html);
        } catch (Exception $e) {
            $return            = [];
            $return['data']    = $e->getMessage();
            $return['success'] = false;
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $return);
    }
}