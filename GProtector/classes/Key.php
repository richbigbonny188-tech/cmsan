<?php

/* --------------------------------------------------------------
  Key.php 2021-03-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

class Key
{
    /**
     * @var  string $key
     */
    private $key;
    
    
    /**
     * Initializes Key instance
     *
     * Key constructor.
     *
     * @param $key
     */
    public function __construct($key)
    {
        $this->validateKey($key);
        $this->key = $key;
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
     * Validates key.
     *
     * @param mixed $key The key to be validated
     *
     * @throws InvalidArgumentException if key is null or not string
     *
     */
    private function validateKey($key)
    {
        if ($key === null || (is_string($key)) === false) {
            throw new InvalidArgumentException('Invalid $key');
        }
    }
}