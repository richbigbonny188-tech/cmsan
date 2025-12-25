<?php
/* --------------------------------------------------------------
   AutoUpdaterFtpManager.inc.php 2018-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use League\Flysystem\Filesystem;

/**
 * Class AutoUpdaterFtpManager
 */
class AutoUpdaterFtpManager
{
	/**
	 * @var \AutoUpdaterSettings
	 */
	private $settings;
	
	/**
	 * @var \AutoUpdaterFilesystem
	 */
	private $filesystem;
	
	/**
	 * @var \LogControl
	 */
	private $logControl;
	
	
	/**
	 * AutoUpdaterFtpManager constructor.
	 *
	 * @param \AutoUpdaterSettings         $settings
	 * @param \League\Flysystem\Filesystem $filesystem
	 * @param \LogControl                  $logControl
	 */
	public function __construct(AutoUpdaterSettings $settings,
	                            Filesystem $filesystem,
	                            LogControl $logControl)
	{
		$this->settings   = $settings;
		$this->filesystem = $filesystem;
		$this->logControl = $logControl;
	}
	
	
	/**
	 * Sets the file permissions and ownership correctly.
	 *
	 * @param string      $file
	 * @param null|string $chmod
	 * @param null|string $owner
	 *
	 * @return bool
	 */
	public function setCorrectChmodAndOwner($file, $chmod = null, $owner = null)
	{
		if($chmod === null)
		{
			if(is_writable(dirname($file)))
			{
				$chmod = $this->settings->writingPermissionValue();
			}
			else
			{
				$chmod = is_dir($file) ? $this->settings->normalDirectoryPermissionValue() : $this->settings->normalFilePermissionValue();
			}
		}
		$owner = ($owner !== null) ? $owner : @fileowner($this->settings->shopBaseDirectory());
		
		$this->createDebugLog('[FtpManager] Set correct chmod and owner', [
			'chmod' => $chmod,
			'owner' => $owner,
		]);
		
		if($owner !== null && $owner !== false)
		{
			@chown($file, $owner);
		}
		return @$this->filesystem->chmod($file, $chmod);
	}
	
	
	/**
	 * Return true on success, otherwise false.
	 *
	 * @param string $directory Path of the new directory.
	 *
	 * @return bool
	 */
	public function createDirectory($directory)
	{
		$directory = str_replace('\\', '/', rtrim($directory, '/'));
		
		$this->createDebugLog('[FtpManager] Create directory', [
			'directory' => $directory,
		]);
		
		if(is_dir($directory))
		{
			if((fileperms($directory) & 0777) !== $this->settings->writingPermissionValue())
			{
				$this->setCorrectChmodAndOwner($directory);
			}
			
			return true;
		}
		elseif(file_exists($directory))
		{
			return false;
		}
		
		if(!is_dir(dirname($directory)))
		{
			if(!$this->createDirectory(dirname($directory)))
			{
				return false;
			}
		}
		
		$dirCreated = @$this->filesystem->createDir($directory);
		$this->setCorrectChmodAndOwner($directory);
		
		return $dirCreated;
	}
	
	
	/**
	 * Deletes a directory. Return true on success and false otherwise.
	 *
	 * @param string $directory
	 *
	 * @return bool
	 */
	public function deleteDirectory($directory)
	{
		$directory = str_replace('\\', '/', rtrim($directory, '/'));
		
		$this->createDebugLog('[FtpManager] Delete directory', [
			'directory' => $directory,
		]);
		
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
		
		return @$this->filesystem->deleteDir($directory) === true;
	}
	
	
	/**
	 * Copies a file.
	 *
	 * @param string $source
	 * @param string $destination
	 *
	 * @return bool
	 */
	public function copyFile($source, $destination)
	{
		$this->createDebugLog('[FtpManager] Copy file', [
			'source'      => $source,
			'destination' => $destination,
		]);
		
		try
		{
			return @$this->filesystem->copy($source, $destination) === true;
		}
		catch(Exception $e)
		{
			return false;
		}
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
		$this->createDebugLog('[FtpManager] Delete file', [
			'file' => $file,
		]);
		
		try
		{
			return @$this->filesystem->delete($file) === true;
		}
		catch(\League\Flysystem\FileNotFoundException $e)
		{
			return true;
		}
		catch(Exception $e)
		{
			return false;
		}
	}
	
	
	/**
	 * Creates a new debug log.
	 *
	 * @param string       $message
	 * @param string|array $additionalData
	 */
	protected function createDebugLog($message, $additionalData = '')
	{
		#if(is_array($additionalData))
		#{
		#	$additionalData = json_encode($additionalData);
		#}
		#
		#$this->logControl->notice($message, '', 'auto_updater', 'notice', 'USER NOTICE', 0, $additionalData);
	}
}