<?php
/* --------------------------------------------------------------
   AutoUpdaterDataCache.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterDataCache
 */
class AutoUpdaterDataCache extends DataCache
{
	/**
	 * @var string
	 */
	public $v_cache_file_prefix = 'persistent_auto_updater_data_cache-';
	
	
	/**
	 * @return \AutoUpdaterDataCache|\DataCache
	 */
	public static function &get_instance()
	{
		static $s_instance;
		
		if($s_instance === null)
		{
			$s_instance = new AutoUpdaterDataCache();
		}
		
		return $s_instance;
	}
	
	public function get_cache_dir()
	{
		$t_cache_directory = DIR_FS_CATALOG . 'cache/auto_updater/';
		
		if(!file_exists($t_cache_directory) && @mkdir($t_cache_directory, 0777) === false)
		{
			$t_cache_directory = DIR_FS_CATALOG . 'cache/';
		}
		
		return $t_cache_directory;
	}
}