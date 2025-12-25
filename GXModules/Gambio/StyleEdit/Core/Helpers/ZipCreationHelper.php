<?php
/* --------------------------------------------------------------
  ZipCreationHelper.php 2019-12-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Helpers;

use Gambio\StyleEdit\Core\Helpers\Interfaces\ArchiveHelperInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use ZipArchive;

/**
 * Class ZipCreationHelper
 * @package Gambio\StyleEdit\Core\Helpers
 */
class ZipCreationHelper implements ArchiveHelperInterface
{
    /**
     * @var string
     */
    private $directory;
    
    /**
     * @var ZipArchive
     */
    protected $zip;
    
    /**
     * @var string
     */
    private $dirInZipName;
    
    
    /**
     * ZipCreationHelper constructor.
     *
     * @param string $directory
     * @param string $dirInZipName
     */
    public function __construct(string $directory, string $dirInZipName)
    {
        $this->directory    = $directory;
        $this->dirInZipName = $dirInZipName;
    }
    
    
    /**
     * @return string
     */
    public function createArchive(): string
    {
        $this->zip = new ZipArchive;
        $zipPath   = str_replace(basename($this->directory), '', $this->directory) . $this->dirInZipName . '.zip';
        
        if ($this->zip->open($zipPath, ZipArchive::CREATE) !== true) {
            
            throw new RuntimeException('ZipArchive for theme export could not be created');
        }
        
        $this->addFilesFromDirectoryToZip();
        $this->zip->close();
        
        return $zipPath;
    }
    
    
    protected function addFilesFromDirectoryToZip(): void
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->directory));
        $allFiles = array_filter(iterator_to_array($iterator),
            static function (SplFileInfo $file) {
                return $file->isFile();
            });
        
        /** @var SplFileInfo[] $allFiles */
        while (count($allFiles)) {
            
            $filePath     = array_shift($allFiles);
            $relativePath = $this->dirInZipName . DIRECTORY_SEPARATOR . substr($filePath, strlen($this->directory) + 1);
            
            $this->zip->addFile($filePath, $relativePath);
        }
        
        $this->zip->addEmptyDir($this->dirInZipName . DIRECTORY_SEPARATOR . 'config');
    }
}