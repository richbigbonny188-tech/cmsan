<?php
/*--------------------------------------------------------------------------------------------------
    StyleEdit4ReaderWrapper.inc.php 2021-07-29
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\GX\Services\System\ThemeSettings\Factories\ThemeSettingsDataFactory;
use Gambio\GX\Services\System\ThemeSettings\ThemeSettingsService;
use Gambio\StyleEdit\Core\Services\MenuBoxService;
use Gambio\StyleEdit\Core\Services\SettingsService;

/**
 * Class StyleEdit4ReaderWrapper
 */
class StyleEdit4ReaderWrapper implements StyleEditReaderInterface
{
    /**
     * @var SettingsService
     */
    protected $reader;
    
    /**
     * @var MenuBoxService
     */
    protected $boxService;
    
    
    /**
     * StyleEdit4ReaderWrapper constructor.
     *
     * @param string $themeId
     *
     * @throws Exception
     */
    public function __construct(string $themeId)
    {
        $this->validateSettingsJson($themeId);
        
        $this->reader     = new SettingsService($themeId);
        $this->boxService = new MenuBoxService($this->reader);
    }
    
    
    /**
     * Get Sass Code by group and style name
     *
     * @param string $p_group
     *
     * @return string
     * @throws Exception
     */
    public function getScss($p_group): string
    {
        return $this->reader->getScss($p_group);
    }
    
    
    /**
     * Returns the custom styles from a JSON File
     *
     * @return string
     * @throws Exception
     */
    public function getCustomStyles(): string
    {
        return $this->reader->getCustomStyles();
    }
    
    
    /**
     * Searches for a setting value identified by its name. If no result is found, null will be returned.
     *
     * @param $p_settingName
     *
     * @return mixed|null
     *
     * @throws Exception
     */
    public function findSettingValueByName($p_settingName)
    {
        return $this->reader->findSettingValueByName($p_settingName);
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function findSettingValuesByNames(...$settingsNames): array
    {
        $result = [];
        
        foreach ($settingsNames as $settingsName) {
            
            $result[$settingsName] = $this->findSettingValueByName($settingsName);
        }
        
        
        return $result;
    }
    
    
    /**
     * Returns error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->reader->getErrorMessage();
    }
    
    
    /**
     * Adapter Method
     *
     * @param string $p_boxName
     *
     * @return bool
     * @throws Exception
     */
    public function get_status($p_boxName): bool
    {
        return $this->boxService->getStatus($p_boxName);
    }
    
    
    /**
     * Adapter Method
     *
     * @param string $p_boxName
     *
     * @return string
     * @throws Exception
     */
    public function get_position($p_boxName): string
    {
        return $this->boxService->getPosition($p_boxName);
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws FileNotFoundException
     */
    protected function validateSettingsJson(string $themeId): void
    {
        $factory = MainFactory::create(ThemeSettingsDataFactory::class);
        if ($factory->createForTheme($themeId) === null) {
            /** @var ThemeSettingsService $themeSettingsService */
            $themeSettingsService = StaticGXCoreLoader::getService('ThemeSettings');
            /** This service sets the current theme in the database and in the theme.json */
            $themeSettingsService->activateTheme($themeId);
        }
    }

    /**
     * Adapter Method
     *
     * @param int $position
     *
     * @return bool
     *
     * @throws Exception
     */
    public function get_status_by_position($position): bool
    {
        return $this->boxService->getStatusByPosition($position);
    }
}