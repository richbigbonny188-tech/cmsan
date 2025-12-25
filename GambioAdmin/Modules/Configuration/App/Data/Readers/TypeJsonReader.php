<?php
/* --------------------------------------------------------------
   TypeJsonReader.php 2020-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Configuration\App\Data\Readers;

use Gambio\Admin\Modules\Configuration\App\Exceptions\TypeDoesNotExist;

/**
 * Class TypeJsonReader
 *
 * @package Gambio\Admin\Modules\Configuration\App\Data\Readers
 */
class TypeJsonReader
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
     * @var array
     */
    private $types;
    
    
    /**
     * TypeJsonReader constructor.
     *
     * @param string $definitionsFilePath
     * @param string $customDefinitionsFilePath
     */
    public function __construct(string $definitionsFilePath, string $customDefinitionsFilePath)
    {
        $this->definitionsFilePath       = $definitionsFilePath;
        $this->customDefinitionsFilePath = $customDefinitionsFilePath;
    }
    
    
    /**
     * @param string $id
     *
     * @return array
     *
     * @throws TypeDoesNotExist
     */
    public function getTypeDataById(string $id): array
    {
        if (array_key_exists($id, $this->types()) === false) {
            throw TypeDoesNotExist::withId($id);
        }
        
        return $this->types()[$id];
    }
    
    
    /**
     * @return array
     */
    private function types(): array
    {
        if ($this->types === null) {
            $this->types = [];
            if (file_exists($this->definitionsFilePath)) {
                if (file_exists($this->customDefinitionsFilePath)) {
                    $customDefinitions = @json_decode(file_get_contents($this->customDefinitionsFilePath), true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $customDefinitions = [];
                    }
                }
                
                $definitions = @json_decode(file_get_contents($this->definitionsFilePath), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $definitions = array_merge_recursive($definitions, $customDefinitions ?? []);
                    $this->types = $this->parseDefinitions($definitions['types'] ?? []);
                }
            }
        }
        
        return $this->types;
    }
    
    
    /**
     * @param array $definitions
     *
     * @return array
     */
    private function parseDefinitions(array $definitions): array
    {
        $types = [];
        foreach ($definitions as $definition) {
            if ($this->validateDefinition($definition)) {
                $types[$definition['id']] = [
                    'id'            => $definition['id'],
                    'defaultParams' => $definition['defaultParams'],
                ];
            }
        }
        
        return $types;
    }
    
    
    private function validateDefinition($definition): bool
    {
        return is_array($definition)
               && array_key_exists('id', $definition)
               && array_key_exists('defaultParams', $definition)
               && is_string($definition['id'])
               && is_array($definition['defaultParams']);
    }
}