<?php
/* --------------------------------------------------------------
   FileSystemDetailsReader.php 2021-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Reader;

use Gambio\AdminFeed\Services\ShopInformation\Settings;

/**
 * Class FileSystemDetailsReader
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Reader
 */
class FileSystemDetailsReader
{
    /**
     * @var Settings
     */
    private $settings;
    
    
    /**
     * @param Settings $settings
     *
     * @return self
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }
    
    
    /**
     * @return array
     */
    public function getUserMods()
    {
        $usermods = array_merge($this->searchForUsermods($this->settings->getBaseDirectory() . 'admin/html/'),
                                $this->searchForUsermods($this->settings->getBaseDirectory() . 'admin/javascript/'),
                                $this->searchForUsermods($this->settings->getBaseDirectory() . 'gm/javascript/'));
        
        $usermods = array_map(function ($usermod) {
            return substr($usermod, strlen($this->settings->getBaseDirectory()));
        },
            $usermods);
        
        return $usermods;
    }
    
    
    /**
     * @return array
     */
    public function getGxModules()
    {
        $gxModules = [];
        
        $moduleDevelopers = (array)glob($this->settings->getBaseDirectory() . 'GXModules/*', GLOB_ONLYDIR);
        foreach ($moduleDevelopers as $moduleDeveloper) {
            $modules = (array)glob($this->settings->getBaseDirectory() . 'GXModules/' . basename($moduleDeveloper)
                                   . '/*',
                                   GLOB_ONLYDIR);
            foreach ($modules as $module) {
                $gxModules[] = basename($moduleDeveloper) . '/' . basename($module);
            }
        }
        
        return $gxModules;
    }
    
    
    /**
     * @return array
     */
    public function getDangerousTools()
    {
        $dangerousToolsPattern = [
            '*MyAdmin*',
            '*NewAdmin*',
            '*phpm*',
            '*msd*',
            '*dumper*',
            'mybackup*',
            '*admin_access*',
            '*adminer*',
            '__*'
        ];
        
        $dangerousTools = [];
        foreach ($dangerousToolsPattern as $pattern) {
            $dangerousTools = array_merge((array)glob($this->settings->getBaseDirectory() . $pattern), $dangerousTools);
        }
        
        $dangerousTools = array_map(function ($dangerousTool) {
            return substr($dangerousTool, strlen($this->settings->getBaseDirectory()));
        },
            $dangerousTools);
        
        return $dangerousTools;
    }
    
    
    /**
     * @return bool
     */
    public function doesGlobalUsermodDirectoryExist()
    {
        return is_dir($this->settings->getBaseDirectory() . 'USERMOD');
    }
    
    
    /**
     * @return array
     */
    public function getReceiptFiles()
    {
        $receiptFiles = (array)glob($this->settings->getBaseDirectory() . 'version_info/*.php');
        $receiptFiles = array_map(function ($receiptFile) {
            return basename($receiptFile);
        },
            $receiptFiles);
        
        return $receiptFiles;
    }
    
    
    /**
     * @return bool
     */
    public function doesUpmDirectoryExist()
    {
        return is_dir($this->settings->getBaseDirectory() . 'upm');
    }
    
    
    /**
     * Searches for user mods in the given directory and returns them
     *
     * @param string $directory .
     *
     * @return array
     */
    private function searchForUsermods($directory)
    {
        $foundUsermods = [];
        
        if (file_exists($directory)) {
            $foundUsermods  = (array)glob($directory . '*-USERMOD.*');
            $subDirectories = (array)glob($directory . '*', GLOB_ONLYDIR | GLOB_NOSORT);
            
            if (count($subDirectories) > 0) {
                foreach ($subDirectories as $subDirectory) {
                    $foundUsermods = array_merge($foundUsermods, $this->searchForUsermods($subDirectory . '/'));
                }
            }
        }
        
        return $foundUsermods;
    }
}