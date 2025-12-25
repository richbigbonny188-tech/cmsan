<?php
/*--------------------------------------------------------------------------------------------------
    BasicFileRepository.php 2020-02-27
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Repositories;

use Exception;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Json\ThemeInheritanceResolver;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\StyleEditConfiguration;
use RuntimeException;

/**
 * Class BasicThemeRepository
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class BasicFileRepository
{
    /**
     * @var StyleEditConfiguration
     */
    protected $configuration;
    
    /**
     * @var FileIO
     */
    protected $fileIO;
    /**
     * @var StyleEditConfiguration|null
     */
    protected $styleEditConfiguration;
    
    
    /**
     * BasicFileRepository constructor.
     *
     * @param FileIO                      $fileIO
     * @param StyleEditConfiguration|null $styleEditConfiguration
     *
     * @throws Exception
     */
    public function __construct(FileIO $fileIO = null, StyleEditConfiguration $styleEditConfiguration = null)
    {
        if (!$fileIO) {
            $this->fileIO = SingletonPrototype::instance()->get(FileIO::class);
        } else {
            $this->fileIO = $fileIO;
        }
        if (!$styleEditConfiguration) {
            $this->styleEditConfiguration = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
        } else {
            $this->styleEditConfiguration = $styleEditConfiguration;
        }

    }
    
    
    /**
     * @return StyleEditConfiguration
     * @throws Exception
     */
    protected function configuration(): StyleEditConfiguration
    {
        
        if ($this->configuration === null) {
            $this->configuration = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
            if (!$this->configuration) {
                throw new RuntimeException('StyleEditConfiguration was not initialized');
            }
        }
        
        return $this->configuration;
    }
    
    
    /**
     * @param $filePath
     *
     * @return array|mixed
     * @throws Exception
     * @throws RuntimeException
     */
    protected function loadJsonFilesFromDisk($filePath)
    {
        $jsonObject = $this->fileIO()->read($filePath);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid Json file '$filePath'" . json_last_error_msg());
        }
        
        return $jsonObject;
    }
    
    
    /**
     * @return FileIO
     * @throws Exception
     */
    protected function fileIO(): FileIO
    {
        if ($this->fileIO === null) {
            $this->fileIO = SingletonPrototype::instance()->get(FileIO::class);
            if (!$this->fileIO) {
                throw new RuntimeException('FileIO was not initialized');
            }
        }
        
        return $this->fileIO;
    }
    
    
    /**
     * @param $object
     * @param $filePath
     *
     * @return bool|int
     * @throws Exception
     */
    protected function saveJsonFilesToDisk($object, $filePath)
    {
        return $this->fileIO()->write($object, $filePath);
    }


    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getExistentSettingsFilename(string $filename): ?string
    {
        if ($this->fileIO->exists($filename)) {
            return $filename;
        }

        $fileParts = explode('.', $filename);
        $fileParts[count($fileParts) - 1] = 'default';
        $fileParts[] = 'json';
        $defaultFilename = implode('.', $fileParts);
        if ($this->fileIO->exists($defaultFilename)) {
            return $defaultFilename;
        }

        return null;
    }


}