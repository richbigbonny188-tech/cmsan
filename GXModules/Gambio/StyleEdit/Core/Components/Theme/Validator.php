<?php
/*--------------------------------------------------------------------------------------------------
    Validator.php 2019-10-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Theme;

use Exception;
use FileNotFoundException;
use FilesystemAdapter;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\Components\Theme\Json\ThemeInheritanceHandler;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\StyleEditConfiguration;
use stdClass;
use Throwable;

/**
 * Class Validator
 *
 * Checks if a theme can be opened in StyleEdit4.
 *
 * theme.json
 * * * * *  Must be a valid json
 * * * * *  content must be an object
 * * * * *  content must have an id
 * * * * *  content must have a config object
 * * * * * * * * *  with a basics & areas object
 */
class Validator implements AlwaysNewStrategyInterface
{
    /**
     * @var string
     */
    protected $themeName;
    
    /**
     * @var FilesystemAdapter
     */
    protected $filesystemThemes;
    
    /**
     * @var bool
     */
    protected $themeCanBeOpenedInStyleEdit4 = true;
    /**
     * @var StyleEditConfiguration
     */
    protected $styleEditConfiguration;
    
    
    /**
     * @param string                 $themeName
     *
     * @param FilesystemAdapter|null $adapter
     *
     * @return Validator
     * @throws Exception
     */
    public static function for(string $themeName, ?FilesystemAdapter $adapter = null): self
    {
        // the SingletonPrototype can only construct this if the inject method of the dependencyInjector class was executed!
        $filesystemThemes       = $adapter ?? SingletonPrototype::instance()->get(FilesystemAdapter::class);
        $styleEditConfiguration = SingletonPrototype::instance()->get(StyleEditConfiguration::class);
        
        $instance = SingletonPrototype::instance()
            ->get(static::class, $themeName, $filesystemThemes, $styleEditConfiguration);
        
        return $instance;
    }
    
    
    /**
     * Validator constructor.
     *
     * @param string                 $themeName
     * @param FilesystemAdapter      $filesystemThemes
     *
     * @param StyleEditConfiguration $styleEditConfiguration
     *
     * @throws FileNotFoundException
     */
    public function __construct(
        string $themeName,
        FilesystemAdapter $filesystemThemes,
        StyleEditConfiguration $styleEditConfiguration
    ) {
        $this->themeName              = $themeName;
        $this->filesystemThemes       = $filesystemThemes;
        $this->styleEditConfiguration = $styleEditConfiguration;
        
        $this->validateThemeJson();
    }
    
    
    /**
     * @return string
     */
    protected function getThemesFolder(): string
    {
        return $this->styleEditConfiguration->themesFolderPath();
    }
    
    /**
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    protected function validateThemeJson(): void
    {
        try {
            $themeJsonPath = $this->themeName . DIRECTORY_SEPARATOR . 'theme.json';
            
            if (!$this->filesystemThemes->has($themeJsonPath)) {
                $this->themeCanBeOpenedInStyleEdit4 = false;
                
                return;
            }
            
            $jsonInheritanceHandler = SingletonPrototype::instance()->get(ThemeInheritanceHandler::class);
            $jsonInheritanceHandler->setFilename($this->getThemesFolder() . $themeJsonPath);
            $themeJson = $jsonInheritanceHandler->execute();
            
            if (!$themeJson instanceof stdClass
                || !isset($themeJson->id, $themeJson->config, $themeJson->config->basics, $themeJson->config->areas)
                || !$themeJson->config->basics instanceof stdClass
                || !$themeJson->config->areas instanceof stdClass
            ) {
                $this->themeCanBeOpenedInStyleEdit4 = false;
            }
        } catch (Throwable $exception) {
            $this->themeCanBeOpenedInStyleEdit4 = false;
        }
    }
    
    
    /**
     * @return bool
     */
    public function canBeOpenedInStyleEdit4(): bool
    {
        return $this->themeCanBeOpenedInStyleEdit4;
    }
}