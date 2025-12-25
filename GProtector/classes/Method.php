<?php

/* --------------------------------------------------------------
  Method.php 2020-02-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

class Method
{
    /**
     * @var  string $method
     */
    private $method;
    
    
    /**
     * initializes method instance inside this class
     *
     * Method constructor.
     *
     * @param $method
     */
    public function __construct($method)
    {
        $this->validateMethod($method);
        $this->method = $method;
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
     * Validates a method
     *
     * @param mixed $method The method to validate
     *
     * @throws InvalidArgumentException
     *
     */
    private function validateMethod($method)
    {
        if ($method === null || (is_string($method)) === false) {
            throw new InvalidArgumentException('Invalid $method');
        }
    }
    
}