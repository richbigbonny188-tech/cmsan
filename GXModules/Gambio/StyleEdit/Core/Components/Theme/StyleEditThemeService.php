<?php
/*--------------------------------------------------------------------------------------------------
    StyleEditThemeService.php 2022-05-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme;

use ContentType;
use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Adapters\Interfaces\CacheCleanerInterface;
use Gambio\StyleEdit\Adapters\Interfaces\ThemeActivatorAdapterInterface;
use Gambio\StyleEdit\Core\BuildStrategies\Exceptions\NotFoundException;
use Gambio\StyleEdit\Core\Command\TransactionalCommandInvoker;
use Gambio\StyleEdit\Core\Components\ContentManager\Command\AbstractContentManagerPagesSaveCommand;
use Gambio\StyleEdit\Core\Components\ContentManager\Factories\ContentManagerPagesSaveCommandFactory;
use Gambio\StyleEdit\Core\Components\Page\Entities\PageOption;
use Gambio\StyleEdit\Core\Components\Theme\Entities\Interfaces\CurrentThemeInterface;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfiguration;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeConfigurationCollection;
use Gambio\StyleEdit\Core\Components\Theme\Entities\ThemeInheritanceMapper;
use Gambio\StyleEdit\Core\Components\Theme\Repositories\PreviewSettingsRepository;
use Gambio\StyleEdit\Core\Components\Theme\Repositories\PublishedThemeRepository;
use Gambio\StyleEdit\Core\Components\Theme\Repositories\StyleEditThemeRepository;
use Gambio\StyleEdit\Core\Components\Theme\Repositories\ThemeConfigurationRepository;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Options\Commands\ClearCacheCommand;
use Gambio\StyleEdit\Core\Options\Commands\UpdateContentsPositionCommand;
use Gambio\StyleEdit\Core\Options\Commands\UpdateContentsSortOrderCommand;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentGroupOption;
use Gambio\StyleEdit\Core\Options\Entities\AbstractOption;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;
use Gambio\StyleEdit\StyleEditConfiguration;
use ReflectionException;

/**
 * Class ThemeService
 * @package Gambio\StyleEdit\Core\Components\Theme
 */
class StyleEditThemeService
{
    
    /**
     * @var bool
     */
    public $previewSettingsRepository;
    /**
     * @var StyleEditConfiguration
     */
    protected $configuration;
    /**
     * @var ThemeConfigurationRepository
     */
    protected $configurationRepository;
    /**
     * @var PublishedThemeRepository
     */
    protected $publishedThemeRepository;
    /**
     * @var StyleEditThemeRepository
     */
    protected $repository;
    /**
     * @var ThemeInheritanceMapper
     */
    private $themeInheritanceMapper;
    
    
    /**
     * ThemeService constructor.
     *
     * @param StyleEditConfiguration $configuration
     * @param ThemeInheritanceMapper $themeInheritanceMapper
     */
    public function __construct(StyleEditConfiguration $configuration, ThemeInheritanceMapper $themeInheritanceMapper)
    {
        $this->configuration          = $configuration;
        $this->themeInheritanceMapper = $themeInheritanceMapper;
    }
    
    
    /**
     * @param ThemeConfiguration                                               $parentTheme
     * @param                                                                  $data
     *
     * @return ThemeConfiguration
     * @throws Exception
     */
    public function duplicateTheme(ThemeConfiguration $parentTheme, $data): ThemeConfiguration
    {
        $data->title = $data->title ?? $this->createTitleForDuplicateTheme($parentTheme->title());
        $data->id    = $data->id ?? $parentTheme->id() . date('Y-m-d_H-i-s');
        
        return $this->repository()->copyTo($parentTheme, $data);
    }
    
    
    /**
     * @param $name
     *
     * @return string
     * @throws Exception
     */
    protected function createTitleForDuplicateTheme($name): string
    {
        $language = SingletonPrototype::instance()->get(Language::class);
        
        switch ($language->code()) {
            case 'de' :
                
                return $name . ' - Kopie';
            
            case 'en' :
            default   :
                
                return $name . ' - Copy';
        }
    }
    
    
    /**
     * @return StyleEditThemeRepository
     * @throws Exception
     */
    protected function repository(): StyleEditThemeRepository
    {
        if ($this->repository === null) {
            $this->repository = SingletonPrototype::instance()->get(StyleEditThemeRepository::class);
            if (!$this->repository) {
                throw new Exception('ThemeRepository was not initialized');
            }
        }
        
        return $this->repository;
    }
    
    
    /**
     * @param bool $alwaysClearPublicTheme
     *
     * @throws Exception
     */
    public function prepareForReloading(bool $alwaysClearPublicTheme = false): void
    {
        $cache        = SingletonPrototype::instance()->get(CacheCleanerInterface::class);
        $currentTheme = SingletonPrototype::instance()->get(CurrentThemeInterface::class);
        
        /**
         * @var CacheCleanerInterface $cache
         */
        if ($cache && $currentTheme) {
            $cache->clearThemeCache($currentTheme->id());
        }
        
        if ($alwaysClearPublicTheme) {
            $cache->clearShopCache();
        }
    }
    
    
    /**
     * @return bool|PreviewSettingsRepository|mixed
     * @throws Exception
     */
    public function previewSettingsRepository()
    {
        if ($this->previewSettingsRepository === null) {
            $this->previewSettingsRepository = SingletonPrototype::instance()->get(PreviewSettingsRepository::class);
            if (!$this->previewSettingsRepository) {
                throw new Exception('PreviewSettingsRepository was not initialized');
            }
        }
        
        return $this->previewSettingsRepository;
    }
    
    
    /**
     * @return string
     * @throws Exception
     */
    public function createPreviewFolder(): string
    {
        $this->publishedThemeRepository()->removeOldPreviewThemes($this->configuration->publicFolderPath() . 'tmp/', 5);
        
        return $this->publishedThemeRepository()->createPreviewFolder();
    }
    
    
    /**
     * @return bool|PublishedThemeRepository|mixed
     * @throws Exception
     */
    public function publishedThemeRepository()
    {
        if ($this->publishedThemeRepository === null) {
            $this->publishedThemeRepository = SingletonPrototype::instance()->get(PublishedThemeRepository::class);
            if (!$this->publishedThemeRepository) {
                throw new Exception('PublishedThemeRepository was not initialized');
            }
        }
        
        return $this->publishedThemeRepository;
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws Exception
     */
    public function initialize($themeId)
    {
        $currentTheme = $this->themeInheritanceMapper->createBasicThemeFor($themeId);
        
        SingletonPrototype::instance()->setUp(CurrentThemeInterface::class, $currentTheme);
        //load the selected template configuration
        $this->loadThemeOverloadClasses($currentTheme);
    }
    
    
    /**
     * @param CurrentThemeInterface $currentTheme
     */
    public function loadThemeOverloadClasses(CurrentThemeInterface $currentTheme)
    {
        $setupComponentScript = $this->configuration->themesFolderPath() . $currentTheme->id() . '/config/setup.php';
        if (file_exists($setupComponentScript)) {
            require_once $setupComponentScript;
        }
    }
    
    
    /**
     * @param                         $data
     *
     * @param ThemeConfiguration|null $themeConfiguration
     *
     * @throws ReflectionException
     * @throws TranslatedException
     * @throws Exception
     */
    public function save($data, ?ThemeConfiguration $themeConfiguration = null): void
    {
        $this->repository()->save($data);
        
        if (isset($data->options) && $themeConfiguration !== null) {
            $this->saveSettings($data->options, $themeConfiguration);
        }
    }
    
    
    /**
     * @param                    $options
     *
     * @param ThemeConfiguration $themeConfiguration
     *
     * @throws ReflectionException
     */
    private function saveSettings($options, ThemeConfiguration $themeConfiguration): void
    {
        
        $commandList             = [];
        $configurationRepository = SingletonPrototype::instance()->get(SettingsRepository::class);
        $clearCache              = false;
        foreach ($options as $id => $jsonOption) {
            if (!isset($jsonOption->id)) {
                $jsonOption->id = $id;
            }
            
            $optionEntity = AbstractOption::createFromJsonObject($jsonOption);
            $clearCache   = $clearCache || $optionEntity->requiresReload();
            
            try {
                $saveCommandName = ucfirst(str_replace('-', '', ucwords($optionEntity->type(), '-'))) . 'SaveCommand';
                $saveCommand     = SingletonPrototype::instance()->get($saveCommandName);
            } catch (NotFoundException $e) {
                $saveCommandName = $optionEntity instanceof
                                   AbstractComponentGroupOption ? 'GroupOptionSaveCommand' : 'OptionSaveCommand';
                $saveCommand     = SingletonPrototype::instance()->get($saveCommandName);
                $saveCommand->setConfigurationRepository($configurationRepository);
            }
            
            $saveCommand->setOption($optionEntity);
            $commandList[] = $saveCommand;
        }
        
        if ($clearCache || !$themeConfiguration->isPreview()) {
            $clearCacheCommand = SingletonPrototype::instance()->get(ClearCacheCommand::class);
            $clearCacheCommand->setClearShopCache(!$themeConfiguration->isPreview());
            
            $commandList[] = $clearCacheCommand;
        }
        $invoker = SingletonPrototype::instance()->get(TransactionalCommandInvoker::class);
        
        $invoker->runInsideTransaction($commandList);
    }
    
    
    /**
     * @param                    $contents
     *
     * @param ThemeConfiguration $themeConfiguration
     *
     * @throws ReflectionException
     */
    private function saveContentManagerPages($contents, ThemeConfiguration $themeConfiguration): void
    {
        $commandList = [];
        $clearCache  = false;
        
        /** @var ContentManagerPagesSaveCommandFactory $saveCommandFactory */
        $saveCommandFactory = SingletonPrototype::instance()->get(ContentManagerPagesSaveCommandFactory::class);
    
        $updateContentsSortOrderCommand = SingletonPrototype::instance()->get(UpdateContentsSortOrderCommand::class);
        $updateContentsPositionCommand  = SingletonPrototype::instance()->get(UpdateContentsPositionCommand::class);
        
        foreach ($contents as $contentManagerEntry) {
            foreach ($contentManagerEntry->items as $sortOrder => $page) {
                if (!empty($contentManagerEntry->sortOrderChanged) && !empty($page->contentGroup)) {
                    $updateContentsSortOrderCommand->add($page->contentGroup, $sortOrder);
                    $updateContentsPositionCommand->add($page->contentGroup, $contentManagerEntry->id);
                }
                
                // As we are not saving any file type, we skip the SaveCommand
                if ($page->pageType === ContentType::FILE) {
                    continue;
                }
                // If the content was not changed, we skip the SaveCommand
                // If the content is NOT a new page, we skip the SaveCommand
                // If the content was not set to delete
                if ((empty($page->isNewPage) && empty($page->hasChanged)) && empty($page->isDeleted)) {
                    continue;
                }
                
                $page->sortOrder = $sortOrder;
                
                $optionEntity = PageOption::createFromJsonObject($page);
                $clearCache   = $clearCache || $optionEntity->requiresReload();
                
                /** @var AbstractContentManagerPagesSaveCommand $saveCommand */
                $saveCommand = $saveCommandFactory->createFromPageType($page->pageType);
                $saveCommand->setOption($optionEntity);
                
                // If the content was deleted, we set it to delete in the SaveCommand
                if (!empty($page->isDeleted)) {
                    $saveCommand->setToDelete();
                }
                
                $commandList[] = $saveCommand;
            }
        }
    
        $commandList[] = $updateContentsSortOrderCommand;
        $commandList[] = $updateContentsPositionCommand;
        
        if ($clearCache || !$themeConfiguration->isPreview()) {
            $clearCacheCommand = SingletonPrototype::instance()->get(ClearCacheCommand::class);
            $clearCacheCommand->setClearShopCache(!$themeConfiguration->isPreview());
            
            $commandList[] = $clearCacheCommand;
        }
        $invoker = SingletonPrototype::instance()->get(TransactionalCommandInvoker::class);
        
        $invoker->runInsideTransaction($commandList);
    }
    
    
    /**
     * @param string             $themeId
     * @param                    $data
     *
     * @return void
     * @throws Exception
     */
    public function patch($themeId, $data): void
    {
        if (isset($data->active) && $data->active) {
            /**
             * @var ThemeActivatorAdapterInterface $themeActivator
             */
            $themeActivator = SingletonPrototype::instance()->get(ThemeActivatorAdapterInterface::class);
            if ($themeActivator) {
                $themeActivator->activateTheme($themeId);
            }
            
            unset($data->active);
        }
        
        if (property_exists($data, 'options')) {
            $options = $data->options;
            unset($data->options);
        } else {
            $options = null;
        }
    
        if (isset($data->contentManager)) {
            $contentManager = $data->contentManager;
            unset($data->contentManager);
        }
        
        $themeConfiguration = $this->getConfigurationById($themeId);
        $this->configurationRepository()->patch($themeConfiguration, $data);
        
        if (isset($options)) {
            $this->saveSettings($options, $themeConfiguration);
        }
        
        if (isset($contentManager)) {
            $this->saveContentManagerPages($contentManager, $themeConfiguration);
        }
    }
    
    
    /**
     * @param string $themeId
     *
     * @return ThemeConfiguration
     * @throws FileNotFoundException
     * @throws TranslatedException
     */
    public function getConfigurationById($themeId): ThemeConfiguration
    {
        return $this->configurationRepository()->getById($themeId);
    }
    
    
    /**
     * @return ThemeConfigurationRepository
     * @throws Exception
     */
    protected function configurationRepository(): ThemeConfigurationRepository
    {
        if ($this->configurationRepository === null) {
            $this->configurationRepository = ThemeConfigurationRepository::create();
            if (!$this->configurationRepository) {
                throw new Exception('ConfigurationRepository was not initialized');
            }
        }
        
        return $this->configurationRepository;
    }
    
    
    /**
     *
     */
    public function __clone()
    {
        $this->configuration = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
    }
    
    
    /**
     * @throws Exception
     */
    public function updateThemeList()
    {
        $list = $this->configurationRepository()->get();
        SingletonPrototype::instance()->setUp(ThemeConfigurationCollection::class, $list);
    }
    
    
    /**
     * @param array $themes
     *
     * @return array
     */
    public function sortByActive(array $themes): array
    {
        usort($themes,
            function (ThemeConfiguration $a, ThemeConfiguration $b) {
                
                if (!$a->isActive() && $b->isActive()) {
                    return 1;
                } elseif ($a->isActive() && !$b->isActive()) {
                    return -1;
                }
                
                return 0;
            });
        
        return $themes;
    }
    
    
    /**
     * @param $themeId
     *
     * @return bool
     * @throws Exception
     */
    public function exists($themeId): bool
    {
        return $this->repository()->exists($themeId);
    }
    
}
