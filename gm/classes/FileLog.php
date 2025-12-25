<?php
/*--------------------------------------------------------------------
 FileLog.php 2022-05-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

require_once __DIR__ . '/../../GambioCore/Application/env.php';

class FileLog
{
	public $v_log_active   = true;
	public $v_file_base    = '';
	public $v_file_suffix  = '';
	public $v_max_filesize = 0;
	
	
	/**
	 * FileLog constructor.
	 *
	 * @param string $p_file_base
	 * @param bool   $p_log_active
	 */
	public function __construct($p_file_base, $p_log_active = true)
	{
		$this->FileLog($p_file_base, $p_log_active);
	}
	
	
	public function FileLog($p_file_base, $p_log_active = true)
	{
		$this->v_file_base = $p_file_base;
		$this->v_file_suffix = $this->get_secure_token();
		
		if(defined('SQL_LOG_MAX_FILESIZE') && (double)SQL_LOG_MAX_FILESIZE > 0)
		{
			$this->v_max_filesize = (double)SQL_LOG_MAX_FILESIZE * 1024 * 1024;
		}
		else
		{
			$this->v_max_filesize = 1 * 1024 * 1024;
		}
  	}
	
	public function get_file_path()
  	{
  		$t_path = 'logfiles/'.$this->v_file_base.'-'.$this->v_file_suffix.'.log';
  		return $t_path;
  	}
	
	public function write($p_string)
	{
		if($this->v_log_active == false)
		{
			# log deactivated. cancel write.
			return true;
		}

		if((file_exists(DIR_FS_CATALOG . $this->get_file_path()) == false && 
				is_writeable(DIR_FS_CATALOG . 'logfiles/') == false)
			|| 
			(file_exists(DIR_FS_CATALOG . $this->get_file_path()) == true && 
				is_writeable(DIR_FS_CATALOG . $this->get_file_path()) == false))
		{
			return false;
		}
        
        $path = DIR_FS_CATALOG . $this->get_file_path();
        $isAllowed  = is_file($path) && is_readable($path);
        
		if($isAllowed && (@filesize($path) >= $this->v_max_filesize && is_writeable(DIR_FS_CATALOG . 'logfiles/') == true))
		{
			$t_file_content = file_get_contents(DIR_FS_CATALOG . $this->get_file_path());			
			$t_gz_file_content = gzencode($t_file_content, 9);
			$t_gz_filename = $this->v_file_base . '-' . $this->v_file_suffix . '_' . date('Ymd_His') . '.log.gz';
			$fp = fopen(DIR_FS_CATALOG . 'logfiles/' . $t_gz_filename, 'w+');
			fwrite($fp, $t_gz_file_content);
			fclose($fp);
			unlink(DIR_FS_CATALOG . $this->get_file_path());
		}
		
		$fp = fopen(DIR_FS_CATALOG . $this->get_file_path(), 'a+');
		fwrite($fp, $p_string);
		fclose($fp);

		return true;
	}
	
	public function set_log_active($p_status)
	{
		$this->v_log_active = $p_status;
	}

	public static function get_secure_token()
	{
	    return Gambio\Core\Application\env('APP_SECURITY_TOKEN');
	}
}