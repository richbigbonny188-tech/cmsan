<?php
/*--------------------------------------------------------------------------------------------------
    RadioOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\RadioImage\Entities;

use Gambio\StyleEdit\Core\SingletonPrototype;
use JsonSerializable;

/**
 * Class RadioImageValue
 * @package Gambio\StyleEdit\Core\Components\RadioImage\Entities
 */
class RadioImageValue implements JsonSerializable
{
    
    /**
     * @var string Option Id
     */
    protected $id;
    
    /**
     * @var String Thumbnail Url
     */
    protected $thumbnail;
    
    /**
     * @var String Title
     */
    protected $title;
    
    
    /**
     * @param $object
     */
    public function initializeFromJsonObject($object): void
    {
        $this->id        = $object->id;
        $this->title     = ($object->title ?? '');
        $this->thumbnail = $object->thumbnail;
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
        
        $result['id']        = $this->id();
        $result['thumbnail'] = $this->thumbnail();
        $result['title']     = $this->title();
        
        return (object)$result;
    }
    
    
    /**
     * @param $jsonObject
     *
     * @return bool|mixed
     */
    public static function createFromJsonObject($jsonObject)
    {
        $result = SingletonPrototype::instance()->get(static::class);
        
        $result->initializeFromJsonObject($jsonObject);
        
        return $result;
    }
    
    
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
    public function thumbnail(): ?string
    {
        return $this->thumbnail;
    }
    
    
    /**
     * @return mixed
     */
    public function title(): ?string
    {
        return $this->title;
    }
    
}