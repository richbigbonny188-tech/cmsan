<?php
/* --------------------------------------------------------------
  Filter.php 2020-07-31
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

class Filter
{
    /**
     * @var string $key
     */
    private $key;
    
    /**
     * @var array $scriptNames
     */
    private $scriptNames;
    
    /**
     * @var array $variables
     */
    private $variables;
    
    /**
     * @var string $method
     */
    private $method;
    
    /**
     * @var string $severity
     */
    private $severity;
    
    
    /**
     * Initializes filter instance inside this class
     *
     * GProtectorFilter constructor.
     *
     * @param Key                  $key
     * @param ScriptNameCollection $scriptNames
     * @param VariableCollection   $variables
     * @param Method               $method
     * @param Severity             $severity
     */
    private function __construct(
        Key $key,
        ScriptNameCollection $scriptNames,
        VariableCollection $variables,
        Method $method,
        Severity $severity
    ) {
        $this->key         = $key->key();
        $this->scriptNames = $scriptNames->getArray();
        $this->variables   = $variables->getArray();
        $this->method      = $method->method();
        $this->severity    = $severity->severity();
    }
    
    
    /**
     * This function creates new Filter objects
     *
     * @param $rawFilter
     *
     * @return Filter
     */
    
    public static function fromData($rawFilter)
    {
        $key = new Key($rawFilter['key']);
    
        $scriptNames = [];
        if (is_array($rawFilter['script_name'])) {
            foreach ($rawFilter['script_name'] as $scriptName) {
                $scriptNames[] = new ScriptName($scriptName);
            }
        } else {
            $scriptNames[] = new ScriptName($rawFilter['script_name']);
        }
        $scriptNameCollection = new ScriptNameCollection($scriptNames);
    
        $variables = [];
        foreach ($rawFilter['variables'] as $variableName) {
            $isSubcategory = isset($variableName['subcategory']) && is_array($variableName['property']);
            $variables[] = new Variable($variableName['type'], $variableName['property'], $isSubcategory ? $variableName['subcategory'] : null);
        }
        $variableCollection = new VariableCollection($variables);
        $method             = new Method($rawFilter['function']);
        $severity           = new Severity($rawFilter['severity']);
        
        return new static($key, $scriptNameCollection, $variableCollection, $method, $severity);
    }
    
    
    /**
     * Getter for key
     *
     * @return string
     */
    public function key()
    {
        return $this->key;
    }
    
    
    /**
     * Getter for script names
     *
     * @return array
     */
    public function scriptName()
    {
        return $this->scriptNames;
    }
    
    
    /**
     * Getter for variables
     *
     * @return array
     */
    public function variables()
    {
        return $this->variables;
    }
    
    
    /**
     * Getter for method
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }
    
    
    /**
     * Getter for severity
     *
     * @return string
     */
    public function severity()
    {
        return $this->severity;
    }
}