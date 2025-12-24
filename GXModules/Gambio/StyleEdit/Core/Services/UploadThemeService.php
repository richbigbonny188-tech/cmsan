<?php
/* --------------------------------------------------------------
  ExportService.php 2022-04-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services;

use Exception;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Configurations\ShopBasePath;
use Gambio\StyleEdit\Core\Components\Theme\UploadValidator;
use Gambio\StyleEdit\Core\Helpers\ZipExtractHelper;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use Gambio\StyleEdit\StyleEditConfiguration;
use Gambio\StyleEdit\Core\Json\ThemeInfoReader;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Class UploadThemeService
 * @package Gambio\StyleEdit\Core\Services
 */
class UploadThemeService
{
    /**
     * @var StyleEditConfiguration
     */
    private $configuration;
    
    /**
     * @var ZipExtractHelper
     */
    private $extractHelper;
    
    /**
     * @var FilesystemAdapter
     */
    private $filesystem;
    
    /**
     * @var ShopBasePath
     */
    private $shopBasePath;
    
    
    public function __construct()
    {
        $this->configuration = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
        $this->extractHelper = SingletonPrototype::instance()->get(ZipExtractHelper::class);
        $this->filesystem    = SingletonPrototype::instance()->get('FilesystemAdapterShopRoot');
        $this->shopBasePath  = SingletonPrototype::instance()->get(ShopBasePath::class);
    }
    
    
    /**
     * @param $file
     *
     * @return array
     * @throws FileNotFoundException
     * @throws TranslatedException
     * @throws Exception
     */
    public function upload($file) : array
    {
        $extractPath = $this->getExtractPath();
        $extracted   = $this->extractHelper->extractArchive($file, $extractPath);
        $themeFiles  = $this->getThemeFilesByPath($extracted);
        
        $themeJsonFile = $this->getThemeJson($themeFiles);
        if (!$themeJsonFile) {
            throw new FileNotFoundException("Invalid theme, theme.json not found");
        }
        
        $themeJson    = $this->filesystem->read($themeJsonFile);
        $themeInfo    = SingletonPrototype::instance()->get(ThemeInfoReader::class, $themeJson);
        $themeId      = $themeInfo->getThemeId();
        $themeVersion = $themeInfo->getThemeVersion();
        
        if (!$themeId) {
            throw new TranslatedException('UPLOADED_THEME_INVALID', [], 500);
        }
        
        $themeExists     = $this->themeExists($themeId);
        $themeImportPath = $this->getDirectoryRelativePath($extracted);
        
        if ($this->getThemeValidator(basename($extracted) . '/' . $themeId)->canBeOpenedInStyleEdit4() === false) {
            throw new TranslatedException('UPLOADED_THEME_INVALID', [], 500);
        }
        
        // prepare response
        $results = [
            'name'    => $themeId,
            'path'    => $themeImportPath,
            'exists'  => $themeExists,
            'version' => $themeVersion,
        ];
        
        return $results;
    }
    
    
    protected function getExtractPath()
    {
        return $this->configuration->tmpFolderPath() . "import_" . md5(time()) . "/";
    }
    
    
    protected function getThemeFilesByPath(string $themePath) : array
    {
        $themeDirectory = $this->getDirectoryRelativePath($themePath);
        $themeFiles     = $this->filesystem->listContents($themeDirectory, true);
        
        return $themeFiles;
    }
    
    
    protected function themeExists(string $themeId) : bool
    {
        // done like that in order not to append the themes directory name manually
        return $this->filesystem->has(
            str_replace(
                $this->shopBasePath->value(),
                '',
                $this->configuration->themesFolderPath() . $themeId
            )
        );
    }
    
    
    private function getThemeJson(array $themeFiles = []) : ?string
    {
        $jsonFile = null;
        array_walk(
            $themeFiles,
            function ($value) use (&$jsonFile) {
                if ("theme.json" == $value['basename'] && is_null($jsonFile)) {
                    $jsonFile = $value['path'];
                }
            }
        );
        
        return $jsonFile;
    }
    
    
    private function getDirectoryRelativePath(string $absolutePath) : string
    {
        return str_replace($this->shopBasePath->value(), '', $absolutePath);
    }
    
    
    /**
     * @param string $themeId
     *
     * @return Validator
     * @throws Exception
     */
    protected function getThemeValidator(string $themeId) : UploadValidator
    {
        $permissionMap = [
            'file' => [
                'public'  => 0777,
                'private' => 0700,
            ],
            'dir'  => [
                'public'  => 0777,
                'private' => 0700,
            ]
        ];
        $visibility = PortableVisibilityConverter::fromArray($permissionMap);
        
        $filesystemAdapter = new LocalFilesystemAdapter(
            $this->configuration->tmpFolderPath(), $visibility, LOCK_EX, LocalFilesystemAdapter::DISALLOW_LINKS);
        $filesystem        = new Filesystem($filesystemAdapter);
        $adapter           = new FilesystemAdapter($filesystem);
        
        return SingletonPrototype::instance()->get(UploadValidator::class, $themeId, $adapter, $this->configuration);
    }
}
