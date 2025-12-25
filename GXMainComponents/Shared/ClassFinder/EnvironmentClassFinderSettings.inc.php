<?php
/* --------------------------------------------------------------
   EnvironmentClassFinderSettings.inc.php 2020-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class EnvironmentClassFinderSettings
 *
 * @category   System
 * @package    Shared
 * @subpackage ClassFinder
 */
class EnvironmentClassFinderSettings implements ClassFinderSettingsInterface
{
    /**
     * Returns an associative array with all classes that will be filtered by the ClassFinder
     * Array format: [ClassName] => [ClassFullFilePath]
     *
     * @return array
     */
    public function getAvailableClasses()
    {
        $classRegistry   = MainFactory::get_class_registry();
        $allClassesArray = $classRegistry->get_all_data();
        
        return $allClassesArray;
    }
    
    
    /**
     * Returns an numeric array with all directories that will be accepted by the ClassFinder.
     *
     * @return array
     */
    public function getAllowedDirectories()
    {
        $shopPath = realpath(DIR_FS_CATALOG);
        $shopPath = str_replace('\\', '/', $shopPath);

        return [
            $shopPath . '/GXEngine',
            $shopPath . '/GXMainComponents',
            $shopPath . '/GXModules',
        ];
    }
    
    
    /**
     * Returns an numeric array with all directories that will NOT be accepted by the ClassFinder.
     *
     * @return array
     */
    public function getDisallowedDirectories()
    {
        $shopPath = realpath(DIR_FS_CATALOG);
        $shopPath = str_replace('\\', '/', $shopPath);
        
        $disallowedDirsArray = [
            $shopPath . '/GXMainComponents/View/ThemeContentViews',
        ];
        
        $gxModuleFiles = GXModulesCache::getFiles();
        
        foreach ($gxModuleFiles as $file) {
            $pos = stripos($file, '/overloads/');
            
            if ($pos !== false) {
                $dir = substr($file, 0, $pos + strlen('/overloads'));
                
                if (!in_array($dir, $disallowedDirsArray, true)) {
                    $disallowedDirsArray[] = $dir;
                }
            }
        }
        
        return $disallowedDirsArray;
    }
}