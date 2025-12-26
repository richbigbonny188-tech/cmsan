<?php
/* --------------------------------------------------------------
   AbstractAutoUpdaterHelper.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractAutoUpdaterHelper
 */
abstract class AbstractAutoUpdaterHelper
{
	/**
	 * @var \AutoUpdaterSettings
	 */
	protected $settings;
	
	/**
	 * @var \AutoUpdaterFtpManager
	 */
	protected $ftpManager;
	
	
	/**
	 * Sets the file permissions and ownership correctly.
	 *
	 * @param string      $file
	 * @param null|string $chmod
	 * @param null|string $owner
	 *
	 * @return bool
	 */
	protected function setCorrectChmodAndOwner($file, $chmod = null, $owner = null)
	{
		if($this->ftpManager !== null && $this->ftpManager->setCorrectChmodAndOwner($file, $chmod, $owner) === true)
		{
			return true;
		}
		
		$chmod = ($chmod !== null) ? $chmod : $this->settings->writingPermissionValue();
		$owner = ($owner !== null) ? $owner : @fileowner($this->settings->shopBaseDirectory());
		
		@chmod($file, $chmod);
		if($owner !== null && $owner !== false)
		{
			@chown($file, $owner);
		}
	}
	
	
	/**
	 * Return true on success, otherwise false.
	 *
	 * @param string $directory Path of the new directory.
	 * @param bool   $writable
	 *
	 * @return bool
	 */
	protected function createDirectory($directory, $writable = false)
	{
		if($this->ftpManager !== null && $this->ftpManager->createDirectory($directory, $writable) === true)
		{
			return true;
		}
		
		$directory = str_replace('\\', '/', rtrim($directory, '/'));
		
		if(is_dir($directory))
		{
			$this->setCorrectChmodAndOwner($directory, ($writable ? $this->settings->writingPermissionValue() : null));
			
			return true;
		}
		elseif(file_exists($directory))
		{
			return false;
		}
		
		if(!is_dir(dirname($directory)))
		{
			if(!$this->createDirectory(dirname($directory), $writable))
			{
				return false;
			}
		}
		
		$dirCreated = @mkdir($directory, $this->settings->writingPermissionValue());
		$this->setCorrectChmodAndOwner($directory, ($writable ? $this->settings->writingPermissionValue() : null));
		
		return $dirCreated;
	}
	
	
	/**
	 * Deletes a directory. Return true on success and false otherwise.
	 *
	 * @param string $directory
	 *
	 * @return bool
	 */
	protected function deleteDirectory($directory)
	{
		if($this->ftpManager !== null && $this->ftpManager->deleteDirectory($directory) === true)
		{
			return true;
		}
		
		$directory = str_replace('\\', '/', rtrim($directory, '/'));
		
		if(!file_exists($directory))
		{
			return true;
		}
		
		$files = array_merge(glob($directory . '/*'), glob($directory . '/.*'));
		foreach($files as $file)
		{
			if(substr($file, -2) === '/.' || substr($file, -3) === '/..')
			{
				continue;
			}
			
			if(is_dir($file))
			{
				if($this->deleteDirectory($file) === false)
				{
					return false;
				}
			}
			else
			{
				if($this->deleteFile($file) === false)
				{
					return false;
				}
			}
		}
		
		return @rmdir($directory);
	}
	
	
	/**
	 * Copies a file.
	 *
	 * @param string $source
	 * @param string $destination
	 *
	 * @return bool
	 */
	protected function copyFile($source, $destination)
	{
		if($this->ftpManager !== null && $this->ftpManager->copyFile($source, $destination) === true)
		{
			return true;
		}
		
		return @copy($source, $destination);
	}
	
	
	/**
	 * Copies a file.
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	public function deleteFile($file)
	{
		if($this->ftpManager !== null && $this->ftpManager->deleteFile($file) === true)
		{
			return true;
		}
		
		return @unlink($file);
	}
	
	
	/**
	 * Returns an array with all files inside a given directory and his subdirectories.
	 *
	 * @param string $directory
	 *
	 * @return array
	 */
	protected function getDirectoryFiles($directory)
	{
		$directory = str_replace('\\', '/', rtrim($directory, '/'));
		
		$return = [];
		$files  = array_merge(glob($directory . '/*'), glob($directory . '/.*'));
		foreach($files as $file)
		{
			if(substr($file, -2) === '/.' || substr($file, -3) === '/..')
			{
				continue;
			}
			
			if(!is_dir($file))
			{
				$return[] = realpath($file);
			}
			else
			{
				$return = array_merge($return, $this->getDirectoryFiles($file));
			}
		}
		
		return $return;
	}
	
	
	/**
	 * Replaces the base directory path of a given file path with a new base directory path.
	 *
	 * @param string $file
	 * @param string $oldBasePath
	 * @param string $newBasePath
	 *
	 * @return string Returns new path of the file. If an error occurs the unmodified file path will be returned.
	 */
	protected function changeFileBasePath($file, $oldBasePath, $newBasePath)
	{
		if(realpath($oldBasePath) !== false && realpath($newBasePath) !== false)
		{
			$file        = str_replace('\\', '/', rtrim($file, '/'));
			$oldBasePath = str_replace('\\', '/', rtrim(realpath($oldBasePath), '/'));
			$newBasePath = str_replace('\\', '/', rtrim(realpath($newBasePath), '/'));
			
			if(strpos($file, $oldBasePath) === 0)
			{
				$file = $newBasePath . substr($file, strlen($oldBasePath));
			}
		}
		
		return $file;
	}
}