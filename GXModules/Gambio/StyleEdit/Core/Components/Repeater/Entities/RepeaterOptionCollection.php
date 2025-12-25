<?php
/*--------------------------------------------------------------------------------------------------
    RepeaterOptionCollection.php 2023-05-30
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace GXModules\Gambio\StyleEdit\Core\Components\Repeater\Entities;

use EditableCollection;
use Exception;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Options\Entities\AbstractOption;
use Gambio\StyleEdit\Core\Options\Entities\OptionInterface;
use JsonSerializable;
use RuntimeException;

/**
 *
 */
class RepeaterOptionCollection extends EditableCollection implements JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->collectionContentArray;
    }
    
    
    /**
     * @param mixed $value
     *
     * @return self
     * @throws Exception
     */
    public function addItem($value): self
    {
        if ($value instanceof self) {
            return $this->addCollection($value);
        }
        
        if ($value instanceof AbstractOption) {
            return parent::addItem($value);
        }
        
        throw new RuntimeException('Collection must be an instance of ' . self::class . ' or ' . AbstractOption::class);
    }
    
    
    /**
     * Filters the repeater option's value by the given ID (repeaterField)
     *
     * @param string $field
     * @param Language|null $language
     *
     * @return mixed
     */
    public function getValueByField(string $field, ?Language $language = null)
    {
        $filteredValues = $this->getOptionByRepeaterField($field);
        
        if (!$filteredValues) {
            return '';
        }
        
        $fieldValue = $filteredValues->value($language);
        return $fieldValue->value ?? $fieldValue;
    }
    
    
    /**
     * Gets an option by the given `repeaterField` value
     *
     * @param string $field
     *
     * @return OptionInterface|null
     */
    public function getOptionByRepeaterField(string $field): ?OptionInterface
    {
        $filteredValues = array_filter($this->getArray(), static fn($option): bool => $option->repeaterField() === $field);
        
        if (!$filteredValues) {
            return null;
        }
        
        return reset($filteredValues);
    }
    
    
    /**
     * Get valid type.
     *
     * This method must be implemented in the child-collection classes.
     *
     * @return string
     */
    protected function _getValidType(): string
    {
        return OptionInterface::class;
    }
}