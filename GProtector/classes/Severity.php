<?php

/* --------------------------------------------------------------
  Severity.php 2020-02-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

class Severity
{
    /**
     * @var string $severity
     */
    private $severity;
    
    
    /**
     * Initializes severity instance inside this class
     *
     * Severity constructor.
     *
     * @param $severity
     */
    public function __construct($severity = 'error')
    {
        $this->validateSeverity($severity);
        $this->severity = $severity;
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
    
    
    /**
     * Validates severity
     *
     * @param mixed $severity severity to be validated
     *
     * @throws InvalidArgumentException
     *
     */
    private function validateSeverity($severity)
    {
        $validSeverities = ['error', 'warning', 'notice'];
        if(in_array($severity, $validSeverities) === false) {
            throw new InvalidArgumentException('Invalid $severity');
        }
    }
}