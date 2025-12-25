<?php
/* --------------------------------------------------------------
  ExportService.php 2019-12-04
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services;

use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Core\Helpers\ZipCreationHelper;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use Gambio\StyleEdit\StyleEditConfiguration;

/**
 * Class ExportService
 * @package Gambio\StyleEdit\Core\Services
 */
class ExportService
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
     * @var ExportImagesService
     */
    protected $exportImagesService;
    
    /**
     * @var ExportDefaultsService
     */
    protected $exportDefaultsService;
    
    /**
     * @var ExportHtmlService
     */
    protected $exportHtmlService;
    
    /**
     * @var ExportContentZoneJsonService
     */
    protected $exportContentZoneJsonService;
    
    
    /**
     * ExportService constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->fileIO                       = SingletonPrototype::instance()->get(FileIO::class);
        $this->configuration                = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
        $this->exportImagesService          = SingletonPrototype::instance()->get(ExportImagesService::class);
        $this->exportDefaultsService        = SingletonPrototype::instance()->get(ExportDefaultsService::class);
        $this->exportHtmlService            = SingletonPrototype::instance()->get(ExportHtmlService::class);
        $this->exportContentZoneJsonService = SingletonPrototype::instance()->get(ExportContentZoneJsonService::class);
    }
    
    
    /**
     * @param string $themeId
     *
     * @return string
     *
     * @throws TranslatedException
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function exportTheme(string $themeId): string
    {
        // conf needs to be read before the copy (duplicate id in /themes/...)
        $themeConfiguration = $this->exportDefaultsService->getConfiguration($themeId);
        $tmp                = $this->duplicationTheme($themeId);
        
        try {
            $tmpThemeId = basename($tmp);
    
            $this->exportDefaultsService->setNewDefaults($tmpThemeId, $themeConfiguration);
            $this->exportContentZoneJsonService->addContentsToThemeJson($tmpThemeId);
            $this->exportContentZoneJsonService->resetProductWidgetsProductSelection($tmpThemeId);
            $this->exportImagesService->addChangedFiles($this->exportContentZoneJsonService->contentManagerImages(), $tmpThemeId);
            $this->exportImagesService->moveImageDependenciesToThemeDirectory($tmpThemeId);
            $this->exportHtmlService->setChangedFiles($this->exportImagesService->changedFiles());
            $this->exportHtmlService->updateContentZoneHtmlFiles($tmpThemeId);
    
            /**
             * creating a zip archive of the result
             * @var ZipCreationHelper $zipHelper
             */
            $zipHelper = SingletonPrototype::instance()
                ->get(ZipCreationHelper::class, $this->configuration->themesFolderPath() . $tmpThemeId, $themeId);
    
            $zipPath = $zipHelper->createArchive();
        }
        finally {
            $this->fileIO->recursive_rmdir($tmp);
        }
        
        return $zipPath;
    }
    
    /**
     * @param string $themeId
     *
     * @return string
     */
    public function duplicationTheme(string $themeId): string
    {
        $themePath   = $this->configuration->themesFolderPath() . $themeId;
        $destination = $this->configuration->themesFolderPath() . $themeId . '_export';
        
        $this->fileIO->recursive_copy($themePath, $destination);
        
        return $destination;
    }
    
    
    /**
     * @param string $path
     */
    public function deleteExportedZip(string $path): void
    {
        unlink($path);
    }
}