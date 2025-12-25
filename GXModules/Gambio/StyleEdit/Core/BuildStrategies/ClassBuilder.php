<?php
/*--------------------------------------------------------------------------------------------------
    ClassBuilder.php 2022-05-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\BuildStrategies;

use Exception;
use Gambio\StyleEdit\Core\BuildStrategies\Exceptions\ContainerException;
use Gambio\StyleEdit\Core\BuildStrategies\Exceptions\NotFoundException;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\SingletonStrategyInterface;
use Gambio\StyleEdit\Core\Mapper\ContentGroupToContentAliasMapper;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Psr\Container\ContainerInterface;
use ReflectionParameter;

/**
 * Class ClassBuilder
 * @package Gambio\StyleEdit\Core\BuildStrategies
 */
abstract class ClassBuilder implements ContainerInterface
{
    /**
     *
     * @var SingletonPrototype
     */
    private static $instance;
    protected      $classSettings = [];
    protected      $objectList    = [];
    
    
    /**
     * SingletonPrototype constructor.
     */
    private function __construct()
    {
        $this->initialize();
    }
    
    
    /**
     * return the singleton instance for the prototype class
     * @return SingletonPrototype
     */
    public static function instance(): SingletonPrototype
    {
        if (self::$instance === null) {
            self::$instance = new SingletonPrototype();
        }
        
        return self::$instance;
    }
    
    
    /**
     * return a instance for the $className param
     *
     * @param string $className
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function get($className)
    {
        if($className == ContentGroupToContentAliasMapper::class)
        {
            $test = true;
        }
        $inputParams = func_get_args();
        array_shift($inputParams);
        
        if (array_key_exists($className, $this->objectList)) {
            $instance = $this->objectList[$className];
            
            if ((is_object($instance) && ($instance instanceof Closure))//unnamed function
                || (!is_string($instance) && is_callable($instance)))//callable array
            {
                $instance = $instance();
                if (!($instance instanceof AlwaysNewStrategyInterface)) {
                    $this->setUp($className, $instance);
                }
            }
            
            if ($instance instanceof SingletonStrategyInterface) {
                return $instance;
            } elseif (is_object($instance)) {
                return clone $instance;
            } else {
                return $instance;
            }
        } else {
            $info = $this->searchClassInfo($className);
            $canSetup = count($inputParams) === 0;

            if ($canSetup) {
                foreach ($info->params AS $param) {
                    /** @var ReflectionParameter $param */
                    try {
                        $paramType = $param->getType();
                        $paramValue = $paramType !== null ? $this->get($paramType->getName()) : null;
                    } catch (NotFoundException $e) {
                        if (!$param->allowsNull()) {
                            throw $e;
                        } else {
                            $paramValue = null;
                        }
                    }
                    $inputParams[] = $paramValue;
                }
            }
            
            $instance = $info->reflection->newInstanceArgs($inputParams);
            if (!($instance instanceof AlwaysNewStrategyInterface) && $canSetup) {
                $this->setUp($className, $instance);
            }
    
            if ($instance instanceof SingletonStrategyInterface) {
                return $instance;
            } else {
                return clone $instance;
            }
        }
        
        return false;
    }
    
    
    /**
     * Setup an instance of a specified class.
     *
     * @param string      $className
     * @param             $object
     */
    public function setUp(string $className, $object): void
    {
        $this->objectList[$className] = $object;
    }
    
    
    /**
     * @param $className
     *
     * @return mixed
     * @throws ContainerException
     * @throws NotFoundException
     */
    protected function searchClassInfo($className)
    {
        if (!array_key_exists($className, $this->classSettings) && class_exists($className)) {
            try {
                $reflection = new \ReflectionClass($className);
            } catch (\Exception $exception) {
                $this->classSettings[$className] = false;
                throw new ContainerException("Error: " . $exception->getMessage());
            }
            
            $this->classSettings[$className]['reflection'] = $reflection;
            
            if ($constructor = $reflection->getConstructor()) {
                $this->classSettings[$className]['params']      = $constructor->getParameters();
                $this->classSettings[$className]['constructor'] = $constructor->getParameters();
            } else {
                $this->classSettings[$className]['params'] = [];
            }
        } elseif (!isset($this->classSettings[$className]) || $this->classSettings[$className] === false) {
            throw new NotFoundException("Invalid class $className");
        }
        
        return (object)$this->classSettings[$className];
    }
    
    
    /**
     * Remove a prototype from the arrayList
     *
     * @param string $objectReference
     */
    public function remove(string $objectReference): void
    {
        if (array_key_exists($objectReference, $this->objectList)) {
            unset($this->objectList[$objectReference]);
        }
    }
    
    
    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->objectList) || class_exists($id);
    }
}