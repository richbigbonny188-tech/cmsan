<?php
/* --------------------------------------------------------------
   ConfigurationJsonReader.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Readers;

use Gambio\Admin\Modules\Configuration\App\Exceptions\ConfigurationDoesNotExist;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class ConfigurationJsonReader
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Readers
 */
class ConfigurationJsonReader
{
    /**
     * @var string
     */
    private $definitionsFilePath;
    
    /**
     * @var string
     */
    private $customDefinitionsFilePath;
    
    /**
     * @var TagJsonReader
     */
    private $tagReader;
    
    /**
     * @var TextManager
     */
    private $textManager;
    
    /**
     * @var array
     */
    private $configurations;
    
    
    /**
     * ConfigurationJsonReader constructor.
     *
     * @param string        $definitionsFilePath
     * @param string        $customDefinitionsFilePath
     * @param TagJsonReader $tagReader
     * @param TextManager   $textManager
     */
    public function __construct(
        string $definitionsFilePath,
        string $customDefinitionsFilePath,
        TagJsonReader $tagReader,
        TextManager $textManager
    ) {
        $this->definitionsFilePath       = $definitionsFilePath;
        $this->customDefinitionsFilePath = $customDefinitionsFilePath;
        $this->tagReader                 = $tagReader;
        $this->textManager               = $textManager;
    }
    
    
    /**
     * @return array
     */
    public function getConfigurationsData(): array
    {
        return array_values($this->configurations());
    }
    
    
    /**
     * @param string $key
     *
     * @return array
     *
     * @throws ConfigurationDoesNotExist
     */
    public function getConfigurationDataByKey(string $key): array
    {
        if (array_key_exists($key, $this->configurations()) === false) {
            throw ConfigurationDoesNotExist::withKey($key);
        }
        
        return $this->configurations()[$key];
    }
    
    
    /**
     * @return array
     */
    private function configurations(): array
    {
        if ($this->configurations === null) {
            $this->configurations = [];
            if (file_exists($this->definitionsFilePath)) {
                if (file_exists($this->customDefinitionsFilePath)) {
                    $customDefinitions = @json_decode(file_get_contents($this->customDefinitionsFilePath), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $customDefinitions = [];
                    }
                }
                
                $definitions = @json_decode(file_get_contents($this->definitionsFilePath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $definitions          = array_merge_recursive($definitions, $customDefinitions ?? []);
                    $this->configurations = $this->parseDefinitions($definitions['configurations'] ?? []);
                }
            }
        }
        
        return $this->configurations;
    }
    
    
    /**
     * @param array $definitions
     *
     * @return array
     */
    private function parseDefinitions(array $definitions): array
    {
        $configurations = [];
        foreach ($definitions as $definition) {
            if ($this->validateDefinition($definition)) {
                $configurations[$definition['key']] = [
                    'key'     => $definition['key'],
                    'label'   => $this->textManager->getPhraseText($definition['label']['phrase'],
                                                                   $definition['label']['section']),
                    'tooltip' => $this->textManager->getPhraseText($definition['tooltip']['phrase'],
                                                                   $definition['tooltip']['section']),
                    'type'    => $definition['type'],
                    'tags'    => $this->tagReader->findMultipleTagsData(...$definition['tags']),
                ];
            }
        }
        
        return $configurations;
    }
    
    
    private function validateDefinition($definition): bool
    {
        return is_array($definition)
               && array_key_exists('key', $definition)
               && array_key_exists('label', $definition)
               && is_array($definition['label'])
               && array_key_exists('section', $definition['label'])
               && array_key_exists('phrase', $definition['label'])
               && array_key_exists('tooltip', $definition)
               && is_array($definition['tooltip'])
               && array_key_exists('section', $definition['tooltip'])
               && array_key_exists('phrase', $definition['tooltip'])
               && array_key_exists('type', $definition)
               && is_array($definition['type'])
               && array_key_exists('id', $definition['type'])
               && array_key_exists('params', $definition['type'])
               && array_key_exists('tags', $definition)
               && is_string($definition['key'])
               && is_array($definition['tags'])
               && is_string($definition['label']['phrase'])
               && is_string($definition['label']['section'])
               && is_string($definition['tooltip']['section'])
               && is_string($definition['tooltip']['phrase'])
               && is_string($definition['type']['id'])
               && is_array($definition['type']['params']);
    }
}