<?php
/* --------------------------------------------------------------
   AutoUpdaterSettings.inc.php 2018-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterSettings
 */
class AutoUpdaterSettings
{
	/**
	 * @var \AutoUpdaterCurlClient
	 */
	protected $curl;
	
	/**
	 * @var \CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * @var bool
	 */
	protected $dataProcessingAccepted;
    
    /**
     * @var string
     */
    protected $gambioStoreToken;
    
	/**
	 * @var string
	 */
	protected $shopBaseDirectory;
	
	/**
	 * @var string
	 */
	protected $backupsDirectory;
	
	/**
	 * @var string
	 */
	protected $updatesDirectory;
	
	/**
	 * @var string
	 */
	protected $updateServerUrl;
	
	/**
	 * @var string
	 */
	protected $feedbackServerUrl;
	
	/**
	 * @var int
	 */
	protected $writingPermissionValue;
	
	
	/**
	 * AutoUpdaterSettings constructor.
	 *
	 * @param \AutoUpdaterCurlClient $curl
	 */
	public function __construct(AutoUpdaterCurlClient $curl, \CI_DB_query_builder $db)
	{
		$this->curl = $curl;
		$this->db   = $db;
	}
	
	
	/**
	 * Return the data processing accepting flag.
	 *
	 * @return bool True, if an admin accepted the data processing, otherwise false.
	 */
	public function dataProcessingAccepted()
	{
		if($this->dataProcessingAccepted === null)
		{
			$this->dataProcessingAccepted = $this->getGmConf('AUTO_UPDATER_ACCEPT_DATA_PROCESSING') !== 'true';
		}
		
		return $this->dataProcessingAccepted;
	}
    
    /**
     * Return the Gambio Store Token
     *
     * @return string
     */
    public function getGambioStoreToken()
    {
        if ($this->gambioStoreToken === null) {
            $this->gambioStoreToken = $this->getGmConf('GAMBIO_STORE_TOKEN');
        }
        
        return $this->gambioStoreToken;
    }
	
	
	/**
	 * Returns the base directory path of the shop.
	 *
	 * @return string
	 */
	public function shopBaseDirectory()
	{
		if($this->shopBaseDirectory === null)
		{
			$this->shopBaseDirectory = DIR_FS_CATALOG;
		}
		
		return $this->shopBaseDirectory;
	}
	
	
	/**
	 * Returns the directory path for the backups to be created.
	 *
	 * @return string
	 */
	public function backupsDirectory()
	{
		if($this->backupsDirectory === null)
		{
			$this->backupsDirectory = str_replace('\\', '/', DIR_FS_CATALOG . 'admin/backups/auto_updater');
		}
		
		return $this->backupsDirectory;
	}
	
	
	/**
	 * Returns the directory path for the updates to be downloaded before moved into the shop.
	 *
	 * @return string
	 */
	public function updatesDirectory()
	{
		if($this->updatesDirectory === null)
		{
			$this->updatesDirectory = str_replace('\\', '/', DIR_FS_CATALOG . 'cache/auto_updater/updates');
		}
		
		return $this->updatesDirectory;
	}
	
	
	/**
	 * Returns the url of the update server.
	 *
	 * @return string
	 */
	public function updateServerUrl()
	{
		if($this->updateServerUrl === null)
		{
			$this->updateServerUrl = $this->determineUpdateServerUrl();
		}
		
		return $this->updateServerUrl;
	}
	
	
	/**
	 * Returns the url of the update server.
	 *
	 * @return string
	 */
	public function feedbackServerUrl()
	{
		if($this->feedbackServerUrl === null)
		{
			$this->feedbackServerUrl = $this->determineFeedbackServerUrl();
		}
		
		return $this->feedbackServerUrl;
	}
	
	
	/**
	 * Returns the correct chmod value for writing permission.
	 *
	 * @return int
	 */
	public function writingPermissionValue()
	{
		if($this->writingPermissionValue === null)
		{
			$this->writingPermissionValue = $this->determineCorrectFilePermission();
		}
		
		return $this->writingPermissionValue;
	}
	
	
	/**
	 * Returns the correct chmod value for normal file permission.
	 *
	 * @return int
	 */
	public function normalFilePermissionValue()
	{
		return 0644;
	}
	
	
	/**
	 * Returns the correct chmod value for normal directory permission.
	 *
	 * @return int
	 */
	public function normalDirectoryPermissionValue()
	{
		return 0711;
	}
	
	
	/**
	 * Determines the url of the update server.
	 *
	 * @return int
	 */
	protected function determineUpdateServerUrl()
	{
		$url = $this->getGmConf('AUTO_UPDATER_UPDATES_URL');
		if(empty($url))
		{
			$url = 'https://updates.gambio-support.de/v2/check.php';
		}
		
		return $url;
	}
	
	
	/**
	 * Determines the url of the feedback server.
	 *
	 * @return int
	 */
	protected function determineFeedbackServerUrl()
	{
		$url = $this->getGmConf('AUTO_UPDATER_FEEDBACK_URL');
		if(empty($url))
		{
			$url = 'https://updates.gambio-support.de/v2/callingHome.php';
		}
		
		return $url;
	}
	
	
	/**
	 * Determines the needed file permissions for writing operations.
	 *
	 * @return int
	 */
	protected function determineCorrectFilePermission()
	{
		$permissionValue = 0777;
		
		if(is_writeable(DIR_FS_CATALOG . 'export'))
		{
			$testFile1 = 'export/permission-test.php';
			$testFile2 = 'export/permission-test2.php';
			
			if (is_file(DIR_FS_CATALOG . $testFile1)) {
                @unlink(DIR_FS_CATALOG . $testFile1);
            }
			if (is_file(DIR_FS_CATALOG . $testFile2)) {
                @unlink(DIR_FS_CATALOG . $testFile2);
            }
			$this->createTestFile(DIR_FS_CATALOG . $testFile1, 0777);
			$this->createTestFile(DIR_FS_CATALOG . $testFile2, 0755);
			
			if($this->isAccessible(HTTP_SERVER . DIR_WS_CATALOG . $testFile1) === false
			   && $this->isAccessible(HTTP_SERVER . DIR_WS_CATALOG . $testFile2)
			   && is_writeable(DIR_FS_CATALOG . $testFile2))
			{
				$permissionValue = 0755;
			}
			
			@unlink(DIR_FS_CATALOG . $testFile1);
			@unlink(DIR_FS_CATALOG . $testFile2);
		}
		
		return $permissionValue;
	}
	
	
	/**
	 * Creates a test file for the permission check.
	 *
	 * @param string $path
	 * @param int    $chmod
	 */
	protected function createTestFile($path, $chmod)
	{
		$file = @fopen($path, 'w');
		@fwrite($file, '<?php echo "test ok";');
		@fclose($file);
		@chmod($path, $chmod);
	}
	
	
	/**
	 * Checks the accessibility of a file by using curl.
	 *
	 * @param string $url URL to the file.
	 *
	 * @return bool True, if the file is accessible by curl, otherwise false.
	 */
	protected function isAccessible($url)
	{
		$this->curl->executeGet($url);
		
		return $this->curl->getStatusCode() === 200 && $this->curl->getContent() === 'test ok';
	}
	
	
	/**
	 * Returns a configuration value for a given configuration key.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	protected function getGmConf($key)
	{
		$result = $this->db->select('value')->from('gx_configurations')->where('key', 'gm_configuration/' . $key)->get()->row();
		
		if(isset($result->value))
		{
			return $result->value;
		}
		
		return null;
	}
}
