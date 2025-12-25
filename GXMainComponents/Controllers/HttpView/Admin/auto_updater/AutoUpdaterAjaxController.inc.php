<?php
/* --------------------------------------------------------------
   AutoUpdaterAjaxController.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class AutoUpdaterAjaxController
 */
class AutoUpdaterAjaxController extends AdminHttpViewController
{
	/**
	 * @var \AutoUpdater
	 */
	protected $autoUpdater;
	
	/**
	 * @var \AutoUpdaterFactory
	 */
	protected $autoUpdaterFactory;
	
	/**
	 * @var \CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var \LanguageTextManager
	 */
	private $textPhrases;
	
	/**
	 * @var string
	 */
	private $updateServer;
	
	/**
	 * @var \DataCache
	 */
	private $dataCache;
	
	/**
	 * @var \LogControl
	 */
	private $logControl;
	
	
	/**
	 * Initial method for this controller.
	 */
	public function init()
	{
		$this->db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$this->dataCache  = DataCache::get_instance();
		$this->logControl = LogControl::get_instance();
		
		$this->autoUpdaterFactory = new AutoUpdaterFactory($this->db, $this->dataCache, $this->logControl);
		$this->autoUpdater        = $this->autoUpdaterFactory->createAutoUpdater();
		$this->textPhrases        = MainFactory::create('LanguageTextManager', 'auto_updater',
		                                                $_SESSION['languages_id']);
		$this->updateServer       = 'http://localhost/update-server/public/v2/callingHome.php';
	}
	
	
	/**
	 * Checks the permission for an update. Update ID must be given as post parameter.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionCheckPermission()
	{
		try
		{
            $gambioStoreData    = $this->_getPostData('gambioStoreData');
            $gambioStoreData    = str_replace('\"', '"', $gambioStoreData);
            $gambioStoreData    = json_decode($gambioStoreData, true);
            $gambioStorePackage = AutoUpdaterUpdate::createByGambioStoreData($gambioStoreData);
            
            $fileList = [];
            foreach ($gambioStorePackage->fileList() as $updateFile) {
                $fileList[] = DIR_FS_CATALOG . $updateFile['destination'];
            }
            
            return MainFactory::create('JsonHttpControllerResponse', [
                'success' => true,
                'error'   => '',
                'result'  => $this->autoUpdater->checkFilesPermissionsWithFileList($fileList),
                'list'    => $this->autoUpdater->getWrongPermittedFiles(),
            ]);
		}
		catch(Exception $e)
		{
			return MainFactory::create('JsonHttpControllerResponse', [
				'success' => false,
				'error'   => $e->getMessage(),
				'result'  => false,
				'list'    => [],
			]);
		}
	}
	
	
	/**
	 * Checks the permission for an update. Update ID must be given as post parameter.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionCheckPermissionForBackupRestore()
	{
		$backupId = $this->_getPostData('backupId');
		if($backupId === null)
		{
			return MainFactory::create('JsonHttpControllerResponse', [
				'success' => false,
				'error'   => $this->textPhrases->get_text('ajax_error_no_backup_selected'),
				'result'  => false,
				'list'    => []
			]);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', [
			'success' => true,
			'error'   => '',
			'result'  => $this->autoUpdater->checkFilesPermissionsForBackup($backupId),
			'list'    => $this->autoUpdater->getWrongPermittedFiles(),
		]);
	}
	
	
	/**
	 * Deletes a backup by a given id.
	 *
	 * @return \JsonHttpControllerResponse
	 *
	 * @throws \AutoUpdaterBackupRestoreFailedException
	 */
	public function actionDeleteBackup()
	{
		$backupId = $this->_getPostData('backupId');
		if($backupId === null)
		{
			return MainFactory::create('JsonHttpControllerResponse', [
				'success' => false,
				'error'   => $this->textPhrases->get_text('ajax_error_no_backup_selected'),
			]);
		}
		
		$this->autoUpdater->deleteBackup($backupId);
		
		return MainFactory::create('JsonHttpControllerResponse', [
			'success' => true,
			'error'   => '',
		]);
	}
	
	
	/**
	 * Checks the ftp connection.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionCheckFtpConnection()
	{
		$protocol = $this->_getPostData('ftp-protocol');
		$server   = $this->_getPostData('ftp-server');
		$login    = $this->_getPostData('ftp-login');
		$password = $this->_getPostData('ftp-password');
		$port     = $this->_getPostData('ftp-port');
		$passive  = $this->_getPostData('ftp-passive') === 'true';
		
		if(empty($protocol) || empty($server) || empty($login))
		{
			return MainFactory::create('JsonHttpControllerResponse', [
				'success' => false,
                'message' => 'Empty connection information provided.'
			]);
		}
		
		try
		{
			$this->autoUpdaterFactory->createFtpManager($protocol, $server, $login, $password, $port, $passive);
		}
		catch(AutoUpdaterException $e)
		{
			return MainFactory::create('JsonHttpControllerResponse', [
				'success' => false,
                'message' => $e->getMessage()
			]);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', [
			'success' => true,
		]);
	}
	
	
	/**
	 * Removes a theme from the shop.
	 *
	 * @return \JsonHttpControllerResponse
	 */
	public function actionUninstallTheme()
	{
		$themeName = $this->_getPostData('themeName');
		
		if (empty($themeName))
        {
            return MainFactory::create('JsonHttpControllerResponse', [
                'success' => false,
                'message' => 'Empty theme name provided.'
            ]);
        }
		
		if(!is_dir(DIR_FS_CATALOG . 'themes/' . $themeName))
		{
			return MainFactory::create('JsonHttpControllerResponse', [
				'success' => false,
                'message' => 'Theme directory not found.'
			]);
		}
		
		$themeControl = StaticGXCoreLoader::getThemeControl();
        
        foreach ($themeControl->getCurrentThemeHierarchy() as $theme) {
            if ($theme === $themeName) {
                return MainFactory::create('JsonHttpControllerResponse', [
                    'success'   => false,
                    'errorCode' => 101,
                    'message'   => 'Theme is currently active or used as parent'
                ]);
            }
        }
		
		return MainFactory::create('JsonHttpControllerResponse', [
			'success' => $this->deleteDirectory(DIR_FS_CATALOG . 'themes/' . $themeName),
            'message' => 'Could not delete theme directory.'
		]);
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
				$fileDeleted = @unlink($file);
				if($fileDeleted === false)
				{
					return false;
				}
			}
		}
		
		return @rmdir($directory);
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
