<?php
/*--------------------------------------------------------------------------------------------------
    OrdainedListOption.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\OrdainedList\Entities;

use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentGroupOption;
use ReflectionException;

/**
 * Class OrdainedListOption
 * @package Gambio\StyleEdit\Core\Components\OrdainedList\Entities
 */
class OrdainedListOption extends AbstractComponentGroupOption
{
    
    /**
     * @param $value
     *
     * @return boolean
     */
    protected function isValid($value): bool
    {
        return is_array($value);
    }
    
    
    /**
     * @param $value
     *
     * @return mixed
     * @throws ReflectionException
     */
    protected function parseValue($value)
    {
        $result = [];
        foreach ($value as $index => $valueItem) {
            $settings            = ['type' => 'ordainedlistitem'];
            $settings['default'] = (property_exists($valueItem, 'default')) ? $valueItem->default : null;
            $settings['value']   = (property_exists($valueItem, 'value')) ? $valueItem->value : null;
            $settings['label']   = (property_exists($valueItem, 'label')) ? $valueItem->label : null;
            $settings['id']      = (property_exists($valueItem, 'id')) ? $valueItem->id : null;
            
            if (isset($valueItem->group)) {
                $settings['group'] = $valueItem->group;
            }
            
            $result[] = OrdainedListItemOption::createFromJsonObject((object)$settings,
                                                                     '',
                                                                     $this->configurationRepository);
        }
        
        uasort($result,
            function ($el1, $el2) {
                $pos1 = $el1->value()->position;
                $pos2 = $el2->value()->position;
                
                if ($pos1 == $pos2) {
                    return 0;
                }
                
                return ($pos1 < $pos2) ? -1 : 1;
            });
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    public function type(): ?string
    {
        return 'ordainedlist';
    }
    
    
    /**
     * @return mixed
     */
    public function getGroupOptions()
    {
        return $this->value();
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
        $result   = parent::jsonSerialize();
        $newValue = [];
        foreach ($this->value as $value) {
            $newValue[] = (object)[
                'label' => $value->label(),
                'id'    => $value->id(),
                'value' => $value->value(),
                'group' => $value->group() ?? null
            ];
        }
        $result->value = $newValue;
        
        return $result;
    }
    
    
    /**
     * @param $object
     *
     * @throws \Exception
     */
    public function initializeFromJsonObject($object): void
    {
        if (!isset($object->value)) {
            $object->value = $object->default;
        }
        
        parent::initializeFromJsonObject($object);
    }
    
    
    /**
     * clone the instance and clean the values
     */
    public function __clone()
    {
        parent::__clone();
        $this->value = [];
    }
}