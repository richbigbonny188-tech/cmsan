<?php
/*--------------------------------------------------------------------------------------------------
    SettingsService.php 2021-03-23
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Services;

use Exception;
use Gambio\StyleEdit\Core\Repositories\Entities\Configuration;
use Gambio\StyleEdit\Core\Repositories\Entities\ConfigurationCollection;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;

/**
 * Class SettingsService
 */
class SettingsService
{
    /**
     * @var int
     */
    protected $boxPosition = 1;
    /**
     * @var ConfigurationCollection
     */
    protected $configurationsList;
    /**
     * @var string
     */
    protected $currentThemeId;
    /**
     * @var string
     */
    protected $errorMessage = '';
    
    /**
     * @var array
     */
    protected $groups = [];
    
    
    /**
     * SettingsService constructor.
     *
     * @param string $currentThemeId active theme
     */
    public function __construct(string $currentThemeId)
    {
        $this->currentThemeId = $currentThemeId;
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
        $scss = '';
        
        $group = $this->getScssByGroup($p_group);
        
        if (count($group) > 0) {
            
            foreach ($group as $variableName => $variableValue) {
                
                if (is_string($variableValue)) {
                    
                    $scss .= '$' . $variableName . ': ' . $variableValue . ';' . PHP_EOL;
                } elseif (is_bool($variableValue)) {
                    
                    $scss .= '$' . $variableName . ': ' . ($variableValue === true ? 'true' : 'false') . ';' . PHP_EOL;
                }
            }
        }
        
        return $scss;
    }
    
    
    /**
     * @param $p_group
     *
     * @return array
     * @throws Exception
     */
    public function getScssByGroup($p_group): array
    {
        $result = [];

        if (empty($this->groups)) {
            // Initialize $this->groups property
            $this->configurationsList();
        }

        if (isset($this->groups[$p_group])) {
            /** @var  Configuration $item */
            foreach ($this->groups[$p_group] as $item) {
                $value = $item->value();
                
                if (is_string($value) || is_bool($value) || is_object($value)) {
                    
                    if (is_string($value) && $value !== '0' && empty(trim($value))) {
                        continue;
                    }
                    switch ($item->type()) {
                        case 'url':
                        case 'imageupload':
                            $result[$item->id()] = '\'' . $value . '\'';
                            break;
                        default:
                            $result[$item->id()] = $value;
                    }
                }
            }
        }
        
        return $result;
    }
    
    
    /**
     * Used by StyleEditReaderWrapper
     */
    
    /**
     * Returns the custom styles from a JSON File
     *
     * @return string
     * @throws Exception
     */
    public function getCustomStyles(): string
    {
        $scss  = '';
        $group = $this->getScssByGroup('custom');
        
        if (count($group) > 0) {
            
            foreach ($group as $variableName => $variableValue) {
                $scss .= $variableValue . PHP_EOL;
            }
        }
        
        return $scss;
    }
    
    
    /**
     * Searches for a setting value identified by its name. If no result is found, null will be returned.
     *
     * @param $p_settingName
     *
     * @return mixed|null
     * @throws Exception
     */
    public function findSettingValueByName($p_settingName)
    {
        /** @var  Configuration $item */
        if ($this->configurationsList()->keyExists($p_settingName)) {
            
            return $this->configurationsList()->getValue($p_settingName)->value();
        }
        
        return null;
    }
    
    
    /**
     * @return ConfigurationCollection
     * @throws Exception
     */
    public function configurationsList()
    {
        if ($this->configurationsList === null) {
            $rep = SettingsRepository::createForTheme($this->currentThemeId);
            $this->configurationsList = $rep->configurationsList();

            /** @var  Configuration $item */
            foreach ($this->configurationsList()->getIterator() as $item) {
                if ($item->group() !== null) {
                    if (!isset($this->groups[$item->group()])) {
                        $this->groups[$item->group()] = [];
                    }
                    $this->groups[$item->group()][] = $item;
                }
            }
        }
        
        return $this->configurationsList;
    }
    
    
    /**
     * Returns error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}