<?php
/* --------------------------------------------------------------
  Option.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Services\Configuration\Entities;

use Gambio\StyleEdit\Core\Services\Configuration\Entities\Interfaces\OptionInterface;
use JsonSerializable;
use stdClass;

/**
 * Class Option
 */
class Option implements OptionInterface, JsonSerializable
{
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var mixed
     */
    protected $value;
    
    /**
     * @var string
     */
    protected $group;
    
    /**
     * @var string|null
     */
    protected $type;
    
    
    /**
     * Option constructor.
     *
     * @param string      $name
     * @param string      $group
     * @param string|null $type
     * @param             $value
     */
    public function __construct(string $name, string $group, ?string $type, $value)
    {
        $this->name  = $name;
        $this->group = $group;
        $this->type  = $type;
        $this->value = $value;
    }
    
    
    /**
     * @return string|null
     */
    public function type(): ?string
    {
        return $this->type;
    }
    
    
    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    
    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
    
    
    /**
     * @return string
     */
    public function group(): string
    {
        return $this->group;
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
        $result        = new stdClass;
        $result->name  = $this->name();
        $result->value = $this->value();
        $result->type  = $this->type();
        $result->group = $this->group();
        
        return $result;
    }
}