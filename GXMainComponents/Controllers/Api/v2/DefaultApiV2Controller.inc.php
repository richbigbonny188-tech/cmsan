<?php
/* --------------------------------------------------------------
   DefaultApiV2Controller.inc.php 2019-11-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class DefaultApiV2Controller
 *
 * The default APIv2 controller will be triggered when client consumers hit the "api.php/v2"
 * URI and it will return information about the API.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class DefaultApiV2Controller extends HttpApiV2Controller
{
    public function get()
    {
        $this->_returnHelpResponse();
    }
    
    
    public function post()
    {
        $this->_returnHelpResponse();
    }
    
    
    public function put()
    {
        $this->_returnHelpResponse();
    }
    
    
    public function patch()
    {
        $this->_returnHelpResponse();
    }
    
    
    public function delete()
    {
        $this->_returnHelpResponse();
    }
    
    
    public function head()
    {
        $this->_returnHelpResponse();
    }
    
    
    public function options()
    {
        $this->_returnHelpResponse();
    }
    
    
    /**
     * @var string[] controller files that are not linked in the default help response
     */
    protected const UNLINKED_CONTROLLERS = [
        'AdditionalProductFieldsApiV2Controller.inc.php' // help controller of the products endpoint, would print incorrect url in help response
    ];
    
    protected function _returnHelpResponse()
    {
        $apiUrl = $this->getRootUri();
        
        $iterator  = new IteratorIterator(new DirectoryIterator(DIR_FS_CATALOG
                                                                . 'GXMainComponents/Controllers/Api/v2'));
        $resources = [];
        
        foreach ($iterator as $item) {
            /** @var DirectoryIterator $item */
            $controllerFile = $item->getFilename();
            if ($controllerFile !== 'AbstractImagesApiV2Controller.inc.php'
                && $controllerFile !== 'DefaultApiV2Controller.inc.php'
                && $controllerFile !== 'ApiV2Authenticator.inc.php'
                && $controllerFile !== 'legacy'
                && $controllerFile !== '.'
                && $controllerFile !== '..'
                && in_array($controllerFile, self::UNLINKED_CONTROLLERS) === false
            ) {
                $resources[] = $this->_camelCaseToUnderscore(str_replace('ApiV2Controller.inc.php',
                                                                         '',
                                                                         $item->getFilename()));
            }
        }
        sort($resources);
        
        $response = [];
        foreach ($resources as $resource) {
            $response[$resource] = $apiUrl . $resource;
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Converts a camel case string to underscored.
     *
     * @param string $input Camel case string that should get underscores.
     *
     * @return string $input with underscores instead of camel cases.
     */
    protected function _camelCaseToUnderscore($input)
    {
        $pattern = '!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!';
        
        preg_match_all($pattern, $input, $matches);
        $return = $matches[0];
        foreach ($return as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        
        return implode('_', $return);
    }
}