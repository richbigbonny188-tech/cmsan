<?php
/*--------------------------------------------------------------------------------------------------
    GXModulesCache.inc.php 2023-04-24
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

class  GXModulesCache
{
	/**
	 * @var array
	 */
	private static $allowedDirectories = [
		'/Classes/',
		'/Shop/',
		'/Admin/',
		'/StyleEdit/',
		'/Build/',
		'/TextPhrases/'
	];
	
	/**
	 * Returns an array of all files in the GXModules directory as absolute file path.
	 *
	 * Notice: Only files are considered, so empty directories are skipped.
	 *
	 * @return array
	 */
	public static function getFiles()
	{
		$dataCache = DataCache::get_instance();
		$cacheKey  = 'GXModules-files';
		
		$files = [];
		if($dataCache->key_exists($cacheKey, true))
		{
			$files = $dataCache->get_data($cacheKey, true);
		}
		else
		{
            $directoryContent = self::rglob(DIR_FS_CATALOG . 'GXModules/*');
            
            foreach ($directoryContent as $file) {
                $filename = basename($file);
                
                if ($filename !== '.' && $filename !== '..' && !is_dir($file)
                    && (stripos($filename, 'GXModule.json') !== false
                        || self::isAllowedDirectory($file))) {
                    $file    = realpath($file);
                    $files[] = str_replace('\\', '/', (string)$file);
                }
            }
            
            $dataCache->set_data($cacheKey, $files, true);
		}
		
		return $files;
	}
    
    
    /**
     * cache only the information related with the theme to avoid looping the whole file array all the time
     */
    public static function getInstalledThemeFiles()
    {
        $dataCache = DataCache::get_instance();
        $cacheKey  = 'Installed-GxModules-ThemeFiles';
        if ($dataCache->key_exists($cacheKey, true)) {
            return $dataCache->get_data($cacheKey, true);
        }
        
        $themeOverloads = [
            'all' => [
                'core'   => ['css' => [], 'html' => [], 'javascript' => []],
                'custom' => ['css' => [], 'html' => [], 'javascript' => []]
            ]
        ];
        $themeDir       = dirname(__DIR__, 3) . '/themes/';
        
        $directories = glob($themeDir . '*', GLOB_ONLYDIR);
        foreach ($directories as $dir) {
            $dirname                  = strtolower(trim(str_replace($themeDir, '', $dir)));
            $themeOverloads[$dirname] = [
                'core'   => ['css' => [], 'html' => [], 'javascript' => []],
                'custom' => ['css' => [], 'html' => [], 'javascript' => []]
            ];
        }
        
        foreach (static::getInstalledModuleFiles() as $file) {
            $block = stripos($file, '/GXModules/Gambio/') !== false ? 'core' : 'custom';
            foreach ($themeOverloads as $theme => $values) {
                $theme_directory      = "/themes/$theme/";
                $javascript_directory = "{$theme_directory}javascript/";
                $css_directory        = "{$theme_directory}css/";
                if (stripos($file, $theme_directory) !== false) {
                    //css
                    if (stripos($file, $css_directory) !== false) {
                        if (substr($file, -4) === '.css' || substr($file, -5) === '.scss') {
                            $themeOverloads[$theme][$block]['css'][] = $file;
                        }
                    //javascript
                    } elseif (($pos = stripos($file, $javascript_directory)) || $pos !== false) {
                            if (substr($file, -3) === '.js') {
                                $name = substr($file, (strlen($file) - $pos - strlen($javascript_directory)) * -1);
                                $page = strtolower(explode(DIRECTORY_SEPARATOR, $name)[0]);
                                if (!isset($themeOverloads[$theme][$block]['javascript'][$page])) {
                                    $themeOverloads[$theme][$block]['javascript'][$page] = [];
                                }
                                $themeOverloads[$theme][$block]['javascript'][$page][] = $file;
                            }
                    //html
                    } elseif (substr($file, -5) === '.html') {
                        $themeOverloads[$theme][$block]['html'][] = $file;
                    }
                }
            }
        }
        
        $dataCache->set_data($cacheKey, $themeOverloads, true);
        
        return $themeOverloads;
    }
	
	
	/**
	 * Returns an array of installed module files in the GXModules directory as absolute file path.
	 *
	 * Notice: Only files are considered, so empty directories are skipped.
	 *
	 * @return array
	 */
	public static function getInstalledModuleFiles()
	{
		static $checkedMatches;
		if($checkedMatches === null)
		{
			$checkedMatches = [];
		}
		
		$allFiles              = self::getFiles();
		$installedModulesFiles = [];
		
		$dataCache = DataCache::get_instance();
		$cacheKey  = 'installed-GXModules-files';
		if($dataCache->key_exists($cacheKey, true))
		{
			return $dataCache->get_data($cacheKey, true);
		}
		elseif(!isset($GLOBALS['db_link']))
		{
			return $allFiles;
		}
		
		foreach($allFiles as $file)
		{
			$moduleIsActive = true;
			preg_match("/GXModules\/([^\/]+\/[^\/]+)/", $file, $matches);
			
			if (!isset($matches[0])) {
				continue;
			}
			
			if(!array_key_exists($matches[0], $checkedMatches) && is_dir(DIR_FS_CATALOG . $matches[0])
			   && file_exists(DIR_FS_CATALOG . $matches[0] . '/GXModule.json'))
			{
				$gxModuleConfig = json_decode(file_get_contents(DIR_FS_CATALOG . $matches[0] . '/GXModule.json'), true);
				if(array_key_exists('forceIncludingFiles', $gxModuleConfig) && $gxModuleConfig['forceIncludingFiles'])
				{
					$moduleIsActive = true;
				}
				else
				{
					$namespace            = 'modules/' . str_replace('/', '', $matches[1]);
					$configurationStorage = MainFactory::create('ConfigurationStorage', $namespace);
					$moduleIsActive       = (bool)$configurationStorage->get('active');
				}
			}
			elseif(array_key_exists($matches[0], $checkedMatches))
			{
				$moduleIsActive = $checkedMatches[$matches[0]];
			}
			
			if($moduleIsActive)
			{
				$installedModulesFiles[] = str_replace('\\', '/', (string)$file);
			}
			
			$checkedMatches[$matches[0]] = $moduleIsActive;
		}
		
		$dataCache->set_data($cacheKey, $installedModulesFiles, true);
		
		# Clear MainFactory cache to remove Overloads that maybe exist in that cache.
		$dataCache->clear_cache('MainFactory-create');
		$dataCache->clear_cache('MainFactory-load');
		$dataCache->clear_cache('MainFactory-loadOrigin');
		$dataCache->clear_cache('Installed-GxModules-ThemeFiles');
		
		return $installedModulesFiles;
	}
	
	
	/**
	 * Checks if the directory contains an allowed directory for GXModules files
	 *
	 * @param $directory
	 *
	 * @return bool
	 */
	private static function isAllowedDirectory($directory)
	{
		$returnValue = false;
		
		$directory = str_replace('\\', '/', $directory);
		
		foreach(self::$allowedDirectories as $allowedDirectory)
		{
			if(stripos($directory,$allowedDirectory) !== false)
            {
                return true;
            }
		}
		
		return $returnValue;
	}
    
    
    /**
     * Scans the given directory recursivly and returns the result as an array. 
     * 
     * @param $pattern
     *
     * @return array
     */
    private static function rglob($pattern)
    {
        $files = glob($pattern);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $dir = str_replace('\\', '/', $dir);
            if(strpos($dir, '/node_modules') === false) {
                $files = array_merge($files, self::rglob($dir . '/' . basename($pattern)));
            }
        }
        
        return $files;
    }
}