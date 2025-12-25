<?php
/*--------------------------------------------------------------------------------------------------
    StyleEditThemeRepository.php 2020-01-29
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme\Repositories;

use ContentWriteServiceInterface;
use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfiguration;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use ReflectionException;
use stdClass;
use ThemeContentsParser;
use ZipArchive;

/**
 * Class ThemeRepository
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class StyleEditThemeRepository extends ThemeBasicFileRepository
{
    /**
     * Allowed file type for uploading
     */
    private const ALLOWED_FILETYPE = 'application/zip';
    /**
     * @var ThemeConfigurationRepository
     */
    protected $configurationRepository;
    /**
     * @var \ContentWriteService
     */
    protected $contentWriteService;
    
    
    /**
     * StyleEditThemeRepository constructor.
     *
     * @param ContentWriteServiceInterface $contentWriteService
     * @param FileIO                       $fileIO
     *
     * @throws Exception
     */
    public function __construct(ContentWriteServiceInterface $contentWriteService, FileIO $fileIO)
    {
        parent::__construct($fileIO);
        $this->contentWriteService = $contentWriteService;
        $this->fileIO = $fileIO;
    }
    
    
    /**
     * @param ThemeConfiguration $theme
     *
     * @throws \Exception
     */
    public function delete(ThemeConfiguration $theme): void
    {
        if (file_exists($theme->path() . 'preview.json')) {
            $preview       = $this->fileIO()->read($theme->path() . 'preview.json');
            $publishedPath = SHOP_ROOT . $preview->publishPath;
            
            if (is_dir($publishedPath)) {
                $this->fileIO()->recursive_rmdir($publishedPath);
            }
        }
        
        $this->fileIO()->recursive_rmdir($theme->path());
    }
    
    
    /**
     * Validates and extracts uploaded themes
     *
     * @param string $data must contain zip file
     *
     * @throws TranslatedException
     * @throws \Exception
     */
    public function save($data): void
    {
        $zipTmp = $this->storeZipFromStringToTmpDirectory($data);
        try {
            $zip = new ZipArchive;
            
            if ($zip->open($zipTmp) === true) {
                $tmpDirList         = glob($this->configuration()->tmpFolderPath() . '/*', GLOB_ONLYDIR);
                $extractedDirectory = '';
                try {
                    if ($zip->extractTo($this->configuration()->tmpFolderPath())) {
                        $extractedDirectories = array_diff(glob($this->configuration()->tmpFolderPath() . '*',
                                                                GLOB_ONLYDIR),
                                                           $tmpDirList);
                        
                        $extractedDirectory = $this->validateExtractedContent($extractedDirectories, $zipTmp);
                        
                        //  validating the uploaded configuration file
                        
                        $uploadedConfiguration = $this->loadJsonFilesFromDisk($extractedDirectory . DIRECTORY_SEPARATOR . 'theme.json');
                        
                        //  Does a already installed theme have the same themeId (directory name)
                        if ($this->themeIsAlreadyInstalled($extractedDirectory, $uploadedConfiguration)) {
                            throw new Exception('Theme is already installed');
                        }
                        
                        if ($zip->extractTo($this->configuration()->themesFolderPath())) {
                            $this->installContentsFromThemeJson($extractedDirectory);
                            
                            return;
                        }
                    }
                }
                finally {
                    $this->fileIO()->recursive_rmdir($extractedDirectory);
                }
            }
        }
        finally {
            if (file_exists($zipTmp)) {
                unlink($zipTmp);
            }
        }
        
        throw new TranslatedException('INVALID_FILE_CONTENT', [self::ALLOWED_FILETYPE]);
    }
    
    
    /**
     * Validates Zip string and stores a valid .zip file to the temporary directory
     *
     * @param string $zip must contain zip file
     *
     * @return string path to zip file in tmp directory
     *
     * @throws TranslatedException
     * @throws \Exception
     */
    private function storeZipFromStringToTmpDirectory($zip)
    {
        //  An empty string means there was no uploaded file
        if ($zip === '') {
            throw new TranslatedException('THEME_UPLOAD_MISSING_FILE');
        }
        
        $regexPattern = '/^Content-Type:\s' . preg_quote(self::ALLOWED_FILETYPE, '/') . '/m';
        
        if (preg_match_all($regexPattern, $zip) !== 1) {
            throw new TranslatedException('INVALID_FILE_TYPE', [self::ALLOWED_FILETYPE]);
        }
        
        $tmpZipName = 'uploadedTheme' . date('Y-m-d_H-i-s') . '.zip';
        $tmpZipPath = $this->configuration()->themesFolderPath() . $tmpZipName;
        
        $zip = substr($zip, strpos($zip, 'PK'));
        
        if (!file_put_contents($tmpZipPath, $zip, 0777)) {
            throw new TranslatedException('INSUFFICIENT_PERMISSIONS', [str_replace($tmpZipName, '', $tmpZipPath)]);
        }
        
        $archive = zip_open($tmpZipPath);
        
        //  Is the stored zip file readable
        if (!is_resource($archive) || !is_readable($tmpZipPath)) {
            throw new TranslatedException('FILE_CANT_BE_READ');
        }
        
        zip_close($archive);
        
        return $tmpZipPath;
    }
    
    
    /**
     * @param string[] $extractedDirectories
     * @param string   $zipTmp
     *
     * @return string
     *
     * @throws TranslatedException
     * @throws \Exception
     */
    protected function validateExtractedContent($extractedDirectories, $zipTmp)
    {
        if (count($extractedDirectories) !== 1) {
            if (count($extractedDirectories) > 1) {
                while (count($extractedDirectories)) {
                    $this->fileIO()->recursive_rmdir(array_shift($extractedDirectories));
                }
            }
            unlink($zipTmp);
            throw new TranslatedException('INVALID_FILE_CONTENT', [self::ALLOWED_FILETYPE]);
        }
        
        return array_shift($extractedDirectories);
    }
    
    
    /**
     * @param string   $path                   Path to a temporary directory that ends
     *                                         with the directory name of a about to be installed theme
     * @param stdClass $uploadedConfiguration  ThemeConfiguration of temporary theme
     *
     * @return bool should the theme be installed?
     * @throws \Exception
     */
    protected function themeIsAlreadyInstalled($path, $uploadedConfiguration)
    {
        $directoryName = explode(DIRECTORY_SEPARATOR, $path);
        $directoryName = array_pop($directoryName);
        $themePath     = $this->configuration()->themesFolderPath() . DIRECTORY_SEPARATOR . $directoryName;
        
        $themeInstalled = true;
        
        try {
            $installedTheme = $this->loadConfigFromDisk($themePath);
        } catch (TranslatedException $exception) {
            $themeInstalled = false;
        } catch (Exception $e) {
            $themeInstalled = false;
        }
        
        if (false && $themeInstalled && $installedTheme->version < $uploadedConfiguration->version) {
            $this->fileIO()->recursive_rmdir($themePath);
            
            return false;
        }
        
        return $themeInstalled;
    }
    
    
    /**
     * @param string $extractedDirectory
     *
     * @throws TranslatedException
     * @throws \UnfinishedBuildException
     */
    protected function installContentsFromThemeJson(string $extractedDirectory): void
    {
        $extractedDirectorySplitBySeparator = explode(DIRECTORY_SEPARATOR, $extractedDirectory);
        $themeName                          = array_pop($extractedDirectorySplitBySeparator);
        $themePath                          = $this->configuration()->themesFolderPath() . DIRECTORY_SEPARATOR
                                              . $themeName;
        
        $uploadedConfiguration = $this->loadConfigFromDisk($themePath);
        
        if (isset($uploadedConfiguration->contents)
            && get_class($uploadedConfiguration->contents) === stdClass::class) {
            $themeContents = ThemeContentsParser::parse($uploadedConfiguration->contents);
            
            if ($themeContents->infoElements()->count()) {
                foreach ($themeContents->infoElements()->getArray() as $infoElement) {
                    $this->contentWriteService->storeInfoElementContent($infoElement);
                }
            }
            
            if ($themeContents->infoPages()->count()) {
                foreach ($themeContents->infoPages()->getArray() as $infoPage) {
                    $this->contentWriteService->storeInfoPageContent($infoPage);
                }
            }
            
            if ($themeContents->linkPages()->count()) {
                foreach ($themeContents->linkPages()->getArray() as $linkPage) {
                    $this->contentWriteService->storeLinkPageContent($linkPage);
                }
            }
        }
    }
    
    
    /**
     * @param ThemeConfiguration $source
     * @param stdClass           $data
     *
     * @return ThemeConfiguration
     * @throws TranslatedException
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function copyTo(ThemeConfiguration $source, stdClass $data)
    {
        $data->author       = $data->author ?? 'CUSTOM';
        $data->version      = $data->version ?? '1.0';
        $data->preview      = $data->preview ?? null;

        //calculated properties must always be null
        $data->editable     = null;
        $data->removable    = null;
        $data->active       = null;
        $data->children     = null;
        $data->languages    = null;

        $destinationPath    = $this->configuration()->themesFolderPath() . $data->id;
        
        $this->fileIO()->recursive_copy($source->path(), $destinationPath);
        
        $this->fileIO()->patch($destinationPath . DIRECTORY_SEPARATOR . 'theme.json', $data);
        
        return $this->configurationRepository()->getById($data->id);
    }
    
    
    /**
     * @return ThemeConfigurationRepository
     * @throws \Exception
     */
    public function configurationRepository()
    {
        if ($this->configurationRepository === null) {
            $this->configurationRepository = SingletonPrototype::instance()->get(ThemeConfigurationRepository::class);
            if (!$this->configurationRepository) {
                throw new Exception('ConfigurationRepository is not initialized');
            }
        }
        
        return $this->configurationRepository;
    }
    
    
    /**
     * @param $themeId
     *
     * @return bool
     * @throws Exception
     */
    public function exists($themeId): bool
    {
        return $this->fileIO->exists($this->configuration()->themesFolderPath() . $themeId . DIRECTORY_SEPARATOR
                                     . 'theme.json');
    }
    
    
}