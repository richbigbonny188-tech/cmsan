<?php
/* --------------------------------------------------------------
   ThemeConfigurationRepository.php 2022-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components\Theme\Repositories;

use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfiguration;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfigurationCollection;
use Gambio\StyleEdit\Core\Components\Theme\Factories\ThemeConfigurationFactory;
use Gambio\StyleEdit\Core\InvalidDirectoryException;
use Gambio\StyleEdit\Core\Services\Configuration\Factories\ConfigurationFactory;
use Gambio\StyleEdit\Core\Services\SettingsService;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use ReflectionException;
use RuntimeException;

/**
 * Class ConfigurationRepository
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class ThemeConfigurationRepository extends ThemeBasicFileRepository
{
    /**
     * @var SettingsService
     */
    protected $settingsService;
    
    /**
     * @var ThemeConfigurationCollection
     */
    private $ThemeConfigurationCollection;
    /**
     * @var ThemeConfigurationFactory
     */
    protected $factory;
    
    
    /**
     * @return bool|mixed
     * @throws Exception
     */
    public static function create()
    {
        return SingletonPrototype::instance()->get(static::class);
    }
    
    
    /**
     * @param string $themeId
     *
     * @return ThemeConfiguration
     * @throws FileNotFoundException
     * @throws TranslatedException
     */
    public function getById(string $themeId): ThemeConfiguration
    {
        try {
            $themePath   = $this->configuration()->themesFolderPath() . $themeId;
            $themeConfig = $this->loadConfigFromDisk($themePath);
            $themeConfig->children = $this->get()->get($themeId)->children();
            $theme =   $this->factory->createExtendedFromJson($themeConfig, $themePath);
        } catch (InvalidDirectoryException $e) {
            throw new RuntimeException('Invalid theme id: ' . $themeId);
        }
        return $theme;
    }
    
    
    /**
     * @return SettingsService
     * @throws Exception
     */
    protected function settingsService(): SettingsService
    {
        if ($this->settingsService === null) {
            $this->settingsService = SingletonPrototype::instance()->get(SettingsService::class);
        }
        
        return $this->settingsService;
    }
    
    
    /**
     * ThemeConfigurationRepository constructor.
     *
     * @param ThemeConfigurationFactory $factory
     */
    public function __construct(ThemeConfigurationFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @return ThemeConfigurationCollection
     * @throws Exception
     */
    public function get()
    {
        $result              = new ThemeConfigurationCollection();
        $themeDirectoryPaths = $this->getThemeDirectoryPaths();
        
        foreach ($themeDirectoryPaths as $themePath) {
            
            try {
                $themeConfig = $this->loadConfigFromDisk($themePath);
                $theme       = $this->factory->createFromJson($themeConfig);
            } catch (Exception $exception) {
                continue;
            }
            
            if ($result->hasKey($theme->id())) {
                throw new TranslatedException('DUPLICATED_THEME_ID',
                                              [$theme->title(), $result->get($theme->id())->title()]);
            }
            $result->add($theme);
        }
        
        //map theme hierarchy
        foreach ($result->elements() as &$theme) {
            if ($theme->extendsOf()) {
                if ($result->hasKey($theme->extendsOf())) {
                    $result->get($theme->extendsOf())->children()[] = $theme->id();
                } else {
                    // If the parent theme is invalid, we must set the child theme as not editable in order to
                    // display it correctly on SE4 as "Invalid Theme"
                    $theme->setToNotEditable();
                }
            }
        }
        unset ($theme);
        
        return $result;
    }
    
    
    /**
     * @return array
     * @throws Exception
     */
    private function getThemeDirectoryPaths(): array
    {
        return $this->fileIO()->listDirectory($this->configuration()->themesFolderPath());
    }
    
    
    /**
     * @param ThemeConfiguration $theme
     * @param                    $data
     *
     * @throws Exception
     */
    public function patch(ThemeConfiguration $theme, $data): void
    {
        $configuration = $this->loadOriginalConfigFromDisk($theme->id());
        
        foreach ($data as $key => $value) {
            $configuration->$key = $value;
        }
        
        if (isset($configuration->active) && !$configuration->active) {
            unset($configuration->active);
        }
        
        if (empty($configuration->extends)) {
            unset($configuration->extends);
        }
        
        $this->saveConfigToDisk($configuration, $theme->path());
    }
    
    
}

