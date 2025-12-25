<?php
/*--------------------------------------------------------------------
 StringParser.php 2020-3-4
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Helpers;

use \Exception;

/**
 * Class StringParser
 * @package Gambio\StyleEdit\Core\Helpers
 */
class StringParser
{
    /**
     * @var string[]
     */
    protected const ALLOWED_FILE_TYPES = [
        'application/zip',              // linux
        'application/x-zip-compressed'  // windows10
    ];
    
    /**
     * Validates Zip string and stores a valid .zip file to the temporary directory
     *
     * @param string $string must contain zip file
     *
     * @return string path to zip file in tmp directory
     *
     * @throws Exception
     */
    public function toZip($string)
    {
        //  An empty string means there was no uploaded file
        if ($string === '') {
            
            throw new Exception('Missing file!');
        }
    
        $fileTypeIsValid = false;
        
        foreach (self::ALLOWED_FILE_TYPES as $fileType) {
            
            $regexPattern = '/^Content-Type:\s' . preg_quote($fileType, '/') . '/m';
    
            if (preg_match_all($regexPattern, $string) !== 1) {
                
                $fileTypeIsValid = true;
            }
        }
        
        
        if ($fileTypeIsValid === false) {
            
            throw new Exception(
                'Invalid MIME type. Allowed types are ' . implode(', ', self::ALLOWED_FILE_TYPES)
            );
        }
        
        $tmpZipName = 'tmpzip_' . md5(time()) . '.zip';
        $tmpZipPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tmpZipName;
        
        if (!$this->checkMemoryForProcess(strlen($string))) {
            throw new Exception("File is to large to process");
        }
        
        $string = substr($string, strpos($string, 'PK'));
        
        if (!file_put_contents($tmpZipPath, $string, 0777)) {
            throw new Exception('Could not write to ' . str_replace($tmpZipName, '', $tmpZipPath));
        }
        
        $archive = zip_open($tmpZipPath);
        
        //  Is the stored zip file readable
        if (!is_resource($archive) || !is_readable($tmpZipPath)) {
            throw new Exception('File can not be read!');
        }
        
        zip_close($archive);
        
        return $tmpZipPath;
    }
    
    
    /**
     * Check if the available memory is enough to handle process
     *
     * @param int $processSize
     *
     * @return bool
     */
    protected function checkMemoryForProcess(int $processSize) : bool
    {
        $limit = $this->getBytesValue(ini_get('memory_limit'));
        $usage = memory_get_usage();
        
        return $processSize < ($limit - $usage);
    }
    
    
    /**
     * @param $val
     *
     * @return int
     */
    private function getBytesValue($val) : int
    {
        $val  = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val  = substr($val, 0, -1);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        
        return $val;
    }
    
}