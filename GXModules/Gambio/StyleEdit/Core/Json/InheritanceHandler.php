<?php
/*--------------------------------------------------------------------------------------------------
    InheritanceHandler.php 2019-10-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2019 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Json;

use Directory;
use Exception;
use FileNotFoundException;
use Gambio\StyleEdit\Core\Json\Interfaces\InheritanceResolverInterface;
use Gambio\StyleEdit\Core\SingletonPrototype;
use RuntimeException;
use stdClass;

/**
 * Class Extender
 * @package Gambio\StyleEdit\Core\Json
 */
class InheritanceHandler
{
    public const APPEND   = 'APPEND';
    public const MERGE    = 'MERGE';
    public const DELETE   = 'DELETE';
    public const OVERRIDE = 'OVERRIDE';
    /**
     * @var Directory|false|null
     */
    protected $basePath;
    /**
     * @var FileIO
     */
    protected $fileIO;
    /**
     * @var string
     */
    protected $filename;
    /**
     * @var array
     */
    protected $files = [];
    /**
     * @var stdClass
     */
    protected $object;
    /**
     * @var InheritanceResolverInterface
     */
    protected $resolver;
    
    
    /**
     * Extender constructor.
     *
     * @param                                  $filename
     * @param FileIO                           $fileIO
     *
     * @param InheritanceResolverInterface     $resolver
     *
     * @throws Exception
     */
    public function __construct(string $filename, ?FileIO $fileIO, InheritanceResolverInterface $resolver)
    {
        $this->fileIO   = $fileIO ?? SingletonPrototype::instance()->get(FileIO::class);
        $this->resolver = $resolver;
        if ($filename) {
            $this->setFilename($filename);
        }
    }
    
    
    /**
     * @param $filename
     *
     * @return void
     * @throws Exception
     */
    protected function setFilename($filename): void
    {
        $this->filename = $filename;
        $this->setBasePath($filename);
        $this->object   = $this->loadMainFile($this->filename);
        $this->files    = $this->fileIO->listDirectoryFiles($this->basePath);
    }
    
    
    /**
     * @param $filename
     *
     * @return mixed|null
     * @throws Exception
     */
    protected function loadMainFile($filename): stdClass
    {
        if ($this->fileIO->exists($filename)) {
            return $this->fileIO->read($filename);
        }
        
        throw new Exception('Invalid file name!');
    }
    
    
    /**
     * @return mixed
     * @throws FileNotFoundException
     */
    public function execute()
    {
        if ($this->object === null) {
            throw new RuntimeException('Object set!');
        }
        $element = $this->handleInheritance($this->object);
        
        if (count($this->files)) {
            return $this->handleExtension($element);
        } else {
            return $element;
        }
    }

    
    /**
     * reads mount the parent element and apply current information into it.
     *
     * @param $element
     *
     * @return mixed|stdClass|null
     * @throws FileNotFoundException
     */
    protected function handleInheritance($element)
    {
        if (is_object($element) && isset($element->inherits)
            && $parent = $this->readParentObject($element->inherits)) {
            $extendType = $this->resolver->resolveInheritanceType($element->inherits, $this->filename);
            unset($element->inherits);
            
            if ($extendType === self::OVERRIDE) {
                $element = $this->overrideExtenderTo($element, $parent);
            } else {
                $element = $this->mergeExtenderTo($element, $parent);
            }
        }
        
        return $element;
    }
    
    
    /**
     * @param $element
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    protected function handleExtension($element)
    {
        
        if (is_object($element) && isset($element->id) && in_array($element->id . '.json', $this->files, true)) {
            $element = $this->mount($element);
        }
        
        if (is_object($element) || is_array($element)) {
            foreach ($element as $property => $value) {
                $newElementValue = $this->handleExtension($value);
                if (is_object($element)) {
                    if ($newElementValue === null) {
                        unset($element->$property);
                    } else {
                        $element->$property = $newElementValue;
                    }
                } elseif (is_array($element)) {
                    if ($newElementValue === null) {
                        unset($element[$property]);
                    } else {
                        $element[$property] = $newElementValue;
                    }
                }
            }
        }
        
        return $element;
    }
    
    
    /**
     * @param string $settings
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    protected function readParentObject($settings)
    {
        $filename = $this->resolver->resolveInheritanceFileName($settings, $this->filename);
        $extender = clone $this;
        $extender->setFilename($filename);
        return $extender->execute();
    }
    
    
    /**
     * @param stdClass $extender
     * @param          $object
     *
     * @return mixed
     */
    protected function overrideExtenderTo(stdClass $extender, $object)
    {
        foreach ($extender as $property => $value) {
            if (isset($object->$property) && is_array($object->$property) && !is_array($value)) {
                $object->$property = get_object_vars($value);
            } else {
                $object->$property = $value;
            }
        }
        
        return $object;
    }
    
    
    /**
     * @param stdClass $extender
     * @param stdClass $object
     *
     * @return stdClass
     */
    protected function mergeExtenderTo(stdClass $extender, stdClass $object): stdClass
    {
        foreach ($extender as $property => $value) {
            if (isset($object->$property)) {
                if (is_array($object->$property)) {
                    if (!is_array($value)) {
                        $value = get_object_vars($value);
                    }
                    $object->$property = array_merge($object->$property, $value);
                } elseif (is_object($object->$property) && is_object($value)) {
                    $object->$property = $this->mergeExtenderTo($value, $object->$property);
                } else {
                    $object->$property = $value;
                }
            } else {
                $object->$property = $value;
            }
        }
        
        return $object;
    }
    
    
    /**
     * @param $element
     *
     * @return mixed|stdClass|null
     * @throws Exception
     */
    protected function mount($element)
    {
        
        $extender = $this->loadExtensionFile($element->id . '.json');
        
        switch ($extender->extension_type ?? self::MERGE) {
            case self::OVERRIDE :
                unset($extender->extension_type);
                $element = $this->overrideExtenderTo($extender, $element);
                break;
            case self::DELETE :
                $element = null;
                break;
            default:
                if (isset($extender->extension_type)) {
                    unset($extender->extension_type);
                }
                $element = $this->mergeExtenderTo($extender, $element);
        }
        
        return $element;
    }
    
    
    /**
     * @param $filename
     *
     * @return mixed|null
     * @throws Exception
     */
    protected function loadExtensionFile($filename): ?stdClass
    {
        
        return $this->fileIO->read($this->basePath . $filename);
    }
    
    
    /**
     * @param $filename
     */
    protected function setBasePath(string $filename): void
    {
        $basePath = dirname($filename) . DIRECTORY_SEPARATOR . $this->resolver->jsonExtensionFolder().DIRECTORY_SEPARATOR;
        $this->basePath = preg_replace('/\/\//', DIRECTORY_SEPARATOR, $basePath) ;
    }
    
    public function __clone()
    {
        $this->filename = null;
        $this->basePath = null;
        $this->object   = null;
        $this->fileIO   = $this->fileIO;
    }
    
    
}