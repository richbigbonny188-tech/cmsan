<?php
/* --------------------------------------------------------------
  FilterCache.php 2023-01-27
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

/**
 * Class FilterCache
 */
class FilterCache
{
    /**
     * @var string $cachedFilterRulesPath
     */
    private $cachedFilterRulesPath;

    /**
     * @var string
     */
    private $metaDataPath;

    /**
     * @var MetaData|null
     */
    private $metaData;

    /**
     * @var int|null
     */
    private $remoteFilterModificationUnixTime;

    /**
     * @var FilterReader $filterReader
     */
    private $filterReader;


    /**
     * FilterCache constructor.
     */
    public function __construct()
    {
        $this->cachedFilterRulesPath = GAMBIO_PROTECTOR_CACHE_DIR . GAMBIO_PROTECTOR_CACHE_FILERULES_FILENAME;
        $this->metaDataPath          = GAMBIO_PROTECTOR_CACHE_DIR . 'meta_data.cache';
        $this->filterReader          = new FilterReader();
    }


    /**
     * Reads the content of the Cached FilterRule file and if it contains valid JSON, then return that content,
     * otherwise return the content of the Fallback FilterRules file.
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getCachedFilterRules()
    {
        $cachedFilterRules = $this->readFile(GAMBIO_PROTECTOR_CACHE_DIR . GAMBIO_PROTECTOR_CACHE_FILERULES_FILENAME);

        if ($this->isJsonValid($cachedFilterRules)) {
            return json_decode($cachedFilterRules, true);
        } else {
            return $this->filterReader->getFallbackFilterRules();
        }
    }


    /**
     * If the Cached FilterRule file does not exists or is older than 8 hours, Download and recreate the file.
     *
     */
    public function renew()
    {
        if ($this->getLastUpdateCheckUnixTime() === null
            || $this->getLastUpdateCheckUnixTime() + GAMBIO_PROTECTOR_CACHE_RENEW_INTERVAL < time()) {
            if ($this->isCacheOlderThanRemoteFile()) {
                $this->createRemoteRulesCacheFile();
            } else {
                $this->writeMetaDataFile(serialize(new MetaData($this->getModificationDateUnixTime(), time())));
            }
        }
    }


    /**
     * Checks if given string is a valid JSON string containing filter rules.
     *
     * @param $jsonString
     *
     * @return bool
     */
    private function isJsonValid($jsonString)
    {
        $filterRules = json_decode($jsonString, true);

        if (!is_array($filterRules) || json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        foreach ($filterRules as $rule) {
            if (!isset($rule['key'], $rule['script_name'], $rule['variables'], $rule['function'], $rule['severity'])
                || !is_string($rule['key'])
                || (!is_string($rule['script_name'])
                    && !is_array($rule['script_name']))
                || !is_array($rule['variables'])
                || !is_string($rule['function'])
                || !is_string($rule['severity'])) {
                return false;
            }

            if (is_array($rule['script_name'])) {
                foreach ($rule['script_name'] as $scriptName) {
                    if (!is_string($scriptName)) {
                        return false;
                    }
                }
            }

            foreach ($rule['variables'] as $variable) {
                if (!isset($variable['type'], $variable['property']) || !is_string($variable['type'])
                    || (!is_string($variable['property'])
                        && !is_array($variable['property']))
                    || (isset($variable['subcategory']) && !is_string($variable['subcategory']))) {
                    return false;
                }

                if (is_array($variable['property'])) {
                    foreach ($variable['property'] as $property) {
                        if (!is_string($property)) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }


    /**
     * Checks if the Cache File is older than the Remote FilterRules file
     *
     * @return bool
     */
    private function isCacheOlderThanRemoteFile()
    {
        $connection = curl_init();
        $timeout    = 5;

        curl_setopt($connection, CURLOPT_URL, GAMBIO_PROTECTOR_REMOTE_FILTERRULES_URL);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_HEADER, true);
        curl_setopt($connection, CURLOPT_FILETIME, true);
        curl_setopt($connection, CURLOPT_NOBODY, true);
        curl_setopt($connection, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($connection, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($connection, CURLOPT_MAXREDIRS, 10);

        curl_exec($connection); // needed for curl_errno check
        $headers = curl_getinfo($connection);

        if (!isset($headers['http_code'], $headers['content_type'])
            || $headers['http_code'] !== 200
            || $headers['content_type'] !== 'application/json'
            || curl_errno($connection)) {
            return false;
        }

        curl_close($connection);

        $this->remoteFilterModificationUnixTime = (int)$headers['filetime'];

        if ($this->getModificationDateUnixTime() === null
            || $this->remoteFilterModificationUnixTime > $this->getModificationDateUnixTime()) {
            return true;
        }

        return false;
    }


    /**
     * Checks if the remote FilterRules are Valid JSON and if it is, delete the old cache file and create a new
     * one with the received FilterRules.
     */
    private function createRemoteRulesCacheFile()
    {
        $remoteRules = $this->getRemoteRules();
        if ($this->isJsonValid($remoteRules)) {
            $success = $this->writeCacheFile($remoteRules);
            if ($success !== false) {
                $this->writeMetaDataFile(serialize(new MetaData($this->remoteFilterModificationUnixTime, time())));
            }
        }
    }


    /**
     * If a given file exists, delete it.
     *
     * @param string $filePath
     */
    private function deleteFile($filePath)
    {
        clearstatcache(true);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }


    /**
     * Writes the filter rules cache file
     *
     * @param string $fileContent
     *
     * @return int|bool
     */
    private function writeCacheFile($fileContent)
    {
        if (!is_writable(dirname($this->cachedFilterRulesPath))) {
            return false;
        }
        
        $this->deleteFile($this->cachedFilterRulesPath);
        
        return file_put_contents($this->cachedFilterRulesPath, $fileContent);
    }


    /**
     * Writes the meta data cache file
     *
     * @param string $fileContent
     *
     * @return int|bool
     */
    private function writeMetaDataFile($fileContent)
    {
        if (!is_writable(dirname($this->metaDataPath))) {
            return false;
        }
        
        $this->deleteFile($this->metaDataPath);

        return file_put_contents($this->metaDataPath, $fileContent);
    }


    /**
     * If a given file exists and is readable, return the file content.
     *
     * @param string $path
     *
     * @return false|string
     * @throws InvalidArgumentException
     */
    private function readFile($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException('Filter Rules file not found');
        }

        if (!is_readable($path)) {
            throw new InvalidArgumentException('Filter Rules file not readable');
        }

        return file_get_contents($path);
    }


    /**
     * Returns the remote FilterRule content via a HTTP GET Request.
     *
     * @return bool|string
     */
    private function getRemoteRules()
    {
        $connection = curl_init();
        $timeout    = 5;

        curl_setopt($connection, CURLOPT_URL, GAMBIO_PROTECTOR_REMOTE_FILTERRULES_URL);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_FILETIME, true);
        curl_setopt($connection, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($connection, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($connection, CURLOPT_MAXFILESIZE, 2000000);
        curl_setopt($connection, CURLOPT_MAXREDIRS, 10);

        $content = curl_exec($connection);

        $headers = curl_getinfo($connection);

        if (!isset($headers['http_code'], $headers['content_type'])
            || $headers['http_code'] !== 200
            || $headers['content_type'] !== 'application/json'
            || curl_errno($connection)) {
            return false;
        }

        curl_close($connection);

        $this->remoteFilterModificationUnixTime = (int)$headers['filetime'];

        return $content;
    }


    /**
     * Initializes the meta data containing information about the status of the current filter rules in the cache.
     */
    private function initMetaData()
    {
        if ($this->metaData === null && file_exists($this->metaDataPath)) {
            if (PHP_VERSION_ID > 70000) {
                $this->metaData = unserialize(file_get_contents($this->metaDataPath),
                                              ['allowed_classes' => [MetaData::class]]);
            } else {
                $this->metaData = unserialize(file_get_contents($this->metaDataPath));
            }

            if (!is_object($this->metaData) || !$this->metaData instanceof MetaData) {
                $this->metaData = null;
            }
        }
    }


    /**
     * Returns the cache filter rules modification date as unix timestamp, if exists
     *
     * @return int|null
     */
    private function getModificationDateUnixTime()
    {
        $this->initMetaData();

        if ($this->metaData !== null) {
            return $this->metaData->modificationDateUnixTime();
        }

        return null;
    }


    /**
     * Returns the date of the last update check as unix timestamp, if exists
     *
     * @return int|null
     */
    private function getLastUpdateCheckUnixTime()
    {
        $this->initMetaData();

        if ($this->metaData) {
            return $this->metaData->lastUpdateCheckUnixTime();
        }

        return null;
    }
}