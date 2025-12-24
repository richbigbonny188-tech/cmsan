<?php
/**
 * ImportThemeService.php 2019-12-11
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2019 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

namespace Gambio\StyleEdit\Core\Services;

use \FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Configurations\ShopBasePath;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\StyleEditConfiguration;
use CacheControl;
use PhraseCacheBuilder;

/**
 * Class ImportService
 * @package Gambio\StyleEdit\Core\Services
 */
class ImportThemeService
{
    /**
     * @var StyleEditConfiguration
     */
    private $configuration;
    
    /**
     * @var CacheControl
     */
    private $cacheControl;
    
    /**
     * @var ShopBasePath
     */
    private $shopBasePath;
    
    /**
     * @var FilesystemAdapter
     */
    private $filesystem;
    
    /**
     * @var FileIO
     */
    private $fileIO;
    
    /**
     * @var PhraseCacheBuilder
     */
    private $phraseCacheBuilder;
    
    
    public function __construct()
    {
        $this->configuration      = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
        $this->cacheControl       = SingletonPrototype::instance()->get(CacheControl::class);
        $this->phraseCacheBuilder = SingletonPrototype::instance()->get(PhraseCacheBuilder::class);
        $this->filesystem         = SingletonPrototype::instance()->get('FilesystemAdapterShopRoot');
        $this->shopBasePath       = SingletonPrototype::instance()->get(ShopBasePath::class);
        $this->fileIO             = SingletonPrototype::instance()->get(FileIO::class);
    }


    /**
     * @param string $themeId
     * @param string $themeTmpPath
     * @param bool $overwrite
     * @return bool
     * @throws FileNotFoundException
     */
    public function import(string $themeId, string $themeTmpPath, bool $overwrite = false)
    {
        if (!$this->filesystem->has($themeTmpPath)) {
            throw new FileNotFoundException("Theme files not found in defined path.");
        }
        
        $themeTmpAbsolutePath = $this->shopBasePath->value() . $themeTmpPath;
        
        $themesFolder    = $this->configuration->themesFolderPath();
        $importThemePath = $themesFolder . $themeId;
        
        $themeExists = $this->themeExists($themeId);
        
        if ($themeExists) {
            if ($overwrite) {
                $this->fileIO->recursive_rmdir($importThemePath);
                $this->copyThemeToPath($themeTmpAbsolutePath, $themesFolder);
                $this->fileIO->recursiveChmod($themesFolder . DIRECTORY_SEPARATOR . $themeId);
            }
        } elseif (!$themeExists) {
            $this->copyThemeToPath($themeTmpAbsolutePath, $themesFolder);
        }
        
        $this->fileIO->recursive_rmdir($themeTmpAbsolutePath);
        
        return true;
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
    
    
    protected function copyThemeToPath($tmp, $final)
    {
        $this->fileIO->recursive_copy($tmp, $final);
        // rebuild text phrases cache
        $this->phraseCacheBuilder->build();
        $this->cacheControl->clear_data_cache();
    }
    
    
}