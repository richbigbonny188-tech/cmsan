<?php
/* --------------------------------------------------------------
  StyleEdit4PermissionHelper.php 2019-10-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class StyleEdit4PermissionHelper
 */
class StyleEdit4PermissionHelper
{
    /***
     * These directories and every theme directory will be checked
     */
    protected const READ_AND_WRITEABLE_DIRECTORIES = [
        'public/theme',
        'public/tmp',
    ];
    
    /**
     * @var array
     */
    protected $directoriesWithInsufficientPermissions = [];
    
    /**
     * @return bool
     */
    public function checkPermissions(): bool
    {
        $shopRoot = dirname(__DIR__, 6);
        
        foreach (self::READ_AND_WRITEABLE_DIRECTORIES as $directory) {
            
            $path = $shopRoot . DIRECTORY_SEPARATOR . $directory;
            
            if (is_dir($path) && (is_writable($path) && is_readable($path)) === false) {
                
                $this->directoriesWithInsufficientPermissions []= $path;
            }
        }
        
        $themesDirectory = $shopRoot . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR;
        
        foreach (new DirectoryIterator($themesDirectory) as $directory) {
            
            if (!$directory->isDot() && $directory->isDir()
                && (!$directory->isReadable() || !$directory->isWritable())) {
    
                $this->directoriesWithInsufficientPermissions []= $directory->getRealPath();
            }
        }
        
        return count($this->directoriesWithInsufficientPermissions) === 0;
    }
    
    
    /**
     * @return array
     */
    public function directoriesWithInsufficientPermissions(): array
    {
        return $this->directoriesWithInsufficientPermissions;
    }
}