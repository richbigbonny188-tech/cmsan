<?php
/* --------------------------------------------------------------
  FilterReader.php 2021-03-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

/**
 * Class GProtectorFilterReader
 */
class FilterReader
{

    /**
     * Reads the content of the Fallback FilterRule file and if it contains valid JSON, then return that content,
     * otherwise retry.
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getFallbackFilterRules()
    {
        $localRules = $this->readFile(GAMBIO_PROTECTOR_LOCAL_FILERULES_DIR .
                                      GAMBIO_PROTECTOR_LOCAL_FILERULES_FILENAME);
        
        if ($this->isJsonValid($localRules)) {
            return json_decode($localRules, true);
        }
        
        return new InvalidArgumentException("Fallback standard.json file is invalid.");
    }
    
    
    /**
     * Gets a List of Custom FilterRule files, passes these into getCustomFilesContent() and returns it.
     *
     * @return array
     */
    public function getCustomFilterRules()
    {
        $allFiles = scandir(GAMBIO_PROTECTOR_LOCAL_FILERULES_DIR);
        $filenames = array_diff($allFiles, ['..', '.', GAMBIO_PROTECTOR_LOCAL_FILERULES_FILENAME]);
        
        $filesContent = [];
        foreach ($filenames as $filename) {
            if (substr($filename, -5) !== '.json') {
                continue;
            }
            $rawFileContent = file_get_contents(GAMBIO_PROTECTOR_LOCAL_FILERULES_DIR . $filename);
            $jsonFileContent = json_decode($rawFileContent, true);
            $filesContent = array_merge($filesContent, $jsonFileContent);
        }
        return $filesContent;
    }
    
    
    /**
     * returns whether a given JSON string is valid JSON.
     *
     * @param string $jsonString
     *
     * @return bool
     */
    private function isJsonValid($jsonString)
    {
        json_decode($jsonString);
        return json_last_error() === JSON_ERROR_NONE;
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
    
        return file_get_contents($path, 'r');
    }
}
