<?php
/* --------------------------------------------------------------
   get_usermod.inc.php 2021-05-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function get_usermod($p_file_path, $p_debug_output = false)
{
	if(!is_string($p_file_path) || strpos($p_file_path, 'string:') !== false || strpos($p_file_path, 'eval:') !== false)
	{
		return $p_file_path;
	}

    $shopPath = realpath(DIR_FS_CATALOG);
    $shopPath = str_replace('\\', '/', $shopPath) . '/';

    if (strlen(DIR_FS_CATALOG) > 1) {
        $strpos = strpos($p_file_path, DIR_FS_CATALOG);
        if ($strpos !== false) {
            $p_file_path = substr($p_file_path, 0, $strpos) . $shopPath . substr($p_file_path,
                                                                                 $strpos + strlen(DIR_FS_CATALOG));
        }
    }
	
	$t_file_path = trim($p_file_path);
	
	$dataCache = DataCache::get_instance();
	$usermods  = [];
	if($dataCache->key_exists('usermods', true))
	{
		$usermods = $dataCache->get_data('usermods', true);
		
		if(isset($usermods[$p_file_path]))
		{
			if(strpos($usermods[$p_file_path], 'extends:') === 0)
			{
				$filePaths    = explode('|', $usermods[$p_file_path]);
				$filePaths[0] = str_replace('extends:', '', $filePaths[0]);
				$fileExists   = 1;
				
				foreach($filePaths as &$filePath)
				{
					$fileExists &= file_exists($filePath);
				}
				
				if($fileExists)
				{
					return $usermods[$p_file_path];
				}
			}
			elseif(file_exists($usermods[$p_file_path]))
			{
				return $usermods[$p_file_path];
			}
		}
	}
	
	// Check for snippets etc. in GXModules, that needs to be extended to the given files
	$extenderTemplateFiles = [];
	$snippetFiles          = explode('|', $t_file_path);
	$snippetFiles[0]       = str_replace('extends:', '', $snippetFiles[0]);
	$gxModulesFiles        = GXModulesCache::getInstalledModuleFiles();
	$directoryName         = 'Themes';
	$addedExtenderFiles    = [];
	foreach($snippetFiles as $snippetFile)
	{
		if(substr($snippetFile, -5) !== '.html')
		{
			continue;
		}
		
		if(stripos($snippetFile, $shopPath . $directoryName . '/') === 0)
		{
			foreach(array_reverse(StaticGXCoreLoader::getThemeControl()->getCurrentThemeHierarchy()) as $currentTheme)
			{
				foreach($gxModulesFiles as $file)
				{
					if(stripos($file, '/' . $directoryName . '/') === false || substr($file, -5) !== '.html'
					   || in_array($file, $addedExtenderFiles))
					{
						continue;
					}
					
					$relativeFilePath = preg_replace('/.*\/' . $directoryName . '\/[^\/].+?\/(.*)/i', '$1', $file);
					$fileTemlate      = preg_replace('/.*\/' . $directoryName . '\/([^\/].+?)\/.*/i', '$1', $file);
					$snippetFileShort = substr($snippetFile,
					                           strlen($shopPath . $directoryName . '/' . $currentTheme . '/'));
					
					if($snippetFile !== $file
					   && strcasecmp($relativeFilePath, $snippetFileShort) === 0
					   && (strcasecmp($fileTemlate, 'All') === 0 || strcasecmp($fileTemlate, $currentTheme) === 0))
					{
						$extenderTemplateFiles[] = $file;
						$addedExtenderFiles[]    = $file;
					}
				}
			}
		}
		elseif(stripos($snippetFile, $shopPath . 'admin/html/') === 0)
		{
			foreach($gxModulesFiles as $file)
			{
				if(stripos($file, '/Admin/Html/') === false || substr($file, -5) !== '.html')
				{
					continue;
				}
				
				$relativeFilePath = preg_replace('/.*\/(Admin\/Html\/.*)/i', '$1', $file);
				$snippetFileShort = substr($snippetFile, strlen($shopPath));
				
				if($snippetFile !== $file && stripos($snippetFileShort, $relativeFilePath) !== false)
				{
					$extenderTemplateFiles[] = $file;
				}
			}
		}
	}
	
	if(strpos($t_file_path, 'extends:') === 0)
	{
		$filePaths    = explode('|', $t_file_path);
		$filePaths[0] = str_replace('extends:', '', $filePaths[0]);
		
		foreach($filePaths as &$filePath)
		{
			$filePath = get_usermod($filePath);
		}
		
		$filePaths[0] = 'extends:' . $filePaths[0];
		
		$t_file_path = implode('|', array_merge($filePaths, $extenderTemplateFiles));
		
		$usermods[$p_file_path] = $t_file_path;
		$dataCache->set_data('usermods', $usermods, true);
		
		return $t_file_path;
	}
	
	$t_coo_cached_directory = new CachedDirectory('');
	
	# extract filename
	$t_file_name = basename($t_file_path);
	
	# extend filename
	$t_file_parts    = explode('.', $t_file_name);
	$t_file_parts[0] .= '-USERMOD';
	
	# rebuild filename
	$t_file_name = implode('.', $t_file_parts);
	
	# rebuild possible filepath to usermod-version
	$t_usermod_file_path = dirname($t_file_path) . '/' . $t_file_name;
	
	# check if -USERMOD-file exists
	if($t_coo_cached_directory->file_exists($t_usermod_file_path))
	{
		$t_file_path = $t_usermod_file_path;
	}
	elseif(strpos($t_usermod_file_path, StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath()))
	{
		$gxModulesFiles          = GXModulesCache::getInstalledModuleFiles();
		$gxModuleUsermodFilePath = str_replace($shopPath, '', $t_usermod_file_path);
		krsort($gxModulesFiles);
		
		foreach($gxModulesFiles as $filePath)
		{
			if(stripos($filePath, $gxModuleUsermodFilePath) !== false && file_exists($filePath))
			{
				$t_file_path = $filePath;
				break;
			}
		}
	}
	
	if(count($extenderTemplateFiles) > 0)
	{
		$t_file_path = 'extends:' . $t_file_path . '|' . implode('|', $extenderTemplateFiles);
	}
	
	$usermods[$p_file_path] = $t_file_path;
	$dataCache->set_data('usermods', $usermods, true);
	
	if($p_debug_output)
	{
		echo "input: $p_file_path <br/>\n";
		echo "tried: $t_usermod_file_path <br/>\n";
		echo "result: $t_file_path <br/>\n";
	}
	
	return $t_file_path;
}
