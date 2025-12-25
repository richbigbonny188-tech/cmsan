<?php
/*--------------------------------------------------------------------------------------------------
    VariantValue.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Variant\Entities;

use Gambio\StyleEdit\Core\SingletonPrototype;
use JsonSerializable;

/**
 * Class VariantValue
 * @package Gambio\StyleEdit\Core\Components\Variant\Entities
 */
class VariantValue implements JsonSerializable
{
    protected $id;
    protected $title;
    protected $thumbnail;
    protected $dir;
    
    
    /**
     * @return mixed
     */
    public function id(): ?string
    {
        return $this->id;
    }
    
    
    /**
     * @return mixed
     */
    public function title(): ?string
    {
        return $this->title;
    }
    
    
    /**
     * @return mixed
     */
    public function thumbnail(): ?string
    {
        return $this->thumbnail;
    }
    
    
    /**
     * @return mixed
     */
    public function dir(): ?string
    {
        return $this->dir;
    }
    
    
    /**
     * @param $object
     */
    public function initializeFromJsonObject($object): void
    {
        $this->id        = property_exists($object, 'id') ? $object->id : null;
        $this->title     = property_exists($object, 'title') ? $object->title : null;
        $this->thumbnail = property_exists($object, 'thumbnail') ? $object->thumbnail : null;
        if (property_exists($object, 'dir') && isset($object->dir)) {
            $this->dir = $object->dir;
        }
    }
    
    
    /**
     * @param $jsonObject
     *
     * @return VariantValue
     */
    public static function createFromJsonObject($jsonObject): VariantValue
    {
        $result = SingletonPrototype::instance()->get(static::class);
        $result->initializeFromJsonObject($jsonObject);
        
        return $result;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = [];
        if ($this->id()) {
            $result['id'] = $this->id();
        }
        
        if ($this->title()) {
            $result['title'] = $this->title();
        }
        
        if ($this->thumbnail()) {
            $result['thumbnail'] = $this->thumbnail();
        }
        
        if ($this->dir()) {
            $result['dir'] = $this->dir();
        }
        
        return (object)$result;
    }
}