<?php
/* --------------------------------------------------------------
   AutoUpdaterUpdatesHelper.inc.php 2023-02-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AutoUpdaterUpdatesHelper
 */
class AutoUpdaterUpdatesHelper extends AbstractAutoUpdaterHelper
{
    /**
     * @var \AutoUpdaterSettings
     */
    protected $settings;

    /**
     * @var \CI_DB_query_builder
     */
    private $db;

    /**
     * @var \DataCache
     */
    private $dataCache;

    /**
     * @var \AutoUpdaterCurlClient
     */
    private $curl;

    /**
     * @var array
     */
    private $updateStatus;

    /**
     * @var array
     */
    private $availableUpdates;

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
    protected $copyingFilesIntoShopDone;

    /**
     * @var int|null
     */
    protected $lastCopiedIndex;


    /**
     * @var float
     */
    protected $progress;

    /**
     * @var int
     */
    protected $ajaxTimeout;

    /**
     * @var int
     */
    protected $updateStatusLifetime;


    /**
     * AutoUpdaterUpdatesChecker constructor.
     *
     * @param \AutoUpdaterSettings $settings
     * @param \CI_DB_query_builder $db
     * @param \DataCache $dataCache
     * @param \AutoUpdaterCurlClient $curl
     * @param \LogControl $logControl
     */
    public function __construct(AutoUpdaterSettings $settings,
                                CI_DB_query_builder $db,
                                DataCache $dataCache,
                                AutoUpdaterCurlClient $curl,
                                LogControl $logControl)
    {
        $this->settings = $settings;
        $this->db = $db;
        $this->dataCache = $dataCache;
        $this->curl = $curl;
        $this->ftpManager = null;
        $this->logControl = $logControl;
        $this->ajaxTimeout = 10;
        $this->updateStatusLifetime = 60 * 5;
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
     * Checks the writing permissions of all needed directories for an update by a given update files directory.
     *
     * @param string $updateFilesDirectory
     *
     * @return array
     */
    public function checkFilesPermissions($updateFilesDirectory)
    {
        if ($this->ftpManager !== null) {
            return [];
        }

        $wrongPermittedFiles = [];
        $checkDirectories = [];
        $updateFiles = $this->getDirectoryFiles($updateFilesDirectory);

        foreach ($updateFiles as $updateFile) {
            $shopFile = $this->changeFileBasePath($updateFile, $updateFilesDirectory,
                $this->settings->shopBaseDirectory());
            $shopTestFile = $shopFile . '.permission_check';
            $shopTestDir = dirname($shopTestFile);

            if (in_array($shopTestDir, $checkDirectories)) {
                continue;
            }
            $checkDirectories[] = $shopTestDir;

            if (((!file_exists($shopTestDir) || !is_dir($shopTestDir)) && $this->createDirectory($shopTestDir) === false)
                || (file_exists($shopTestDir) && is_dir($shopTestDir) && !is_writeable($shopTestDir))) {
                $wrongPermittedFiles[] = $shopTestDir;
                continue;
            }

            $fileOpen = @fopen($shopTestFile, 'w');
            $fileWritten = @fwrite($fileOpen, 'permission test');
            $fileClosed = @fclose($fileOpen);
            $this->deleteFile($shopTestFile);
            if ($fileOpen === false || $fileWritten === false || $fileClosed === false
                || (file_exists($shopFile)
                    && !is_writable($shopFile)
                    && !is_writable($shopTestDir))) {
                $wrongPermittedFiles[] = $shopTestDir;
            }
        }

        $this->createDebugLog('[UpdateHelper] Check file permissions for update directory', [
            'updateFilesDirectory' => $updateFilesDirectory,
            'wrongPermittedFiles' => $wrongPermittedFiles,
        ]);

        return $wrongPermittedFiles;
    }


    /**
     * Checks the writing permissions of all needed directories for an update by a given update files directory.
     *
     * @param string $fileList
     *
     * @return array
     */
    public function checkFilesPermissionsWithFileList($fileList)
    {
        $wrongPermittedFiles = [];
        $checkDirectories = [];

        foreach ($fileList as $shopFile) {
            $shopTestFile = $shopFile . '.permission_check';
            $shopTestDir = dirname($shopTestFile);

            if (in_array($shopTestDir, $checkDirectories)) {
                continue;
            }
            $checkDirectories[] = $shopTestDir;

            if (((!file_exists($shopTestDir) || !is_dir($shopTestDir)) && $this->createDirectory($shopTestDir) === false)
                || (file_exists($shopTestDir) && is_dir($shopTestDir) && !is_writeable($shopTestDir))) {
                $wrongPermittedFiles[] = $shopTestDir;
                continue;
            }

            $fileOpen = @fopen($shopTestFile, 'w');
            $fileWritten = @fwrite($fileOpen, 'permission test');
            $fileClosed = @fclose($fileOpen);

            $this->deleteFile($shopTestFile);
            if ($fileOpen === false || $fileWritten === false || $fileClosed === false
                || (file_exists($shopFile)
                    && !is_writable($shopFile)
                    && !is_writable($shopTestDir))) {
                $wrongPermittedFiles[] = $shopTestDir;
            }
        }

        $this->createDebugLog('[UpdateHelper] Check file permissions for a file list', [
            'fileList' => $fileList,
            'wrongPermittedFiles' => $wrongPermittedFiles,
        ]);

        return $wrongPermittedFiles;
    }


    /**
     * Copies the update files into the shop.
     *
     * @param string $updateFilesDirectory
     * @param int|null $lastCopiedIndex
     *
     * @return int|null
     *
     * @throws \AutoUpdaterMovingUpdateFilesFailedException
     */
    public function copyFilesIntoShop($updateFilesDirectory, $lastCopiedIndex = null)
    {
        $this->createDebugLog('[UpdateHelper] Copy update files into shop', [
            'updateFilesDirectory' => $updateFilesDirectory,
            'lastCopiedIndex' => $lastCopiedIndex,
        ]);

        $startTime = time();
        $updateFiles = $this->getDirectoryFiles($updateFilesDirectory);
        $this->copyingFilesIntoShopDone = true;
        $this->lastCopiedIndex = $lastCopiedIndex;
        foreach ($updateFiles as $index => $updateFile) {
            if ($index <= $this->lastCopiedIndex && $this->lastCopiedIndex !== null) {
                continue;
            }

            $shopFile = $this->changeFileBasePath($updateFile, $updateFilesDirectory,
                $this->settings->shopBaseDirectory());
            $shopDir = dirname($shopFile);

            $this->createDirectory($shopDir);
            $this->deleteFile($shopFile);
            $fileCopied = $this->copyFile($updateFile, $shopFile);
            $this->setCorrectChmodAndOwner($shopFile);

            if ($fileCopied === false || filesize($updateFile) !== filesize($shopFile)) {
                throw new AutoUpdaterMovingUpdateFilesFailedException('Could not move update file "' . $updateFile
                    . '" to "' . $shopFile . '".');
            }

            $this->lastCopiedIndex = $index;
            if ($startTime < time() - $this->ajaxTimeout) {
                $this->copyingFilesIntoShopDone = false;
                break;
            }
        }
        $this->progress = $this->lastCopiedIndex / (count($updateFiles) - 1);
    }


    /**
     * @return mixed
     */
    public function copyingFilesIntoShopDone()
    {
        return $this->copyingFilesIntoShopDone;
    }


    /**
     * @return mixed
     */
    public function getLastCopiedIndex()
    {
        return $this->lastCopiedIndex;
    }


    /**
     * @return float
     */
    public function getProgress()
    {
        return $this->progress;
    }


    /**
     * Returns the update check response of the update server.
     *
     * @throws \AutoUpdaterException If connection to update server failed.
     */
    public function getUpdateCheckResponse()
    {
        if ($this->updateStatus === null) {
            if ($this->dataCache->key_exists('auto-updater-update-status', true)) {
                $updateCheck = $this->dataCache->get_data('auto-updater-update-status', true);
                if ($updateCheck['lastCheck'] >= time() - $this->updateStatusLifetime) {
                    $this->updateStatus = $updateCheck['response'];

                    return $this->updateStatus;
                }
            }

            $this->createDebugLog('[UpdateHelper] Check update server for available updates');
            $this->curl->executePost($this->settings->updateServerUrl(), $this->collectShopInformation(), [
                CURLOPT_HTTPHEADER => ['X-STORE-TOKEN: ' . $this->settings->getGambioStoreToken()],
                CURLOPT_TIMEOUT    => 20
            ]);
            if ($this->curl->getStatusCode() !== 200) {
                throw new AutoUpdaterException('Could not connect to update server.');
            }
            $this->updateStatus = json_decode($this->curl->getContent(), true);
            $this->dataCache->set_data('auto-updater-update-status', [
                'lastCheck' => time(),
                'response' => $this->updateStatus,
            ], true);
        }

        return $this->updateStatus;
    }


    /**
     * Returns a list of all available updates as an array.
     *
     * @return array
     *
     * @throws \AutoUpdaterException If connection to update server failed.
     *
     * @deprecated
     */
    public function getAvailableUpdates()
    {
        if ($this->availableUpdates === null) {
            $this->availableUpdates = [];

            if (isset($this->getUpdateCheckResponse()['updates'])) {
                foreach ($this->getUpdateCheckResponse()['updates'] as $update) {
                    $this->availableUpdates[] = AutoUpdaterUpdate::createByUpdateServerResponse($update);
                }
            }
        }

        return $this->availableUpdates;
    }


    /**
     * Checks if updates are available.
     *
     * @return bool True, if updates are available, otherwise false.
     *
     * @throws \AutoUpdaterException If connection to update server failed.
     */
    public function areUpdatesAvailable()
    {
        return count($this->getUpdateCheckResponse()['updates']) > 0;
    }


    /**
     * Checks if there are downloaded update, that not have been installed.
     *
     * @return bool True, if uninstalled downloaded updates are available, otherwise false.
     */
    public function areUninstalledUpdatesAvailable()
    {
        $this->createDebugLog('[UpdateHelper] Check for uninstalled updates');

        $return = false;

        if ($this->dataCache->key_exists('auto-updater-downloaded-updates', true)) {
            $downloadedUpdates = $this->dataCache->get_data('auto-updater-downloaded-updates', true);
            foreach ($downloadedUpdates as $index => $update) {
                $receiptFilePath = DIR_FS_CATALOG . 'version_info/' . $update['receiptFile'];
                if (file_exists($receiptFilePath)) {
                    $updateName = $this->determineUpdateName($receiptFilePath);
                    if (!empty($updateName) && !$this->hasUpdateBeenInstalled($updateName)) {
                        $return = true;
                    } else {
                        unset($downloadedUpdates[$index]);
                    }
                }
            }

            $this->dataCache->set_data('auto-updater-downloaded-updates', $downloadedUpdates, true);
        }

        return $return;
    }


    /**
     * Removes the version history entry of an update by a given version history name.
     *
     * @param $versionHistoryName string
     */
    public function resetVersionHistoryName($versionHistoryName)
    {
        $this->createDebugLog('[UpdateHelper] Delete old version history entry', [
            'versionHistoryName' => $versionHistoryName,
        ]);

        $this->db->delete('version_history', ['name' => $versionHistoryName]);
    }


    /**
     * Marks the admin infobox message with the given identifier as deleted.
     *
     * @param $infoboxIdentifier string
     */
    public function deleteInfoboxMessage($infoboxIdentifier)
    {
        $this->createDebugLog('[UpdateHelper] Mark admin infobox message as deleted', [
            'infoboxIdentifier' => $infoboxIdentifier,
        ]);

        if (!empty($infoboxIdentifier)) {
            $this->db->update('infobox_messages', ['status' => 'deleted'], ['identifier' => $infoboxIdentifier]);
        }
    }


    /**
     * Deletes a given update files directory.
     *
     * @param string $updateFilesDirectory
     *
     * @return bool
     */
    public function deleteUpdateDirectory($updateFilesDirectory)
    {
        $this->createDebugLog('[UpdateHelper] Delete downloaded update files', [
            'updateFilesDirectory' => $updateFilesDirectory,
        ]);

        return $this->deleteDirectory($updateFilesDirectory);
    }


    /**
     * Deletes the updates directory. Return true on success and false otherwise.
     *
     * @return bool
     */
    public function deleteUpdatesDirectory()
    {
        $this->createDebugLog('[UpdateHelper] Delete all downloaded update files');

        return $this->deleteDirectory($this->settings->updatesDirectory());
    }


    /**
     * @param string $updateId
     *
     * @return mixed
     */
    public function getProcessingStatus($updateId)
    {
        if (!$this->dataCache->key_exists('auto-update-processing-status', true)) {
            $status = [
                'state' => 'start',
                'backupId' => null,
                'lastIndex' => null,
                'newUpdateFiles' => null,
                'exception' => null,
            ];

            $this->dataCache->set_data('auto-update-processing-status', [$updateId => $status], true);

            return $status;
        }

        $statusData = $this->dataCache->get_data('auto-update-processing-status', true);
        if (!isset($statusData[$updateId])) {
            $statusData[$updateId] = [
                'state' => 'start',
                'backupId' => null,
                'lastIndex' => null,
                'newUpdateFiles' => null,
                'exception' => null,
            ];
            $this->dataCache->set_data('auto-update-processing-status', $statusData, true);
        }

        return $statusData[$updateId];
    }


    /**
     * @param string $updateId
     *
     * @return mixed
     */
    public function setProcessingStatus($updateId, $status)
    {
        if (!$this->dataCache->key_exists('auto-update-processing-status', true)) {
            $this->dataCache->set_data('auto-update-processing-status', [$updateId => $status], true);

            return;
        }

        $statusData = $this->dataCache->get_data('auto-update-processing-status', true);
        $statusData[$updateId] = $status;
        $this->dataCache->set_data('auto-update-processing-status', $statusData, true);
    }


    /**
     * Calls home and sends some feedback about the update processing
     *
     * @param string $message
     * @param string $selectedUpdate
     * @param array $wrongPermittedFiles
     * @param \Exception|null $exception
     */
    public function callHome($message,
                             $selectedUpdate = '',
                             $wrongPermittedFiles = [],
                             \Exception $exception = null)
    {
        if ($exception !== null) {
            $exception = [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ];
        }

        $feedback = [
            'message' => $message,
            'shopVersion' => $this->determineShopVersion(),
            'shopUrl' => HTTP_SERVER . DIR_WS_CATALOG,
            'shopKey' => $this->determineShopKey(),
            'selectedUpdates' => $selectedUpdate,
            'notPermittedFiles' => json_encode($wrongPermittedFiles),
            'exception' => json_encode($exception),
        ];

        $this->curl->executePost($this->settings->feedbackServerUrl(), $feedback);
    }


    /**
     * Collects all shop information that are needed for the update server.
     *
     * @return array
     */
    protected function collectShopInformation()
    {
        return [
            'shopVersion' => $this->determineShopVersion(),
            'shopUrl' => HTTP_SERVER . DIR_WS_CATALOG,
            'shopKey' => $this->determineShopKey(),
            'versionHistory' => json_encode($this->determineInstalledUpdates()),
            'versionReceipts' => json_encode($this->determineExistingReceiptFiles()),
            'downloadedUpdates' => json_encode($this->determineDownloadedUpdates()),
        ];
    }


    /**
     * Returns the shop key.
     *
     * @return mixed
     */
    protected function determineShopVersion()
    {
        $shopKey = $this->db->select('value')
            ->from('gx_configurations')
            ->where('key', 'gm_configuration/INSTALLED_VERSION')
            ->get()
            ->row();

        if (isset($shopKey->gm_value)) {
            return $shopKey->gm_value;
        }

        return null;
    }


    /**
     * Returns the shop key.
     *
     * @return mixed
     */
    protected function determineShopKey()
    {
        $shopKey = $this->db->select('configuration_value')
            ->from('configuration')
            ->where('configuration_key', 'GAMBIO_SHOP_KEY')
            ->get()
            ->row();
        if (isset($shopKey->configuration_value)) {
            return $shopKey->configuration_value;
        }

        return null;
    }


    /**
     * Returns a db dump of the version history table as an array.
     *
     * @return mixed
     */
    protected function determineInstalledUpdates()
    {
        $return = [];

        $installedUpdates = (array)$this->db->select()
            ->from('version_history')
            ->order_by('history_id', 'DESC')
            ->get()
            ->result_array();
        foreach ($installedUpdates as $installedUpdate) {
            $return[] = $installedUpdate;
            if ($installedUpdate['type'] === 'master_update' && substr($installedUpdate['name'], 0, 1) === 'v') {
                break;
            }
        }

        return $return;
    }


    /**
     * Returns the version receipts from filesystem.
     *
     * @return mixed
     */
    protected function determineExistingReceiptFiles()
    {
        return scandir(DIR_FS_CATALOG . 'version_info');
    }


    /**
     * Returns the shop key from database.
     *
     * @return mixed
     */
    protected function determineDownloadedUpdates()
    {
        $downloadedUpdates = [];
        if ($this->dataCache->key_exists('auto-updater-downloaded-updates', true)) {
            $cachedDownloadedUpdates = $this->dataCache->get_data('auto-updater-downloaded-updates', true);
            foreach ($cachedDownloadedUpdates as $cachedDownloadedUpdate) {
                $downloadedUpdates[] = $cachedDownloadedUpdate['receiptFile'];
            }
        }

        return $downloadedUpdates;
    }


    /**
     * Determines the version of the version info items while considering known edge cases.
     *
     * @param string $receiptFilePath
     *
     * @return string $version
     */
    protected function determineUpdateName($receiptFilePath)
    {
        $updateName = '';
        $receiptFile = @fopen($receiptFilePath, 'r');
        while ($line = @fgets($receiptFile)) {
            if (strpos($line, 'version:') === 0) {
                $updateName = trim(substr($line, 8));

                break;
            }
        }
        @fclose($receiptFile);

        return $updateName;
    }


    /**
     * Checks the version history database table if the given version is installed.
     *
     * @return bool Returns true, if the given version is installed, otherwise false.
     */
    protected function hasUpdateBeenInstalled($updateName)
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $installed = $db->select()
                ->from('version_history')
                ->where(['name' => $updateName])
                ->or_where([
                    'name' => 'v' . $updateName
                ])
                ->get()
                ->num_rows() > 0;

        return $installed;
    }


    /**
     * Creates a new debug log.
     *
     * @param string $message
     * @param string|array $additionalData
     */
    protected function createDebugLog($message, $additionalData = '')
    {
        if (is_array($additionalData)) {
            $additionalData = json_encode($additionalData);
        }

        $this->logControl->notice($message, '', 'auto_updater', 'notice', 'USER NOTICE', 0, $additionalData);
    }
}
