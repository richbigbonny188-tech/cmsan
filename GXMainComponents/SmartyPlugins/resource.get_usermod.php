<?php
/* --------------------------------------------------------------
   resource.get_usermod.php 2020-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Smarty_Resource_Get_Usermod
 *
 * Resource overload for the smarty include statements. It enables USERMOD-files for templates.
 *
 * USAGE:
 * {include file="get_usermod:PATH/TO/TPL.FILE"}
 */
class Smarty_Resource_Get_Usermod extends Smarty_Resource_Custom
{
    
    /**
     * Fetch a template and its modification time
     *
     * @param string  $name   template name
     * @param string  $source template source
     * @param integer $mtime  template modification timestamp (epoch)
     *
     * @return void
     */
    protected function fetch($name, &$source, &$mtime)
    {
        $filename = get_usermod($this->_getPath($name));
        
        if (stripos($filename, 'extends:') === 0) {
            $filename  = str_replace('extends:', '', $filename);
            $filenames = explode('|', $filename);
            $source    = '{extends file="' . $filenames[0] . '"}';
            $mtime     = 0;
            unset($filenames[0]);
            foreach ($filenames as $filename) {
                $source .= file_get_contents($filename);
                if ($mtime < filemtime($filename)) {
                    $mtime = filemtime($filename);
                }
            }
        } else {
            $source = file_get_contents($filename);
            $mtime  = filemtime($filename);
        }
    }
    
    
    /**
     * Fetch a template's modification time
     *
     * @note implementing this method is optional. Only implement it if modification times can be accessed faster than
     *       loading the complete template source.
     *
     * @param string $name template name
     *
     * @return integer timestamp (epoch) the template was modified
     */
    protected function fetchTimestamp($name)
    {
        $filename = get_usermod($this->_getPath($name));
        
        if (stripos($filename, 'extends') === 0) {
            return null;
        }
        
        return filemtime($filename);
    }
    
    
    /**
     * @param $name
     *
     * @return string
     */
    protected function _getPath($name)
    {
        static $shopPath;

        if ($shopPath === null) {
            $shopPath = str_replace('\\',
                                    '/',
                                    realpath(DIR_FS_CATALOG)) . '/';
        }

        if (strlen(DIR_FS_CATALOG) > 1) {
            $strpos = strpos($name, DIR_FS_CATALOG);
            if ($strpos !== false) {
                $name = substr($name, 0, $strpos) . $shopPath . substr($name,
                                                                       $strpos + strlen(DIR_FS_CATALOG));
            }
        }
        
        if (strpos($name, $shopPath) === 0 && file_exists($name)) {
            return $name;
        }
        
        $path = $shopPath . $name;
        
        if (defined('APPLICATION_RUN_MODE') && APPLICATION_RUN_MODE === 'backend') {
            $path = $shopPath . 'admin/html/content/' . $name;
        }
        
        if (!file_exists($path)) {
            $gxModulesFiles = GXModulesCache::getInstalledModuleFiles();
            $filePath       = $shopPath . 'GXModules/' . $name;
            
            if (in_array(strtolower($filePath), array_map('strtolower', $gxModulesFiles), true)) {
                $path = $filePath;
            }
        }
        
        return $path;
    }
}
