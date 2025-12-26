<?php
/*--------------------------------------------------------------------------------------------------
    RepeaterOption.php 2023-06-06
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\StyleEdit\Core\Components\Repeater\Entities;

use Exception;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\Components\Option\Entities\Option;
use Gambio\StyleEdit\Core\Components\Option\Entities\OptionCollection;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Gambio\StyleEdit\Core\Options\Entities\ComponentOptionAttributeCollection;
use GXModules\Gambio\StyleEdit\Core\Components\Repeater\Entities\RepeaterOptionCollection;
use ReflectionException;

/**
 *
 */
class RepeaterOption extends AbstractComponentOption implements AlwaysNewStrategyInterface
{
    /**
     * @var OptionCollection
     */
    protected OptionCollection $fields;
    
    
    /**
     * RepeaterOption constructor.
     *
     * @param string|null                             $label
     * @param ComponentOptionAttributeCollection|null $attributes
     */
    public function __construct(string $label = null, ComponentOptionAttributeCollection $attributes = null)
    {
        parent::__construct($label, $attributes);
        
        $this->fields = new OptionCollection();
    }
    
    
    /**
     * @inheritDoc
     */
    public function initializeFromJsonObject($object): void
    {
        parent::initializeFromJsonObject($object);
        
        if (isset($object->fields)) {
            foreach ($object->fields as $field) {
                $this->fields->addItem(Option::createFromJsonObject($field));
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $result         = parent::jsonSerialize();
        $result->fields = $this->fields->getArray();
        
        return $result;
    }
    
    
    /**
     * Returns the fields of the repeater
     *
     * @return OptionCollection
     */
    public function fieldsCollection(): OptionCollection
    {
        return $this->fields;
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isValid($value): bool
    {
        return is_array($value);
    }
    
    
    /**
     * @inheritDoc
     * @throws ReflectionException
     * @throws Exception
     */
    protected function parseValue($value)
    {
        $parsedValue = [];
        foreach ($value as $options) {
            $parsedValue[] = $this->createOptions($options);
        }
        
        return $parsedValue;
    }
    
    
    /**
     * Creates the Option for each repeater value
     *
     * @param array $options
     *
     * @return RepeaterOptionCollection
     * @throws ReflectionException
     * @throws Exception
     */
    private function createOptions(array $options): RepeaterOptionCollection
    {
        $repeaterCollection = new RepeaterOptionCollection([]);
        if (!$options) {
            return $repeaterCollection;
        }
        
        foreach ($options as $option) {
            if (isset($option->id)) {
                $repeaterCollection->addItem(Option::createFromJsonObject($option));
            }
        }
        
        return $repeaterCollection;
    }
    
    
    /**
     * @inheritDoc
     */
    public function type(): ?string
    {
        return 'repeater';
    }
}

