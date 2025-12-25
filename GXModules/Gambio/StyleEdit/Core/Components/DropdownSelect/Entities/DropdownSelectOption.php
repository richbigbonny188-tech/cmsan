<?php
/* --------------------------------------------------------------
   DropdownSelectOption.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\StyleEdit\Core\Components\DropdownSelect\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;

/**
 * Class DropdownSelectOption
 * @package Gambio\StyleEdit\Core\Components\DropdownSelect\Entities
 */
class DropdownSelectOption extends AbstractComponentOption
{
    /**
     * @var array of strings
     */
    protected $options = [];
    
    
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
        return $value;
    }
    
    
    /**
     * @param $object
     *
     * @throws \Exception
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        
        if (isset($object->options)) {
            $this->options = $object->options;
        }
    }
    
    
    /**
     * @return array
     */
    protected function options(): array
    {
        return $this->options;
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
        
        if ($this->options()) {
            $result->options = $this->options();
        }
        
        return (object)$result;
    }
    
    
    /**
     * @return string|void|null
     */
    public function type(): string
    {
        return 'selectbox';
    }
}