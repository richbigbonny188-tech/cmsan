<?php
/* --------------------------------------------------------------
   GroupReader.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Readers;

use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class GroupJsonReader
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Readers
 */
class GroupJsonReader
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
     * @var TextManager
     */
    private $textManager;
    
    /**
     * @var array
     */
    private $groups;
    
    
    /**
     * GroupJsonReader constructor.
     *
     * @param string      $definitionsFilePath
     * @param string      $customDefinitionsFilePath
     * @param TextManager $textManager
     */
    public function __construct(
        string $definitionsFilePath,
        string $customDefinitionsFilePath,
        TextManager $textManager
    ) {
        $this->definitionsFilePath       = $definitionsFilePath;
        $this->customDefinitionsFilePath = $customDefinitionsFilePath;
        $this->textManager               = $textManager;
    }
    
    
    /**
     * @param string $categoryId
     *
     * @return array
     */
    public function getGroupDataByCategoryId(string $categoryId): array
    {
        if (array_key_exists($categoryId, $this->groups()) === false) {
            return [];
        }
        
        return array_values($this->groups()[$categoryId]);
    }
    
    
    /**
     * @return array
     */
    private function groups(): array
    {
        if ($this->groups === null) {
            $this->groups = [];
            if (file_exists($this->definitionsFilePath)) {
                if (file_exists($this->customDefinitionsFilePath)) {
                    $customDefinitions = @json_decode(file_get_contents($this->customDefinitionsFilePath), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $customDefinitions = [];
                    }
                }
                
                $definitions = @json_decode(file_get_contents($this->definitionsFilePath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $definitions  = array_merge_recursive($definitions, $customDefinitions ?? []);
                    $this->groups = $this->parseDefinitions($definitions['groups'] ?? []);
                }
            }
        }
        
        return $this->groups;
    }
    
    
    /**
     * @param array $definitions
     *
     * @return array
     */
    private function parseDefinitions(array $definitions): array
    {
        $groups = [];
        foreach ($definitions as $definition) {
            if ($this->validateDefinition($definition)) {
                $groups[$definition['categoryId']][$definition['id']] = [
                    'id'             => $definition['id'],
                    'label'          => $this->textManager->getPhraseText($definition['label']['phrase'],
                                                                          $definition['label']['section']),
                    'configurations' => $definition['configurationKeys'],
                    'links'          => $this->parseLinks($definition),
                ];
            }
        }
        
        return $groups;
    }
    
    
    /**
     * @param $definition
     *
     * @return array
     */
    private function parseLinks($definition): array
    {
        $links = [];
        foreach ($definition['links'] as $linkDefinition) {
            if ($this->validateLinkDefinition($linkDefinition)) {
                $link = [
                    'label'     => $this->textManager->getPhraseText($linkDefinition['label']['phrase'],
                                                                     $linkDefinition['label']['section']),
                    'link'      => $linkDefinition['link'],
                    'newWindow' => $linkDefinition['newWindow'],
                ];
                
                if (isset($linkDefinition['buttonText'])) {
                    
                    $link['buttonText'] = $this->textManager->getPhraseText($linkDefinition['buttonText']['phrase'],
                                                                            $linkDefinition['buttonText']['section']);
                } else {
                    
                    $link['buttonText'] = $this->textManager->getPhraseText('open', 'buttons');
                }
                
                $links[] = $link;
            }
        }
        
        return $links;
    }
    
    
    /**
     * @param $definition
     *
     * @return bool
     */
    private function validateDefinition($definition): bool
    {
        return is_array($definition)
               && array_key_exists('id', $definition)
               && array_key_exists('categoryId', $definition)
               && array_key_exists('label', $definition)
               && is_array($definition['label'])
               && array_key_exists('section', $definition['label'])
               && array_key_exists('phrase', $definition['label'])
               && array_key_exists('configurationKeys', $definition)
               && array_key_exists('links', $definition)
               && is_string($definition['id'])
               && is_string($definition['categoryId'])
               && is_string($definition['label']['phrase'])
               && is_string($definition['label']['section'])
               && is_array($definition['configurationKeys'])
               && is_array($definition['links']);
    }
    
    
    private function validateLinkDefinition($linkDefinition): bool
    {
        return array_key_exists('label', $linkDefinition) && is_array($linkDefinition['label'])
               && array_key_exists('phrase', $linkDefinition['label'])
               && array_key_exists('section', $linkDefinition['label'])
               && array_key_exists('link', $linkDefinition)
               && is_string($linkDefinition['link'])
               && array_key_exists('newWindow', $linkDefinition)
               && is_bool($linkDefinition['newWindow']);
    }
}