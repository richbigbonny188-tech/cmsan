<?php
/* --------------------------------------------------------------
   AutoUpdater.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdater
 */
class AutoUpdater
{
	/**
	 * @var \AutoUpdaterSettings
	 */
	private $settings;

	/**
	 * @var \AutoUpdaterBackupHelper
	 */
	private $backupHelper;

	/**
	 * @var \AutoUpdaterUpdatesHelper
	 */
	private $updatesHelper;

	/**
	 * @var \AutoUpdaterDownloadHelper
	 */
	private $downloadHelper;

	/**
	 * @var array
	 */
	private $wrongPermittedFiles;

	/**
	 * @var \LogControl
	 */
	private $logControl;


	/**
	 * AutoUpdater constructor.
	 *
	 * @param \AutoUpdaterSettings       $settings
	 * @param \AutoUpdaterBackupHelper   $backupHelper
	 * @param \AutoUpdaterUpdatesHelper  $updatesHelper
	 * @param \AutoUpdaterDownloadHelper $downloadHelper
	 * @param \LogControl                $logControl
	 */
	public function __construct(AutoUpdaterSettings $settings,
	                            AutoUpdaterBackupHelper $backupHelper,
	                            AutoUpdaterUpdatesHelper $updatesHelper,
	                            AutoUpdaterDownloadHelper $downloadHelper,
	                            LogControl $logControl)
	{
		$this->settings            = $settings;
		$this->backupHelper        = $backupHelper;
		$this->updatesHelper       = $updatesHelper;
		$this->downloadHelper      = $downloadHelper;
		$this->wrongPermittedFiles = [];
		$this->logControl          = $logControl;
	}


	/**
	 * Returns a list of all available updates.
	 *
	 * @return array
	 *
	 * @throws \AutoUpdaterException
	 */
	public function getAvailableUpdates()
	{
		return $this->updatesHelper->getAvailableUpdates();
	}


	/**
	 * Returns a list of all available backups.
	 *
	 * @return array
	 */
	public function getAvailableBackups()
	{
		return $this->backupHelper->getAvailableBackups();
	}


	/**
	 * Checks if there are uninstalled updates.
	 *
	 * @return bool Returns true, if there are uninstalled updates, otherwise false.
	 */
	public function areUninstalledUpdatesAvailable()
	{
		return $this->updatesHelper->areUninstalledUpdatesAvailable();
	}


	/**
	 * Returns a list of files, that are wrong permitted.
	 *
	 * @return array
	 */
	public function getWrongPermittedFiles()
	{
		return $this->wrongPermittedFiles;
	}


	/**
	 * Checks the writing permissions for a given list of files.
	 *
	 * @param array $fileList
	 *
	 * @return bool
	 */
	public function checkFilesPermissionsWithFileList(array $fileList)
	{
		$this->wrongPermittedFiles = $this->updatesHelper->checkFilesPermissionsWithFileList($fileList);

		return count($this->wrongPermittedFiles) === 0;
	}


	/**
	 * Checks the writing permissions for a given list of files.
	 *
	 * @param array $fileList
	 *
	 * @return bool
	 */
	public function checkFilesPermissionsForBackup($backupId)
	{
		$fileList                  = $this->backupHelper->getBackupFileList($backupId);
		$this->wrongPermittedFiles = $this->updatesHelper->checkFilesPermissionsWithFileList($fileList);

		return count($this->wrongPermittedFiles) === 0;
	}
	
	
	/**
	 * Installs a single given update.
	 *
	 * @param \AutoUpdaterUpdate $update
	 *
	 * @return array
	 *
	 * @throws \AutoUpdaterBackupCreationFailedException
	 * @throws \AutoUpdaterBackupRestoreFailedException
	 * @throws \AutoUpdaterMissingPermissionException
	 * @throws \AutoUpdaterUpdateDownloadFailedException
	 */
	public function processUpdate(AutoUpdaterUpdate $update)
	{
		$this->createDebugLog('[AutoUpdater] Process update', [
			'updateId' => $update->id(),
		]);

		$progress             = 1;
		$processingStatus     = $this->updatesHelper->getProcessingStatus($update->id());
		$updateFilesDirectory = $this->settings->updatesDirectory() . '/' . $update->id();

		switch($processingStatus['state'])
		{
			case 'start':
				$this->downloadHelper->downloadUpdate($update, $updateFilesDirectory, $processingStatus['lastIndex']);
				$processingStatus['lastIndex'] = $this->downloadHelper->getLastDownloadedIndex();
				if($this->downloadHelper->downloadingFilesDone())
				{
					$processingStatus['state']     = 'downloaded';
					$processingStatus['lastIndex'] = null;
				}
				$progress = round(($this->downloadHelper->getProgress() * 3) / 14, 2);
				break;
			case 'downloaded':
				$this->wrongPermittedFiles = $this->updatesHelper->checkFilesPermissions($updateFilesDirectory);
				if(count($this->wrongPermittedFiles) > 0)
				{
					throw new AutoUpdaterMissingPermissionException('Permission check failed: There are wrong permitted files.');
				}
				$processingStatus['state'] = 'permissionChecked';
				$progress                  = round(4 / 14, 2);
				break;
			case 'permissionChecked':
				$this->backupHelper->backupFiles($update, $updateFilesDirectory, $processingStatus['backupId'],
				                                 $processingStatus['newUpdateFiles'], $processingStatus['lastIndex']);
				$processingStatus['backupId']       = $this->backupHelper->getBackupId();
				$processingStatus['lastIndex']      = $this->backupHelper->getLastBackupedIndex();
				$processingStatus['newUpdateFiles'] = $this->backupHelper->getNewUpdateFiles();
				if($this->backupHelper->backupingFilesDone())
				{
					$processingStatus['state']          = 'backupCreated';
					$processingStatus['lastIndex']      = null;
					$processingStatus['newUpdateFiles'] = null;
				}
				$progress = round((4 + $this->backupHelper->getProgress() * 3) / 14, 2);
				break;
			case 'backupCreated':
				try
				{
					$this->updatesHelper->copyFilesIntoShop($updateFilesDirectory, $processingStatus['lastIndex']);
					$processingStatus['lastIndex'] = $this->updatesHelper->getLastCopiedIndex();
					if($this->updatesHelper->copyingFilesIntoShopDone())
					{
						$processingStatus['state']     = 'filesCopied';
						$processingStatus['lastIndex'] = null;
					}
					$progress = round((7 + $this->updatesHelper->getProgress() * 3) / 14, 2);
				}
				catch(Exception $e)
				{
					$this->callHome($e->getMessage(), $update->id(), $e);
					$processingStatus['state']     = 'copyFailed';
					$processingStatus['exception'] = serialize($e);
					$progress                      = round(10 / 14, 2);
				}
				break;
			case 'filesCopied':
				$this->updatesHelper->deleteUpdateDirectory($updateFilesDirectory);
				$processingStatus['state'] = 'cleanedUp';
				$progress                  = round(11 / 14, 2);
				break;
			case 'copyFailed':
				$this->backupHelper->restoreBackup($processingStatus['backupId'], $processingStatus['lastIndex']);
				$processingStatus['lastIndex'] = $this->backupHelper->getLastRestoredIndex();
				if($this->backupHelper->restoringFilesDone())
				{
					$processingStatus['state']          = 'backupRestored';
					$processingStatus['lastIndex']      = null;
					$processingStatus['newUpdateFiles'] = null;
				}
				$progress = round((10 + $this->backupHelper->getProgress() * 3) / 14, 2);
				break;
			case 'cleanedUp':
				$this->updatesHelper->resetVersionHistoryName($update->versionHistoryName());
				$this->updatesHelper->deleteInfoboxMessage($update->infoboxIdentifier());
				$processingStatus['state'] = 'done';
				$progress                  = round(12 / 14, 2);
				break;
			case 'backupRestored':
				$this->updatesHelper->deleteUpdateDirectory($updateFilesDirectory);
				throw unserialize($processingStatus['exception']);
		}

		$this->updatesHelper->setProcessingStatus($update->id(), $processingStatus);

		return [
			'done'     => $processingStatus['state'] === 'done',
			'progress' => $progress,
		];
	}


	/**
	 * Restores a backup by a given id.
	 *
	 * @param string   $backupId
	 * @param int|null $lastRestoredIndex
	 *
	 * @return array
	 *
	 * @throws \AutoUpdaterBackupRestoreFailedException
	 */
	public function restoreBackup($backupId, $lastRestoredIndex = null)
	{
		$this->backupHelper->restoreBackup($backupId, $lastRestoredIndex);

		return [
			'done'              => $this->backupHelper->restoringFilesDone(),
			'lastRestoredIndex' => $this->backupHelper->getLastRestoredIndex(),
		];
	}


	/**
	 * Deletes a backup by a given id.
	 *
	 * @param string $backupId
	 *
	 * @throws \AutoUpdaterBackupRestoreFailedException
	 */
	public function deleteBackup($backupId)
	{
		$this->backupHelper->deleteBackup($backupId);
	}


	/**
	 * Returns an update instance by a given update id.
	 *
	 * @param string $updateId
	 *
	 * @return \AutoUpdaterUpdate
	 *
	 * @throws \AutoUpdaterException
     *
     * @deprecated
	 */
	public function fetchUpdateById($updateId)
	{
		$update           = null;
		$availableUpdates = $this->updatesHelper->getAvailableUpdates();

		/* @var \AutoUpdaterUpdate $availableUpdate */
		foreach($availableUpdates as $availableUpdate)
		{
			if($availableUpdate->id() === $updateId)
			{
				$update = $availableUpdate;
			}
		}

		if($update === null)
		{
			throw new AutoUpdaterException('Selected update is not available.');
		}

		return $update;
	}


	/**
	 * Activates the ftp manager. After activation all filesystem actions will be primary done with (s)ftp.
	 *
	 * @param \AutoUpdaterFtpManager $ftpManager
	 */
	public function activateFtpManager(AutoUpdaterFtpManager $ftpManager)
	{
		$this->createDebugLog('[AutoUpdater] Activate ftp manager');

		$this->backupHelper->registerFtpManager($ftpManager);
		$this->updatesHelper->registerFtpManager($ftpManager);
		$this->downloadHelper->registerFtpManager($ftpManager);
	}


	/**
	 * Calls home and sends some feedback about the update processing
	 *
	 * @param string          $message
	 * @param string          $updateId
	 * @param \Exception|null $exception
	 */
	public function callHome($message,
	                         $updateId = '',
	                         \Exception $exception = null)
	{
		$this->updatesHelper->callHome($message, $updateId, $this->wrongPermittedFiles, $exception);
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