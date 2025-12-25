<?php
/*--------------------------------------------------------------------------------------------------
    CheckboxOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\Checkbox\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use stdClass;

/**
 * Class CheckboxOption
 * @package Gambio\StyleEdit\Core\Components\Checkbox\Entities
 */
class CheckboxOption extends AbstractComponentOption
{
    /**
     * @var stdClass
     */
    protected $values;
    
    
    /**
     * @param $value
     *
     * @return boolean
     */
    protected function isValid($value): bool
    {
        return true;
    }
    
    
    /**
     * @param $value
     *
     * @return mixed
     */
    protected function parseValue($value)
    {
        if (is_object($value) && $value->checked) {
            return $value->checked;
        }
        
        return $value;
    }
    
    
    /**
     * @return string
     */
    public function type(): string
    {
        return 'checkbox';
    }
    
    /**
     * @param bool $value
     */
    public function setValue(bool $value): void
    {
        $this->value = $value;
    }
    
    
    /**
     * @param $object
     *
     * @throws \Exception
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        
        if (!empty($object->values)) {
            $this->values = $object->values;
        }
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
        $result = parent::jsonSerialize();
        
        if (!empty($this->values)) {
            $result->values = $this->values;
        }
        
        return $result;
    }
}