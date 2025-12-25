<?php
/* --------------------------------------------------------------
   SFTPManager.inc.php 2022-11-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SFTPManager
 */
class SFTPManager implements FileManagerInterface
{
	protected $error      = '';
	protected $sftp;
	protected $data_array = array();
	protected $isDirCache = array();
	protected $additionalChmodAll = array(
		'/admin/includes/magnalister',
	);
	
	public function __construct($p_connect = true, $p_host, $p_user, $p_password, $p_port = 22)
	{
		if($p_connect == true)
		{
			$this->connect($p_host, $p_user, $p_password, $p_port);
		}
	}


	public function connect($p_host, $p_user, $p_password, $p_port)
	{
		$this->sftp = new phpseclib\Net\SFTP($p_host, $p_port);
		
		if(@!$this->sftp->login($p_user, $p_password))
		{
			$this->error .= ERROR_SFTP_CONNECTION;
		}
	}
	
	
	protected function _isdir($p_dir)
	{
		if(isset($this->isDirCache[$p_dir]))
		{
			return $this->isDirCache[$p_dir];
		}
		$this->isDirCache[$p_dir] = false;
		
		if($this->sftp->chdir($p_dir))
		{
			$this->sftp->chdir('..');
			$this->isDirCache[$p_dir] = true;
		}
		
		return $this->isDirCache[$p_dir];
	}
	
	
	public function get_directories($p_dir, $p_no_parent_dirs = false)
	{
		$t_parent = '';
		if($p_dir !== '/')
		{
			$t_parent = $p_dir;
		}
		$t_parent .= '/';
		
		$t_list_array = $this->_get_dir_content($p_dir);
		
		$t_final_list = array();
		
		$list_count = count($t_list_array);
		for($i = 0; $i < $list_count; $i++)
		{
			if(substr($t_list_array[$i], -3) === '/.')
			{
				continue;
			}
			
			if(strpos($t_list_array[$i], '/') !== 0)
			{
				$t_list_array[$i] = $t_parent . $t_list_array[$i];
			}
			
			if(substr($t_list_array[$i], -3) !== '/..' && substr($t_list_array[$i], -2) !== '/.' && $this->_isdir($t_list_array[$i]))
			{
				if($p_no_parent_dirs)
				{
					$t_dir = substr(strrchr($t_list_array[$i], '/'), 1);
				}
				else
				{
					$t_dir = $t_list_array[$i];
				}
				
				if(strlen($t_dir) > 1 && substr($t_dir, 0, 2) === '//')
				{
					$t_dir = substr($t_dir, 1);
				}
				
				$t_final_list[] = $t_dir;
			}
		}
		
		sort($t_final_list);
		
		return $t_final_list;
	}
	
	
	protected function _get_dir_content($p_dir)
	{
		$dirContent = $this->sftp->nlist($p_dir);
		
		if($dirContent === false)
		{
			$dirContent = array();
		}
		
		return $dirContent;
	}
	
	
	public function quit()
	{
		if($this->sftp !== null)
		{
			$this->sftp->disconnect();
		}
	}
	
	
	public function is_shop($p_dir)
	{
		$t_found_shop = false;
		
		if($this->sftp !== null)
		{
			$t_includes_dir_content_array = $this->_get_dir_content($p_dir . '/includes');
			$file_count = count($t_includes_dir_content_array);
			for($i = 0; $i < $file_count; $i++)
			{
				if(strpos($t_includes_dir_content_array[$i], 'application_top_main.php') !== false)
				{
					$t_found_shop = true;
					break;
				}
			}
		}
		
		return $t_found_shop;
	}
	
	
	public function chmod_777($p_dir)
	{
		$fp = fopen(DIR_FS_CATALOG . 'version_info/lists/chmod.txt', 'r');
		
		while($t_line = fgets($fp, 1024)) {
			$t_line = trim($t_line);
			if(strlen($t_line) > 0)
			{
				$t_line = str_replace('\\', '/', $t_line);
				if(substr($t_line, 0, 1) != '/')
				{
					$t_line = '/' . $t_line;
				}
				$this->sftp->chmod(0777, $p_dir . $t_line);
			}
		}
		fclose($fp);
		
		$fp = fopen(DIR_FS_CATALOG . 'version_info/lists/chmod_all.txt', 'r');
		
		while($t_line = fgets($fp, 1024))
		{
			$this->_recursive_chmod_all($p_dir, $t_line);
		}
		
		foreach($this->additionalChmodAll as $path)
		{
			$this->_recursive_chmod_all($p_dir, $path);
		}
		
		fclose($fp);
		
		$fp = fopen(DIR_FS_CATALOG . 'version_info/lists/chmod_all_directories.txt', 'r');
		
		while($t_line = fgets($fp, 1024)) {
			$t_line = trim($t_line);
			if(strlen($t_line) > 0)
			{
				$t_line = str_replace('\\', '/', $t_line);
				if(substr($t_line, 0, 1) != '/')
				{
					$t_line = '/' . $t_line;
				}
				
				if(substr($p_dir, -1) == '/')
				{
					$t_line = substr($p_dir, 0, -1) . $t_line;
				}
				else
				{
					$t_line = $p_dir . $t_line;
				}
				
				$this->data_array = array();
				$this->_recursive_ftpn_list($t_line, true, false);
				
				$this->sftp->chmod(0777, trim($t_line));
				for($i = 0; $i < count($this->data_array); $i++)
				{
					$this->sftp->chmod(0777, trim($this->data_array[$i]));
				}
			}
		}
		
		$_SESSION['FTP_PATH'] = $p_dir;
		
		fclose($fp);
	}
	
	
	protected function _recursive_ftpn_list($p_dir, $p_directories = true, $p_files = true, $p_exclude = array('.', '..'))
	{
		$t_parent = '/';
		
		if(strpos($p_dir, '/') !== 0)
		{
			$p_dir = $t_parent . $p_dir;
		}
		
		$t_list_array = $this->_get_dir_content($p_dir);
		foreach($t_list_array as $t_file)
		{
			if(strpos($t_file, '/') !== 0)
			{
				$t_file = $p_dir . '/' . $t_file;
			}
			
			if(strrchr($t_file, '/') !== false)
			{
				$t_name = substr(strrchr($t_file, '/'), 1);
			}
			else
			{
				$t_name = $t_file;
			}
			if(!in_array($t_name, $p_exclude))
			{
				if($this->_isdir($t_file) && $p_directories)
				{
					$this->data_array[] = $t_file;
					$this->_recursive_ftpn_list($t_file, $p_directories, $p_files, $p_exclude);
				}
				
				if(!$this->_isdir($t_file) && $p_files)
				{
					$this->data_array[] = $t_file;
				}
			}
		}
		
		return $this->data_array;
	}
	
	
	public function chmod_444($p_dir)
	{
		@chmod(DIR_FS_CATALOG . 'admin/includes/configure.org.php', 0444);
		@chmod(DIR_FS_CATALOG . 'admin/includes/configure.php', 0444);
		@chmod(DIR_FS_CATALOG . 'includes/configure.org.php', 0444);
		@chmod(DIR_FS_CATALOG . 'includes/configure.php', 0444);
		
		$this->sftp->chmod(0444, $p_dir . '/admin/includes/configure.org.php');
		$this->sftp->chmod(0444, $p_dir . '/admin/includes/configure.php');
		$this->sftp->chmod(0444, $p_dir . '/includes/configure.org.php');
		$this->sftp->chmod(0444, $p_dir . '/includes/configure.php');
	}
	
	
	public function check_chmod()
	{
		$this->wrong_chmod_array = SecurityCheck::getWrongPermittedInstallerFiles();
		
		return $this->wrong_chmod_array;
	}
	
	
	public function recursive_check_chmod($p_dir, $p_exclude = array('.', '..'))
	{
		if(substr($p_dir, -1) != '/')
		{
			$p_dir .= '/';
		}
		
		if(is_dir($p_dir))
		{
			if($t_dh = opendir($p_dir))
			{
				while(($t_file = readdir($t_dh)) !== false)
				{
					if(!in_array($t_file, $p_exclude))
					{
						if(!is_writable($p_dir . $t_file))
						{
							// set 777 rights only if path is not writable to prevent problems with servers which only need 755 rights to run properly
							@chmod($p_dir . $t_file, 0777);
							
							if(!is_writable($p_dir . $t_file))
							{
								$this->wrong_chmod_array[] = $p_dir . $t_file;
							}
						}
						
						if(is_dir($p_dir . $t_file))
						{
							$this->recursive_check_chmod($p_dir . $t_file, $p_exclude);
						}
					}
				}
				closedir($t_dh);
			}
		}
		
		return $this->wrong_chmod_array;
	}
	
	
	public function write_robots_file($p_dir, $p_relative_shop_path, $p_https_server = false)
	{
		$t_file_exists = @file_exists(str_replace($p_relative_shop_path, '/', DIR_FS_CATALOG) . 'robots.txt');
		
		$t_robots_content = '';
		if($t_file_exists === false)
		{
			$t_file = DIR_FS_CATALOG . 'export/robots.txt.tpl';
			
			$t_lines = file($t_file);
			
			foreach($t_lines AS $t_line)
			{
				$t_robots_content .= str_replace('{PATH}', $p_relative_shop_path, $t_line);
			}
			
			// check SSL
			if($p_https_server)
			{
				// check if ssl is in a subdirectory
				$t_http_parsed = parse_url($p_https_server);
				if(isset($t_http_parsed['path']))
				{
					$t_robots_content .= "\t\n\t\n";
					$t_path = substr($t_http_parsed['path'], 1);
					if(substr($t_path, -1, 1) != '/')
					{
						$t_path = $t_path.'/';
					}
					// again for ssl
					foreach($t_lines AS $t_line)
					{
						$t_robots_content .= str_replace('{PATH}', $p_shop_path.$t_path, $t_line);
					}
				}
			}
		}
		
		$t_handle = fopen(DIR_FS_CATALOG . 'cache/temp_robots.txt', 'w+');
		fwrite($t_handle, $t_robots_content);
		rewind($t_handle);
		
		if($p_relative_shop_path != '/' && strpos($p_dir, substr($p_relative_shop_path, 0, -1)) !== false
		   && substr($p_dir, strlen(substr($p_relative_shop_path, 0, -1)) * -1) === substr($p_relative_shop_path, 0, -1))
		{
			$t_dir =  substr($p_dir, 0, strlen(substr($p_relative_shop_path, 0, -1)) * -1);
			$t_success = false;
			
			$t_filesize = $this->sftp->size($t_dir . '/robots.txt');
			if($t_filesize !== false)
			{
				$this->sftp->delete($t_dir . '/robots.txt');
				$t_success = $this->sftp->put($t_dir . '/robots.txt', DIR_FS_CATALOG . 'cache/temp_robots.txt',
                                              phpseclib\Net\SFTP::SOURCE_LOCAL_FILE);
				$this->sftp->chmod(0644, $t_dir . '/robots.txt');
			}
			
			fclose($t_handle);
			
			if($t_success)
			{
				@unlink(DIR_FS_CATALOG . 'cache/temp_robots.txt');
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * returns path to shop if found else returns $p_dir
	 *
	 * @param string $p_dir
	 *
	 * @return string
	 */
	public function find_shop_dir($p_dir)
	{
		if($this->is_shop($p_dir))
		{
			return $p_dir;
		}
		
		$dirContent = $this->get_directories($p_dir, true);
		
		// __FILE__ = .../gambio_installer/includes/FTPManager.inc.php
		$pathArray = preg_split('![/\\\]!', dirname(dirname(dirname(__FILE__)))); // PHP 5.2 compatibility
		$pathArray = array_reverse($pathArray);
		
		$shopPath = '';
		
		foreach($pathArray as $pathDir)
		{
			$shopPath = $pathDir . '/' . $shopPath;
			
			foreach($dirContent as $dir)
			{
				if($pathDir === $dir)
				{
					$shopDir = '/' . substr($shopPath, 0, -1);
					if($this->is_shop($shopDir))
					{
						return $shopDir;
					}
				}
			}
		}
		
		return $p_dir;
	}
	
	
	public function getError()
	{
		return $this->error;
	}
	
	
	/**
	 * @param $p_dir
	 * @param $t_line
	 */
	protected function _recursive_chmod_all($p_dir, $t_line)
	{
		$t_line = trim($t_line);
		if(strlen($t_line) > 0)
		{
			$t_line = str_replace('\\', '/', $t_line);
			if(substr($t_line, 0, 1) != '/')
			{
				$t_line = '/' . $t_line;
			}
			
			if(substr($p_dir, -1) == '/')
			{
				$t_line = substr($p_dir, 0, -1) . $t_line;
			}
			else
			{
				$t_line = $p_dir . $t_line;
			}
			
			$this->data_array = array();
			$this->_recursive_ftpn_list($t_line);
			
			$this->sftp->chmod(0777, trim($t_line));
			for($i = 0; $i < count($this->data_array); $i++)
			{
				$this->sftp->chmod(0777, trim($this->data_array[$i]));
			}
		}
	}
}
