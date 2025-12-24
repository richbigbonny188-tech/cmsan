<?php
/* --------------------------------------------------------------
   AutoUpdaterBackupHelper.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterBackupHelper
 */
class AutoUpdaterBackupHelper extends AbstractAutoUpdaterHelper
{
	/**
	 * @var \AutoUpdaterSettings
	 */
	protected $settings;
	
	/**
	 * @var \AutoUpdaterDataCache
	 */
	protected $dataCache;
	
	/**
	 * @var array
	 */
	protected $availableBackups;
	
	/**
	 * @var \AutoUpdaterFtpManager
	 */
	protected $ftpManager;
	
	/**
	 * @var \LogControl
	 */
	protected $logControl;
	
	/**
	 * @var bool
	 */
	protected $backupingFilesDone;
	
	/**
	 * @var int|null
	 */
	protected $lastBackupedIndex;
	
	/**
	 * @var bool
	 */
	protected $restoringFilesDone;
	
	/**
	 * @var int|null
	 */
	protected $lastRestoredIndex;
	
	/**
	 * @var string
	 */
	protected $backupId;
	
	/**
	 * @var array
	 */
	protected $newUpdateFiles;
	
	/**
	 * @var float
	 */
	protected $progress;
	
	/**
	 * @var int
	 */
	protected $ajaxTimeout;
	
	
	/**
	 * AutoUpdaterFilesystemHelper constructor.
	 *
	 * @param \AutoUpdaterSettings  $settings
	 * @param \AutoUpdaterDataCache $dataCache
	 * @param \LogControl           $logControl
	 */
	public function __construct(AutoUpdaterSettings $settings,
	                            AutoUpdaterDataCache $dataCache,
	                            LogControl $logControl)
	{
		$this->settings    = $settings;
		$this->dataCache   = $dataCache;
		$this->ftpManager  = null;
		$this->logControl  = $logControl;
		$this->ajaxTimeout = 10;
	}
	
	
	/**
	 * Registers the auto updater ftp manager.
	 *
	 * @param \AutoUpdaterFtpManager $ftpManager
	 */
	public function registerFtpManager(AutoUpdaterFtpManager $ftpManager)
	{
		$this->ftpManager = $ftpManager;
	}
	
	
	/**
	 * Returns a list of available backups.
	 *
	 * @return array
	 */
	public function getAvailableBackups()
	{
		$this->createDebugLog('[BackupHelper] Collect available backups');
		
		if($this->availableBackups === null)
		{
			$this->availableBackups = [];
			if($this->dataCache->key_exists('auto-updater-backups', true))
			{
				$cachedBackups = $this->dataCache->get_data('auto-updater-backups', true);
				foreach($cachedBackups as $index => $backup)
				{
					if(file_exists($backup['directoryPath']))
					{
						$this->availableBackups[] = AutoUpdaterBackup::createFromDataCache($index, $backup);
					}
					else
					{
						unset($cachedBackups[$index]);
					}
				}
				$this->dataCache->set_data('auto-updater-backups', $cachedBackups, true);
			}
		}
		
		return $this->availableBackups;
	}
	
	
	/**
	 * Returns a list of files, that belongs to the given backup id.
	 *
	 * @param string $backupId
	 *
	 * @return array
	 */
	public function getBackupFileList($backupId)
	{
		$filelist = [];
		if($this->dataCache->key_exists('auto-updater-backups', true))
		{
			$cachedBackups = $this->dataCache->get_data('auto-updater-backups', true);
			if(isset($cachedBackups[$backupId]))
			{
				$backup   = AutoUpdaterBackup::createFromDataCache($backupId, $cachedBackups[$backupId]);
				$filelist = array_merge($this->getDirectoryFiles($backup->directoryPath()), $backup->newUpdateFiles());
			}
		}
		
		$this->createDebugLog('[BackupHelper] Collect file list of backup', [
			'backupId' => $backupId,
			'filelist' => $filelist,
		]);
		
		return $filelist;
	}
	
	
	/**
	 * Creates a backup by a given update and update files directory.
	 *
	 * @param \AutoUpdaterUpdate $update
	 * @param string             $updateFilesDirectory
	 * @param string|null        $backupId
	 * @param array|null         $newUpdateFiles
	 * @param int|null           $lastBackupedIndex
	 *
	 * @return string
	 *
	 * @throws \AutoUpdaterBackupCreationFailedException
	 */
	public function backupFiles(AutoUpdaterUpdate $update,
	                            $updateFilesDirectory,
	                            $backupId = null,
	                            $newUpdateFiles = null,
	                            $lastBackupedIndex = null)
	{
		$this->createDebugLog('[BackupHelper] Create backup for update', [
			'updateId'             => $update->id(),
			'updateName'           => $update->name('en'),
			'updateFilesDirectory' => $updateFilesDirectory,
			'backupId'             => $backupId,
			'newUpdateFiles'       => $newUpdateFiles,
			'lastBackupedIndex'    => $lastBackupedIndex,
		]);
		
		$startTime                = time();
		$this->backupId           = isset($backupId) ? $backupId : time() . '-' . $update->id();
		$this->lastBackupedIndex  = $lastBackupedIndex;
		$this->backupingFilesDone = true;
		$this->newUpdateFiles     = isset($newUpdateFiles) ? $newUpdateFiles : [];
		
		$backupDirectory = $this->settings->backupsDirectory() . '/' . $this->backupId . '/';
		if($this->lastBackupedIndex === null)
		{
			$this->deleteDirectory($backupDirectory);
			if($this->createDirectory($backupDirectory, true) === false)
			{
				throw new AutoUpdaterBackupCreationFailedException('Could not create directory "' . $backupDirectory
				                                                   . '".');
			}
		}
		
		$updateFiles = $this->getDirectoryFiles($updateFilesDirectory);
		foreach($updateFiles as $index => $updateFile)
		{
			if($index <= $this->lastBackupedIndex && $this->lastBackupedIndex !== null)
			{
				continue;
			}
			
			$shopFile      = $this->changeFileBasePath($updateFile, $updateFilesDirectory, DIR_FS_CATALOG);
			$backupFile    = $this->changeFileBasePath($updateFile, $updateFilesDirectory, $backupDirectory);
			$backupFileDir = dirname($backupFile);
			$this->createDirectory($backupFileDir, true);
			if(file_exists($shopFile))
			{
				$fileCopied = $this->copyFile($shopFile, $backupFile);
				$this->setCorrectChmodAndOwner($backupFile);
				if($fileCopied === false || filesize($shopFile) !== filesize($backupFile))
				{
					throw new AutoUpdaterBackupCreationFailedException('Could not backup file "' . $shopFile . '".');
				}
			}
			else
			{
				$this->newUpdateFiles[] = $shopFile;
			}
			
			$this->lastBackupedIndex = $index;
			if($startTime < time() - $this->ajaxTimeout = 10)
			{
				$this->backupingFilesDone = false;
				break;
			}
		}
		$this->progress = $this->lastBackupedIndex / (count($updateFiles) - 1);
		
		if($this->backupingFilesDone)
		{
			$cachedBackups = [];
			if($this->dataCache->key_exists('auto-updater-backups', true))
			{
				$cachedBackups = $this->dataCache->get_data('auto-updater-backups', true);
			}
			$cachedBackups[$this->backupId] = [
				'receiptFile'    => $update->receiptFile(),
				'directoryPath'  => $backupDirectory,
				'updateName'     => $update->name(),
				'creationDate'   => date('Y-m-d H:i:s'),
				'newUpdateFiles' => $this->newUpdateFiles,
			];
			$this->dataCache->set_data('auto-updater-backups', $cachedBackups, true);
		}
	}
	
	
	/**
	 * @return bool
	 */
	public function backupingFilesDone()
	{
		return $this->backupingFilesDone;
	}
	
	
	/**
	 * @return int
	 */
	public function getLastBackupedIndex()
	{
		return $this->lastBackupedIndex;
	}
	
	
	/**
	 * @return bool
	 */
	public function restoringFilesDone()
	{
		return $this->restoringFilesDone;
	}
	
	
	/**
	 * @return int
	 */
	public function getLastRestoredIndex()
	{
		return $this->lastRestoredIndex;
	}
	
	
	/**
	 * @return string
	 */
	public function getBackupId()
	{
		return $this->backupId;
	}
	
	
	/**
	 * @return array
	 */
	public function getNewUpdateFiles()
	{
		return $this->newUpdateFiles;
	}
	
	
	/**
	 * @return float
	 */
	public function getProgress()
	{
		return $this->progress;
	}
	
	
	/**
	 * Restores and backup by coping
	 *
	 * @param string   $backupId
	 * @param int|null $lastRestoredIndex
	 *
	 * @throws \AutoUpdaterBackupRestoreFailedException
	 */
	public function restoreBackup($backupId, $lastRestoredIndex = null)
	{
		$cachedBackups = $this->dataCache->get_data('auto-updater-backups', true);
		if(!isset($cachedBackups[$backupId]))
		{
			throw new AutoUpdaterBackupRestoreFailedException('Could not found backup data for "' . $backupId . '".');
		}
		
		$this->createDebugLog('[BackupHelper] Restore backup', [
			'backupId'          => $backupId,
			'updateName'        => $cachedBackups[$backupId]['updateName']['en'],
			'directoryPath'     => $cachedBackups[$backupId]['directoryPath'],
			'newUpdateFiles'    => $cachedBackups[$backupId]['newUpdateFiles'],
			'lastRestoredIndex' => $lastRestoredIndex,
		]);
		
		$startTime                = time();
		$backupFilesDir           = $cachedBackups[$backupId]['directoryPath'];
		$newUpdateFiles           = $cachedBackups[$backupId]['newUpdateFiles'];
		$this->restoringFilesDone = true;
		$this->lastRestoredIndex  = $lastRestoredIndex;
		
		$backupFiles = $this->getDirectoryFiles($backupFilesDir);
		foreach($backupFiles as $index => $backupFile)
		{
			if($index <= $this->lastRestoredIndex && $this->lastRestoredIndex !== null)
			{
				continue;
			}
			
			$shopFile = $this->changeFileBasePath($backupFile, $backupFilesDir, DIR_FS_CATALOG);
			if(file_exists($shopFile) && $this->deleteFile($shopFile) === false)
			{
				throw new AutoUpdaterBackupRestoreFailedException('Could not delete shop file "' . $shopFile
				                                                  . '" to restore backup file "' . $backupFile . '".');
			}
			
			$fileCopied = $this->copyFile($backupFile, $shopFile);
			$this->setCorrectChmodAndOwner($shopFile);
			if($fileCopied === false || filesize($shopFile) !== filesize($backupFile))
			{
				throw new AutoUpdaterBackupRestoreFailedException('Could not restore shop file "' . $shopFile
				                                                  . '" with backup file "' . $backupFile . '".');
			}
			
			$this->lastRestoredIndex = $index;
			if($startTime < time() - $this->ajaxTimeout = 10)
			{
				$this->restoringFilesDone = false;
				break;
			}
		}
		$this->progress = $this->lastBackupedIndex / (count($backupFiles) - 1);
		
		if($this->restoringFilesDone)
		{
			foreach($newUpdateFiles as $newUpdateFile)
			{
				if(file_exists($newUpdateFile) && $this->deleteFile($newUpdateFile) === false)
				{
					throw new AutoUpdaterBackupRestoreFailedException('Could not delete new update file "'
					                                                  . $newUpdateFile . '".');
				}
			}
			
			unset($cachedBackups[$backupId]);
			$this->dataCache->set_data('auto-updater-backups', $cachedBackups, true);
		}
	}
	
	
	/**
	 * Restores and backup by coping
	 *
	 * @param string $backupId
	 *
	 * @throws \AutoUpdaterBackupRestoreFailedException
	 */
	public function deleteBackup($backupId)
	{
		$cachedBackups = $this->dataCache->get_data('auto-updater-backups', true);
		if(!isset($cachedBackups[$backupId]))
		{
			throw new AutoUpdaterBackupRestoreFailedException('Could not found backup data for "' . $backupId . '".');
		}
		
		$this->createDebugLog('[BackupHelper] Delete backup', [
			'backupId'      => $backupId,
			'updateName'    => $cachedBackups[$backupId]['updateName']['en'],
			'directoryPath' => $cachedBackups[$backupId]['directoryPath'],
		]);
		
		$backupFilesDir = $cachedBackups[$backupId]['directoryPath'];
		$this->deleteDirectory($backupFilesDir);
		unset($cachedBackups[$backupId]);
		$this->dataCache->set_data('auto-updater-backups', $cachedBackups, true);
	}
	
	
	/**
	 * Creates a new debug log.
	 *
	 * @param string       $message
	 * @param string|array $additionalData
	 */
	protected function createDebugLog($message, $additionalData = '')
	{
		if(is_array($additionalData))
		{
			$additionalData = json_encode($additionalData);
		}
		
		$this->logControl->notice($message, '', 'auto_updater', 'notice', 'USER NOTICE', 0, $additionalData);
	}
}