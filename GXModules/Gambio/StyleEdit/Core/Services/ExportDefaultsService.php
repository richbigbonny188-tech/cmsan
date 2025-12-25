<?php
/* --------------------------------------------------------------
  ExportDefaultsService.php 2019-12-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services;

use Exception;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Configurations\ShopBaseUrl;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfiguration;
use Gambio\StyleEdit\Core\Components\Theme\StyleEditThemeService;
use Gambio\StyleEdit\Core\Helpers\ConvertSettingsToDefaultValueThemeExtensions;
use Gambio\StyleEdit\Core\Helpers\DefaultOverwriteCreator;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use Gambio\StyleEdit\StyleEditConfiguration;
use stdClass;

/**
 * Class ExportDefaultsService
 * @package Gambio\StyleEdit\Core\Services
 */
class ExportDefaultsService
{
    
    /**
     * @var StyleEditThemeService
     */
    protected $themeService;
    
    /**
     * @var FilesystemAdapter
     */
    protected $filesystem;
    
    
    
    
    /**
     * ExportService constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->themeService  = SingletonPrototype::instance()->get(StyleEditThemeService::class);
        $this->filesystem    = SingletonPrototype::instance()->get('FilesystemAdapterShopRoot');
    }
    
    
    
    /**
     * @param string $themeId
     *
     * @return ThemeConfiguration
     * @throws TranslatedException
     */
    public function getConfiguration(string $themeId): ThemeConfiguration
    {
        return $this->themeService->getConfigurationById($themeId);
    }
    
    
    /**
     * @param string $themeId
     *
     * @return stdClass[]
     * @throws FileNotFoundException
     */
    protected function settingsJsonForTheme(string $themeId): array
    {
        $settingsJsonPath = 'themes' . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR;
        $settingsDefaultJson = $settingsJsonPath . 'settings.default.json';
        $settingsJson = $settingsJsonPath . 'settings.json';

        $settingsJsonDefaultExists = $this->filesystem->has($settingsDefaultJson);
        $settingsJsonExists = $this->filesystem->has($settingsJson);

        if(!$settingsJsonExists && !$settingsJsonDefaultExists) {
            throw new FileNotFoundException();
        }

        if($settingsJsonExists) {
            $settingJsonStr = $this->filesystem->read($settingsJson);
        } else {
            $settingJsonStr = $this->filesystem->read($settingsDefaultJson);
        }

        return json_decode($settingJsonStr);
    }
    
    
    /**
     * @param string             $themeId
     * @param ThemeConfiguration $configuration
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function setNewDefaults(string $themeId, ThemeConfiguration $configuration): void
    {
        $configurationStd = json_decode(json_encode($configuration, JSON_PRETTY_PRINT), false);
        $settingJson      = $this->settingsJsonForTheme($themeId);
        $themeFilesystem  = SingletonPrototype::instance()->get(FilesystemAdapter::class);
        
        $converter = SingletonPrototype::instance()
            ->get(ConvertSettingsToDefaultValueThemeExtensions::class, $configurationStd, $settingJson);
        
        /** @var DefaultOverwriteCreator $overwriteCreator */
        $overwriteCreator = SingletonPrototype::instance()
            ->get(DefaultOverwriteCreator::class, $themeId, $themeFilesystem, $converter);
        
        $overwriteCreator->store();
    }
}