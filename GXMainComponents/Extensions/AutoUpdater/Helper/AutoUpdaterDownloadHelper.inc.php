<?php
/* --------------------------------------------------------------
   AutoUpdaterDownloadHelper.inc.php 2023-02-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterDownloadHelper
 */
class AutoUpdaterDownloadHelper extends AbstractAutoUpdaterHelper
{
	/**
	 * @var \AutoUpdaterSettings
	 */
	protected $settings;
	
	/**
	 * @var \AutoUpdaterCurlClient
	 */
	protected $curl;
	
	/**
	 * @var \AutoUpdaterFtpManager
	 */
	protected $ftpManager;
	
	/**
	 * @var \LogControl
	 */
	protected $logControl;
	
	/**
	 * @var int
	 */
	protected $lastDownloadedIndex;
	
	/**
	 * @var bool
	 */
	protected $downloadingFilesDone;
	
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
	 * @param \AutoUpdaterSettings   $settings
	 * @param \AutoUpdaterCurlClient $curl
	 * @param \LogControl            $logControl
	 */
	public function __construct(AutoUpdaterSettings $settings,
	                            AutoUpdaterCurlClient $curl,
	                            LogControl $logControl)
	{
		$this->settings    = $settings;
		$this->curl        = $curl;
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
	 * Downloads a zip archive of a given update and extracts it. If the zip extension is not available on the web
	 * server, the downloadUpdateFilesSeparately method will be used as a fallback.
	 *
	 * @param \AutoUpdaterUpdate $update
	 * @param string             $updateFilesDirectory
	 * @param int|null           $lastDownloadedIndex
	 *
	 * @throws \AutoUpdaterUpdateDownloadFailedException
	 */
	public function downloadUpdate(AutoUpdaterUpdate $update, $updateFilesDirectory, $lastDownloadedIndex = null)
	{
		$this->createDebugLog('[DownloadHelper] Download update', [
			'updateId'             => $update->id(),
			'updateName'           => $update->name('en'),
			'updateFilesDirectory' => $updateFilesDirectory,
		]);
		
		$updateFilesDirectory = str_replace('\\', '/', rtrim($updateFilesDirectory, '/'));
		
		if($lastDownloadedIndex === null)
		{
			$this->deleteDirectory($updateFilesDirectory);
			if($this->createDirectory($updateFilesDirectory, true) === false)
			{
				throw new AutoUpdaterUpdateDownloadFailedException('Directory "' . $updateFilesDirectory
				                                                   . '" could not be created.');
			}
			
			$this->curl->executeHead($update->zip(), [
                CURLOPT_HTTPHEADER => ['X-STORE-TOKEN: ' . $this->settings->getGambioStoreToken()],
                CURLOPT_TIMEOUT    => 20
            ]);
			if($this->curl->getStatusCode() === 200)
			{
				$zipFile = $updateFilesDirectory . '/' . $update->id() . '.zip';
				$this->downloadExternalFile($update->zip(), $zipFile);
				if(file_exists($zipFile) && md5_file($zipFile) === $update->zipHash())
				{
					try
					{
						$this->extractZip($zipFile, $updateFilesDirectory);
						$this->deleteFile($zipFile);
						$this->checkUnpackedFiles($update, $updateFilesDirectory);
						$this->downloadingFilesDone = true;
						$this->progress             = 1;
						
						return;
					}
					catch(AutoUpdaterCouldNotExtractUpdateZipException $e)
					{
						$this->deleteDirectory($updateFilesDirectory);
						$this->downloadingFilesDone = false;
						$this->lastDownloadedIndex  = -1;
						$this->progress             = 0;
					}
				}
			}
		}
		$this->downloadUpdateFilesSeparately($update, $updateFilesDirectory, $lastDownloadedIndex);
	}
	
	
	public function getLastDownloadedIndex()
	{
		return $this->lastDownloadedIndex;
	}
	
	
	public function downloadingFilesDone()
	{
		return $this->downloadingFilesDone;
	}
	
	
	public function getProgress()
	{
		return $this->progress;
	}
	
	
	/**
	 * Downloads all update files of a given update into a given update files directory.
	 * This method is a fallback, if the zip extension is not available on the web server.
	 *
	 * @param \AutoUpdaterUpdate $update
	 * @param string             $updateFilesDirectory
	 * @param int|null           $lastDownloadedIndex
	 *
	 * @throws \AutoUpdaterUpdateDownloadFailedException
	 */
	protected function downloadUpdateFilesSeparately(AutoUpdaterUpdate $update,
	                                                 $updateFilesDirectory,
	                                                 $lastDownloadedIndex = null)
	{
		$this->createDebugLog('[DownloadHelper] Download update files separately', [
			'updateId'             => $update->id(),
			'updateName'           => $update->name('en'),
			'updateFilesDirectory' => $updateFilesDirectory,
			'lastDownloadedIndex'  => $lastDownloadedIndex,
		]);
		
		$startTime                  = time();
		$updateFilesDirectory       = str_replace('\\', '/', rtrim($updateFilesDirectory, '/'));
		$this->lastDownloadedIndex  = $lastDownloadedIndex;
		$this->downloadingFilesDone = true;
		
		if($this->lastDownloadedIndex === null)
		{
			if($this->createDirectory($updateFilesDirectory, true) === false)
			{
				throw new AutoUpdaterUpdateDownloadFailedException('Directory "' . $updateFilesDirectory
				                                                   . '" could not be created.');
			}
			if(count($update->fileList()) === 0)
			{
				$this->deleteDirectory($updateFilesDirectory);
				throw new AutoUpdaterUpdateDownloadFailedException('Can not download the update files of update "'
				                                                   . $update->name('en') . '".');
			}
		}
		
		foreach($update->fileList() as $index => $updateFile)
		{
			if($index <= $this->lastDownloadedIndex && $this->lastDownloadedIndex !== null)
			{
				continue;
			}
			
			$this->curl->executeHead($updateFile['source'], [
                CURLOPT_HTTPHEADER     => [
                    'X-STORE-TOKEN: ' . $this->settings->getGambioStoreToken()
                ]]);
			$statusCode = $this->curl->getStatusCode();
			$curlErrorMsg = $this->curl->getError();
            if(!empty($curlErrorMsg))
            {
                $this->createDebugLog('[downloadUpdateFilesSeparately] CURL ERROR: ', [
                    'message'             => $curlErrorMsg,
                    'url' => $updateFile['source']
                ]);
            }
            
			if($statusCode !== 200 && $statusCode !== 204)
			{
				$this->deleteDirectory($updateFilesDirectory);
                throw new AutoUpdaterUpdateDownloadFailedException(' Could not download the update file "'
                                                                   . basename($updateFile['source']) . '" of update "'
                                                                   . $update->name('en') . '".');
			}
			else
			{
				$destinationFile = $updateFilesDirectory . '/' . $updateFile['destination'];
				$destinationDir  = dirname($destinationFile);
				
				if($this->createDirectory($destinationDir, true) === false)
				{
					$this->deleteDirectory($updateFilesDirectory);
					throw new AutoUpdaterUpdateDownloadFailedException('Could not create directory "' . $destinationDir
					                                                   . '" for update "' . $update->name('en') . '".');
				}
				$this->downloadExternalFile($updateFile['source'], $destinationFile);
				
				if(!file_exists($destinationFile) || md5_file($destinationFile) !== $updateFile['hash'])
				{
					$this->deleteDirectory($updateFilesDirectory);
					throw new AutoUpdaterUpdateDownloadFailedException('Wrong hash for "' . $updateFile['destination']
					                                                   . '" of update "' . $update->name('en') . '".');
				}
			}
			$this->lastDownloadedIndex = $index;
			if($startTime < time() - $this->ajaxTimeout = 10)
			{
				$this->downloadingFilesDone = false;
				break;
			}
		}
		$this->progress = $this->lastDownloadedIndex / (count($update->fileList()) - 1);
	}
	
	
	/**
	 * Downloads an external file with curl.
	 *
	 * @param string $url
	 * @param string $destination
	 */
	protected function downloadExternalFile($url, $destination)
	{
		$downloadedFile = fopen($destination, 'w');
		$this->curl->executeGet($url, [
            CURLOPT_HTTPHEADER     => [
                'X-STORE-TOKEN: ' . $this->settings->getGambioStoreToken()
            ],
			CURLOPT_RETURNTRANSFER => false,
			CURLOPT_FILE           => $downloadedFile,
			CURLOPT_TIMEOUT        => 20
		]);
        
        $curlErrorMsg = $this->curl->getError();
        if(!empty($curlErrorMsg))
        {
            $this->createDebugLog('[downloadExternalFile] CURL ERROR: ', [
                'message'             => $curlErrorMsg,
                'url' => $url
            ]);
        }
		
		@fclose($downloadedFile);
		$this->setCorrectChmodAndOwner($destination);
	}
	
	
	/**
	 * Extracts a given zip file to a given directory
	 *
	 * @param string $zipFile
	 * @param string $destination
	 *
	 * @throws \AutoUpdaterCouldNotExtractUpdateZipException
	 */
	protected function extractZip($zipFile, $destination)
	{
		$zip          = new \ZipArchive;
		$zipOpen      = @$zip->open($zipFile);
		$zipExtracted = @$zip->extractTo($destination);
		$zipClosed    = @$zip->close();
		if($zipOpen !== true || $zipExtracted === false || $zipClosed === false)
		{
			$this->deleteDirectory($destination);
			throw new AutoUpdaterCouldNotExtractUpdateZipException();
		}
	}
	
	
	/**
	 * Checks the unpacked files.
	 *
	 * @param \AutoUpdaterUpdate $update
	 * @param string             $updateFilesDirectory
	 *
	 * @throws \AutoUpdaterCouldNotExtractUpdateZipException If unpacked file is not ok.
	 */
	protected function checkUnpackedFiles(AutoUpdaterUpdate $update, $updateFilesDirectory)
	{
		foreach($update->fileList() as $updateFile)
		{
			if(!file_exists($updateFilesDirectory . '/' . $updateFile['destination'])
			   || $updateFile['hash'] !== md5_file($updateFilesDirectory . '/' . $updateFile['destination']))
			{
				throw new AutoUpdaterCouldNotExtractUpdateZipException();
			}
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
		if(is_array($additionalData))
		{
			$additionalData = json_encode($additionalData);
		}
		
		$this->logControl->notice($message, '', 'auto_updater', 'notice', 'USER NOTICE', 0, $additionalData);
	}
}
