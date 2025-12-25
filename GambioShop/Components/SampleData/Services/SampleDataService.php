<?php
/*
 * --------------------------------------------------------------
 *   SampleDataService.php 2021-03-16
 *   Gambio GmbH
 *   http://www.gambio.de
 *   Copyright (c) 2021 Gambio GmbH
 *   Released under the GNU General Public License (Version 2)
 *   [http://www.gnu.org/licenses/gpl-2.0.html]
 * --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\SampleData\Services;

use Doctrine\DBAL\Connection;
use Exception;
use Gambio\Shop\SampleData\SampleDataService as ServiceInterface;

/**
 * Class SampleDataService
 */
class SampleDataService implements ServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * SampleDataService constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @inheritDoc
     */
    public function install() : bool
    {
        try {
            $this->importSql();
            $this->importFiles();
            
            return true;
        } catch (\Throwable $e) {
            // continue silently
        }
        
        return false;
    }
    
    
    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function importSql() : void
    {
        $this->connection->exec(
            file_get_contents(
                $this->getDataPath('database.sql')
            )
        );
    }
    
    
    /**
     * @throws Exception
     */
    protected function importFiles() : void
    {
        $files    = $this->getDirectoryList($this->getDataPath('files'));
        $basePath = $this->getBasePath();
        foreach ($files as $file) {
            $filePath = str_replace('files' . DIRECTORY_SEPARATOR, '', strstr($file, 'files'));
            $filename = basename($file);
            $pathDirs = str_replace($filename, '', $filePath);
            $dirs     = explode(DIRECTORY_SEPARATOR, $pathDirs);
            $dirPath  = $basePath;
            foreach ($dirs as $dir) {
                if (!$dir) {
                    continue;
                }
                $dirPath = $dirPath . $dir . DIRECTORY_SEPARATOR;
                if (!file_exists($dirPath) && !is_dir($dirPath)) {
                    mkdir($dirPath);
                }
            }
            $destinationPath = $dirPath . $filename;
            if (!copy($file, $destinationPath)) {
                $errors = error_get_last();
                throw new Exception(
                    "Copy Error({$errors['type']}): {$file} to {$destinationPath} \n {$errors['message']}, in {$errors['file']}, on line {$errors['line']}"
                );
            }
        }
    }
    
    
    /**
     * @return string
     */
    private function getBasePath() : string
    {
        return dirname(__DIR__, 4) . DIRECTORY_SEPARATOR;
    }
    
    
    /**
     * @param string $path
     *
     * @return string
     */
    private function getDataPath(string $path) : string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $path;
    }
    
    
    /**
     * @param string $path
     * @param array  $results
     *
     * @return array
     * @throws Exception
     */
    private function getDirectoryList(string $path, &$results = []) : array
    {
        if (!is_dir($path) && !is_file($path)) {
            throw new Exception('No such file or directory: ' . $path);
        }
        
        $files = scandir($path);
        foreach ($files as $file) {
            
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($filePath)) {
                $this->getDirectoryList($filePath, $results);
                continue;
            }
            
            if (is_file($filePath)) {
                $results[] = $filePath;
            }
        }
        
        return $results;
    }
    
}