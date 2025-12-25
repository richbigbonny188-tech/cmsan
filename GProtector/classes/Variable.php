<?php

/* --------------------------------------------------------------
  Variable.php 2020-08-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace GProtector;

use \InvalidArgumentException;

class Variable
{
    /**
     * @var string $type
     */
    private $type;
    
    /**
     * @var array|string $properties
     */
    private $properties;
    
    /**
     * @var null|string $subCategory
     */
    private $subCategory;
    
    
    /**
     * Initializes variable instance
     *
     * Variable constructor.
     *
     * @param string       $type
     * @param array|string $properties
     * @param null|string  $subCategory
     */
    public function __construct($type, $properties, $subCategory = null)
    {
        if (!in_array(strtoupper($type), ['POST', 'GET', 'REQUEST'])) {
            throw new InvalidArgumentException('Invalid type');
        }
        
        if (!is_string($properties) && !is_array($properties)) {
            throw new InvalidArgumentException('Invalid properties');
        }
        
        if ($subCategory !== null && !is_string($subCategory)) {
            throw new InvalidArgumentException('Invalid sub category name');
        }
        
        $this->type        = $type;
        $this->properties  = $properties;
        $this->subCategory = $subCategory;
    }
    
    
    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }
    
    
    /**
     * @return array
     */
    public function properties()
    {
        return $this->properties;
    }
    
    
    /**
     * @return string|null
     */
    public function subCategory()
    {
        return $this->subCategory;
    }
    
    
    public function isSubcategory()
    {
        return $this->subCategory !== null && is_array($this->properties);
    }
}