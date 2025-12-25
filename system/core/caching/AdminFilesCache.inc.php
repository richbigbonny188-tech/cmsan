<?php
/* --------------------------------------------------------------
   AdminFilesCache.inc.php 2017-10-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminFilesCache
 */
class AdminFilesCache
{
	/**
	 * Returns an array of all files in the admin/html directory as absolute file path.
	 * 
	 * Notice: Only files are considered, so empty directories are skipped.
	 * 
	 * @return array
	 */
	public static function getFiles()
	{
		$dataCache = DataCache::get_instance();
		$cacheKey  = 'Admin-files';
		
		$files = [];
		if($dataCache->key_exists($cacheKey, true))
		{
			$files = $dataCache->get_data($cacheKey, true);
		}
		else
		{
			$iterator = new RecursiveDirectoryIterator(DIR_FS_CATALOG . 'admin/html');
			
			foreach(new RecursiveIteratorIterator($iterator) as $file)
			{
				if($file->getFilename() !== '.' && $file->getFilename() !== '..')
				{
					$files[] = str_replace('\\', '/', (string)$file);
				}
			}
			
			$dataCache->set_data($cacheKey, $files, true);
		}
		
		return $files;
	}
}