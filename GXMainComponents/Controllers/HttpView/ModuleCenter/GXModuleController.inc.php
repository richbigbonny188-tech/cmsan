<?php
/* --------------------------------------------------------------
   GXModuleController.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXModuleController
 *
 * Extend from this class to get easy access to the configuration
 * of the your GXModule you don't need the GXModuleConfigurationStorage
 */
class GXModuleController
{
    /**
     * Init the configuration for module
     */
    public function __construct()
    {
        $module       = $this->_getGXModuleName();
        $this->config = MainFactory::create('GXModuleConfigurationStorage', $module);
    }
    
    
    /**
     * Helper function to get module namespace from directory structure
     *
     * @return string
     */
    protected function _getGXModuleName()
    {
        $dir = dirname((new ReflectionClass(static::class))->getFileName());
        preg_match("/GXModules\/([^\/]+\/[^\/]+)/", $dir, $matches);
        
        return $gxModuleNamespace = str_replace('/', '', $matches[1]);
    }
}