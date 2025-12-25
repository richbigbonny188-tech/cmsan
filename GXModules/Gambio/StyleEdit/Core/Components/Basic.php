<?php
/* --------------------------------------------------------------
   BasicElement.inc.php 2019-03-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components;

use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\TranslatedException;

/**
 * Class Basic
 * @package Gambio\StyleEdit\Core\Components
 */
abstract class Basic
{
    /**
     * @var string
     */
    protected $id = '';
    
    /**
     * @var array of ElementProperty
     */
    protected $properties = [];
    /**
     * @var array
     */
    protected $children = [];
    
    
    /**
     * @param $configurations
     *
     * @throws \Exception
     */
    public function initialize($configurations)
    {
        $this->initializeId($configurations);
        $this->initializeProperties($configurations);
        $this->initializeChildren($configurations);
    }
    
    
    /**
     * @param $configurations
     *
     * @throws \Gambio\StyleEdit\Core\TranslatedException
     */
    protected function initializeId($configurations)
    {
        if (!is_array($configurations) || !array_key_exists('id', $configurations)) {
            throw new TranslatedException('INVALID_COMPONENT_ID', [get_class($this)]);
        }
        
        $this->setId($configurations['id']);
    }
    
    
    /**
     * @param array|null $configuration
     *
     * @throws \Gambio\StyleEdit\Core\TranslatedException
     */
    protected function initializeProperties(array $configuration = null)
    {
        if (!array_key_exists('properties', $configuration) || !is_array($configuration['properties'])) {
            return;
        }
        
        foreach ($configuration['properties'] as $propertyConfig) {
            $property                            = new Property($propertyConfig);
            $this->properties[$property->name()] = $property;
        }
    }
    
    
    /**
     * @param array|null $configuration
     *
     * @throws \Gambio\StyleEdit\Core\TranslatedException
     */
    protected function initializeChildren(array $configuration = null)
    {
        if (!array_key_exists('children', $configuration) || !is_array($configuration['children'])) {
            return;
        }
        
        foreach ($configuration['children'] as $childConfig) {
            if (!array_key_exists('type', $childConfig)) {
                throw new TranslatedException('COMPONENT_TYPE_NOT_SUPPLIED');
            }
            
            $child = SingletonPrototype::instance()->get($childConfig['type']);
            if ($child) {
                $child->initialize($childConfig);
                if (array_key_exists($child->id, $this->children)) {
                    throw new TranslatedException('DUPLICATED_CHILD_ID',
                                                  [$child->id, $this->children[$child->id]->type()]);
                }
                $this->children[$child->id] = $child;
            } else {
                throw new TranslatedException('NOT_REGISTERED_CLASS', [$childConfig['type']]);
            }
        }
    }
    
    
    /**
     * @param $id
     *
     * @throws \Gambio\StyleEdit\Core\TranslatedException
     */
    protected function setId($id)
    {
        if (empty(trim($id))) {
            throw new TranslatedException('INVALID_COMPONENT_ID', [get_class($this)]);
        }
        
        $this->id = $id;
    }
    
    
    abstract public function type();
    
    
    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }
    
    
    public function properties()
    {
        return $this->properties;
    }
    
    
    public function children()
    {
        return $this->children;
    }
    
    
    /**
     * @param Object|Array $value Object/array to converted into Json
     */
    protected function outputJson($value)
    {
        header('Content-Type: application/json');
        echo json_encode($value);
    }
}