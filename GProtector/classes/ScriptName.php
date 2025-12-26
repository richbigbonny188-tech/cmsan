<?php

/* --------------------------------------------------------------
  ScriptName.php 2020-02-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

class ScriptName
{
    
    /**
     * @var string $scriptName
     */
    private $scriptName;
    
    
    /**
     * Initializes script name instance inside this class
     *
     * ScriptName constructor.
     *
     * @param string $scriptName
     */
    public function __construct($scriptName)
    {
        $this->validateScriptName($scriptName);
        
        $this->scriptName = $scriptName;
    }
    
    
    /**
     * Getter for script name
     *
     * @return string
     */
    public function scriptName()
    {
        return $this->scriptName;
    }
    
    
    /**
     * Validates a script name
     *
     * @param string $scriptName The script name to be validated
     *
     * @throws InvalidArgumentException If the script name is null or not a string
     *
     */
    private function validateScriptName($scriptName)
    {
        if ($scriptName === null || is_string($scriptName) === false) {
            throw new InvalidArgumentException('Invalid $scriptName');
        }
    }
    
}