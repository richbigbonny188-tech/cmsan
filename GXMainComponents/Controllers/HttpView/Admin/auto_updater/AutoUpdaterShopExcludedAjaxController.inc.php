<?php
/* --------------------------------------------------------------
   AutoUpdaterShopExcludedAjaxController.inc.php 2019-01-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterShopExcludedAjaxController
 */
class AutoUpdaterShopExcludedAjaxController
{
	/**
	 * @var \CI_DB_query_builder
	 */
	protected $queryBuilder;
	
	/**
	 * @var \DataCache
	 */
	protected $dataCache;
	
	/**
	 * @var \LogControl
	 */
	protected $logControl;
	
	/**
	 * @var \AutoUpdater
	 */
	protected $autoUpdater;
	
	/**
	 * @var array
	 */
	protected $exceptionErrorPhrase = [
		AutoUpdaterBackupCreationFailedException::class => 'ajax_error_backup_creation_failed',
		AutoUpdaterBackupRestoreFailedException::class  => 'ajax_error_backup_restore_failed',
		AutoUpdaterUpdateDownloadFailedException::class => 'ajax_error_update_download_failed',
		AutoUpdaterMissingPermissionException::class    => 'ajax_error_missing_permissions',
		'default'                                       => 'ajax_error_unexpected_error',
	];
	
	/**
	 * @var array
	 */
	protected $exceptionDebugLog = [
		AutoUpdaterBackupCreationFailedException::class => 'Backup creation failed',
		AutoUpdaterBackupRestoreFailedException::class  => 'Backup restore failed',
		AutoUpdaterUpdateDownloadFailedException::class => 'Download of update file failed',
		AutoUpdaterMissingPermissionException::class    => 'Missing file permissions',
		'default'                                       => 'Unexpected error',
	];
	
	/**
	 * @var array
	 */
	protected $exceptionState = [
		AutoUpdaterBackupCreationFailedException::class => 'backup_creation_failed',
		AutoUpdaterBackupRestoreFailedException::class  => 'backup_restore_failed',
		AutoUpdaterUpdateDownloadFailedException::class => 'update_download_failed',
		AutoUpdaterMissingPermissionException::class    => 'permission_check_failed',
		'default'                                       => 'install_failed',
	];
	
	
	public function __construct()
	{
		$this->queryBuilder = $this->getQueryBuilder();
		$this->dataCache    = DataCache::get_instance();
		$this->logControl   = LogControl::get_instance();
		
		$this->autoUpdaterFactory = new AutoUpdaterFactory($this->queryBuilder, $this->dataCache, $this->logControl);
		$this->autoUpdater        = $this->autoUpdaterFactory->createAutoUpdater();
	}
	
	
	/**
	 * Installs a single update.
	 */
	public function actionInstallUpdate()
	{
        $error           = null;
        $list            = [];
        $state           = null;
        $gambioStoreData = $_POST['gambioStoreData'];
        $gambioStoreData = str_replace('\"', '"', $gambioStoreData);
        $gambioStoreData = json_decode($gambioStoreData, true);
        
        try {
            $protocol = isset($_POST['ftp-protocol']) ? $_POST['ftp-protocol'] : null;
            $server   = isset($_POST['ftp-server']) ? $_POST['ftp-server'] : null;
            $login    = isset($_POST['ftp-login']) ? $_POST['ftp-login'] : null;
            if (!empty($protocol) && !empty($server) && !empty($login)) {
                $password = isset($_POST['ftp-password']) ? $_POST['ftp-password'] : null;
                $port     = isset($_POST['ftp-port']) ? $_POST['ftp-port'] : null;
				$passive  = (isset($_POST['ftp-passive']) ? $_POST['ftp-passive'] : null) === 'true';
                
                try {
                    $ftpManager = $this->autoUpdaterFactory->createFtpManager($protocol, $server, $login, $password,
                        $port, $passive);
                    $this->autoUpdater->activateFtpManager($ftpManager);
                } catch (AutoUpdaterException $e) {
                }
            }
            $gambioStorePackage = AutoUpdaterUpdate::createByGambioStoreData($gambioStoreData);
            $processingStatus   = $this->autoUpdater->processUpdate($gambioStorePackage);
        }
		catch(Exception $e)
		{
			$wrongPermittedFiles = $this->autoUpdater->getWrongPermittedFiles();
			
			$this->createDebugLog('[AjaxControl] ' . $this->exceptionDebugLog[get_class($e)], [
				'message'                => $e->getMessage(),
				'trace'                  => $e->getTrace(),
				'getWrongPermittedFiles' => $wrongPermittedFiles,
			]);
			
			$error = $this->exceptionErrorPhrase[get_class($e)];
			$state = $this->exceptionState[get_class($e)];
			
			echo json_encode([
				                 'success'  => false,
				                 'error'    => $error,
				                 'list'     => $wrongPermittedFiles,
				                 'state'    => $state,
				                 'done'     => false,
                                 'message'  => $e->getMessage(),
                                 'progress' => 1,
			                 ]);
			die();
		}
		
		if($processingStatus['done'] === true)
		{
			$downloadedUpdates = [];
			if($this->dataCache->key_exists('auto-updater-downloaded-updates', true))
			{
				$downloadedUpdates = $this->dataCache->get_data('auto-updater-downloaded-updates', true);
			}
			$downloadedUpdates[] = [
                'id'          => $gambioStorePackage->id(),
                'name'        => $gambioStorePackage->name('en'),
                'version'     => '',
                'date'        => date('Y-m-d H:i:s'),
                'receiptFile' => $gambioStorePackage->receiptFile(),
            ];
			$this->dataCache->set_data('auto-updater-downloaded-updates', $downloadedUpdates, true);

			// remove processing status if update is finished … otherwise, all subsequent downloads will fail
            $this->dataCache->clear_cache('auto-update-processing-status');
		}
		
		echo json_encode([
			                 'success'  => true,
			                 'error'    => '',
			                 'list'     => '',
			                 'state'    => 'processing',
			                 'done'     => $processingStatus['done'],
			                 'progress' => $processingStatus['progress'],
		                 ]);
	}
	
	
	/**
	 * Restores a backup by a given id.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionRestoreBackup()
	{
		$backupId = $_POST['backupId'];
		if($backupId === null)
		{
			echo json_encode([
				                 'success'           => false,
				                 'done'              => false,
				                 'lastRestoredIndex' => null,
				                 'error'             => 'ajax_error_no_backup_selected',
			                 ]);
			die();
		}
		
		try
		{
			$protocol = $_POST['ftp-protocol'];
			$server   = $_POST['ftp-server'];
			$login    = $_POST['ftp-login'];
			if(!empty($protocol) && !empty($server) && !empty($login))
			{
				$password = $_POST['ftp-password'];
				$port     = $_POST['ftp-port'];
				$passive  = $_POST['ftp-passive'] === 'true';
				
				try
				{
					$ftpManager = $this->autoUpdaterFactory->createFtpManager($protocol, $server, $login, $password,
					                                                          $port, $passive);
					$this->autoUpdater->activateFtpManager($ftpManager);
				}
				catch(AutoUpdaterException $e)
				{
				}
			}
			$lastRestoredIndex = $_POST['lastRestoredIndex'];
			$response          = $this->autoUpdater->restoreBackup($backupId, $lastRestoredIndex);
		}
		catch(AutoUpdaterBackupRestoreFailedException $e)
		{
			echo json_encode([
				                 'success'           => false,
				                 'done'              => false,
				                 'lastRestoredIndex' => null,
				                 'error'             => 'ajax_error_backup_restore_failed',
			                 ]);
			die();
		}
		
		echo json_encode([
			                 'success'           => true,
			                 'done'              => $response['done'],
			                 'lastRestoredIndex' => $response['lastRestoredIndex'],
			                 'error'             => '',
		                 ]);
	}
	
	
	/**
	 * Method depends on CodeIgniter database library
	 *
	 * @return CI_DB_query_builder
	 */
	protected function getQueryBuilder()
	{
		if($this->queryBuilder !== null)
		{
			return $this->queryBuilder;
		}
		
		$dbUser     = DB_SERVER_USERNAME;
		$dbPassword = urlencode(DB_SERVER_PASSWORD);
		$dbServer   = DB_SERVER;
		$dbName     = DB_DATABASE;
		
		if(strpos(DB_SERVER, ':/')) // mysql socket detected
		{
			$exploded = explode(':', DB_SERVER);
			$dbServer = array_shift($exploded);
			$dbSocket = array_shift($exploded);
		}
		$dbSocket = isset($dbSocket) ? '?socket=' . $dbSocket : '';
		
		$connectionString   = 'mysqli://' . $dbUser . ':' . $dbPassword . '@' . $dbServer . '/' . $dbName . $dbSocket;
		$this->queryBuilder = CIDB($connectionString);
		
		return $this->queryBuilder;
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