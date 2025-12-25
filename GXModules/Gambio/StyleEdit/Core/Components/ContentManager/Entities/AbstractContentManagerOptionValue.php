<?php
/* --------------------------------------------------------------
  AbstractContentManagerOptionValue.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentManager\Entities;

use InvalidArgumentException, stdClass;

/**
 * Class AbstractContentManagerOptionValue
 */
abstract class AbstractContentManagerOptionValue
{
    /**
     * @var string
     */
    protected $value;
    
    /**
     * @var string
     */
    protected $title;
    
    
    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
    
    
    /**
     * AbstractContentManagerOptionValue constructor.
     *
     * @param string $value
     * @param string $title
     */
    public function __construct(string $value, string $title)
    {
        $this->value = $value;
        $this->title = $title;
    }
    
    
    /**
     * @param stdClass $jsonObject
     *
     * @return AbstractContentManagerOptionValue
     */
    public static function createFromJsonObject(stdClass $jsonObject): self
    {
        if (!isset($jsonObject->value, $jsonObject->title)) {
            
            throw new InvalidArgumentException(static::class . ' needs to have a value and a title');
        }
        
        return new static($jsonObject->value, $jsonObject->title);
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
        $result = new stdClass;
        
        $result->value = $this->value();
        $result->title = $this->title();
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
    
    
    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
    
    
    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}