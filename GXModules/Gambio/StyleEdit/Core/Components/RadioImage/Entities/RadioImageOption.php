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

use Exception;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;

/**
 * Class RadioImageOption
 * @package Gambio\StyleEdit\Core\Components\RadioImage\Entities
 */
class RadioImageOption extends AbstractComponentOption
{
    /**
     * @var mixed
     */
    protected $options;
    
    
    /**
     * @return mixed
     */
    public function options()
    {
        return $this->options;
    }
    
    
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
     * @return object
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = parent::jsonSerialize();
        
        if ($this->options()) {
            $result->options = $this->options;
        }
        
        return (object)$result;
    }
    
    
    /**
     * @param $object
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        
        if (!isset($object->id)) {
            throw new Exception('Id is a mandatory property for Radio Image options');
        }
        $this->options = [];
        if (isset($object->options)) {
            foreach ($object->options as $option) {
                $this->options[] = RadioImageValue::createFromJsonObject($option);
            }
        }
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
     * @return string|void|null
     */
    public function type(): string
    {
        return 'radioimage';
    }
}
