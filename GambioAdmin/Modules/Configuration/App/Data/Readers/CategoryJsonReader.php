<?php
/* --------------------------------------------------------------
   CategoryJsonReader.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Readers;

use Gambio\Admin\Modules\Configuration\App\Exceptions\CategoryDoesNotExist;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CategoryJsonReader
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Readers
 */
class CategoryJsonReader
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
    private $categories;
    
    
    /**
     * CategoryJsonReader constructor.
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
     * @return array
     */
    public function getCategoriesData(): array
    {
        return array_values($this->categories());
    }
    
    
    /**
     * @param string $id
     *
     * @return array
     *
     * @throws CategoryDoesNotExist
     */
    public function getCategoryDataById(string $id): array
    {
        if (array_key_exists($id, $this->categories()) === false) {
            throw CategoryDoesNotExist::withId($id);
        }
        
        return $this->categories()[$id];
    }
    
    
    /**
     * @return array
     */
    private function categories(): array
    {
        if ($this->categories === null) {
            $this->categories = [];
            if (file_exists($this->definitionsFilePath)) {
                if (file_exists($this->customDefinitionsFilePath)) {
                    $customDefinitions = @json_decode(file_get_contents($this->customDefinitionsFilePath), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $customDefinitions = [];
                    }
                }
                
                $definitions = @json_decode(file_get_contents($this->definitionsFilePath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $definitions      = array_merge_recursive($definitions, $customDefinitions ?? []);
                    $this->categories = $this->parseDefinitions($definitions['categories'] ?? []);
                }
            }
        }
        
        return $this->categories;
    }
    
    
    /**
     * @param array $definitions
     *
     * @return array
     */
    private function parseDefinitions(array $definitions): array
    {
        $categories = [];
        foreach ($definitions as $definition) {
            if ($this->validateDefinition($definition)) {
                $categories[$definition['id']] = [
                    'id'    => $definition['id'],
                    'label' => $this->textManager->getPhraseText($definition['label']['phrase'],
                                                                 $definition['label']['section']),
                ];
            }
        }
        
        return $categories;
    }
    
    
    private function validateDefinition($definition): bool
    {
        return is_array($definition)
               && array_key_exists('id', $definition)
               && array_key_exists('label', $definition)
               && is_array($definition['label'])
               && array_key_exists('section', $definition['label'])
               && array_key_exists('phrase', $definition['label'])
               && is_string($definition['id'])
               && is_string($definition['label']['phrase'])
               && is_string($definition['label']['section']);
    }
}