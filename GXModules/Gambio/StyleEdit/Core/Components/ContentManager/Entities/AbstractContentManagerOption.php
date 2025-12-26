<?php
/*--------------------------------------------------------------------------------------------------
    AbstractContentManagerOption.php 2023-05-31
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\StyleEdit\Core\Components\ContentManager\Entities;

use ContentIdentificationFactoryInterface;
use ContentIdentificationInterface;
use ContentNotFoundException;
use ContentReadServiceInterface;
use ContentText;
use Exception;
use Gambio\StyleEdit\Core\Options\Entities\AbstractComponentOption;
use Gambio\StyleEdit\Core\Options\Entities\ComponentOptionAttribute;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class AbstractContentManagerOption
 */
abstract class AbstractContentManagerOption extends AbstractComponentOption
{
    /**
     * @var ContentIdentificationInterface
     */
    protected $contentIdentification;
    
    /**
     * @var boolean
     */
    protected $useEditorContent;
    
    
    /**
     * @param $object
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($object): void
    {
        if (isset($object->label)) {
            $this->label = $object->label;
        }
        
        if (isset($object->attributes)) {
            foreach ($object->attributes as $key => $attribute) {
                $this->attributes->setValue($key, ComponentOptionAttribute::create($key, $attribute));
            }
            $this->label = $object->label;
        }
        
        if (isset($object->default)) {
            $this->default = $object->default;
        }
        
        if (isset($object->contentGroup) || isset($object->contentAlias)) {
            
            /**
             * @var ContentIdentificationFactoryInterface $factory
             */
            $factory = SingletonPrototype::instance()->get(ContentIdentificationFactoryInterface::class);
            
            $identification = new \stdClass();
            
            if (isset($object->contentAlias)) {
                $identification->contentAlias = $object->contentAlias;
            }
            if (isset($object->contentGroup)) {
                $identification->contentGroup = $object->contentGroup;
            }
            $this->contentIdentification = $factory->createFromJson($identification);
        }
        
        if (isset($object->translatable)) {
            $this->translatable = $object->translatable === true;
        }
        
        if (isset($object->useEditorContent)) {
            $this->useEditorContent = (bool)$object->useEditorContent;
        }
        
        if (isset($object->value) && $this->isValid($object->value)) {
            $this->value = $this->parseValue($object->value);
        }
        
        if ($this->contentIdentification && !isset($object->value) && isset($object->id)) {
            
            /** @var ContentReadServiceInterface $reader */
            $reader                    = SingletonPrototype::instance()->get(ContentReadServiceInterface::class);
            $contentManagerEntry       = null;
            $contentManagerEntryExists = true;
            
            try {
                $contentManagerEntry = $reader->findById($this->contentIdentification);
            } catch (ContentNotFoundException $contentNotFoundException) {
                $contentManagerEntryExists = false;
            }
            
            if ($contentManagerEntryExists) {
                $this->value = [];
                /** @var \InfoElementContent $value */
                foreach ($contentManagerEntry->texts() as $text) {
                    
                    /** @var ContentText $text */
                    $this->value[$text->languageCode()] = $text->content();
                }
            } else {
                
                $this->value = $this->default;
            }
        }
        
        if (isset($object->id)) {
            $this->id = $object->id;
        }
        
        if (isset($object->selector)) {
            $this->selector = $object->selector;
        }
        
        if (isset($object->for)) {
            $this->for = $object->for;
        }
        
        if (isset($object->repeaterField)) {
            $this->repeaterField = $object->repeaterField;
        }
    }
    
    
    /**
     * @param $value
     *
     * @return boolean
     */
    protected function isValid($value): bool
    {
        return $this->translatable() ? is_object($value) && !isset($value->id) : is_object($value);
    }
    
    
    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result                   = parent::jsonSerialize();
        if ($this->contentIdentification()) {
            $result->contentGroup = $this->contentIdentification()->contentGroup();
            $result->contentAlias = $this->contentIdentification()->contentAlias();
        } else {
            $result->contentGroup = null;
        }
        $result->useEditorContent = $this->useEditorContent;
        
        return $result;
    }
    
    
    /**
     * @return ContentIdentificationInterface
     */
    public function contentIdentification(): ?ContentIdentificationInterface
    {
        return $this->contentIdentification;
    }
    
    
    /**
     * @param ContentIdentificationInterface $contentIdentification
     */
    public function setContentIdentification(ContentIdentificationInterface $contentIdentification): void
    {
        $this->contentIdentification = $contentIdentification;
    }
    
    
    /**
     * @return bool
     */
    public function useEditorContent(): bool
    {
        return $this->useEditorContent;
    }
    
    
    /**
     * @param string                            $language
     * @param AbstractContentManagerOptionValue $value
     */
    public function addValue(string $language, AbstractContentManagerOptionValue $value): void
    {
        $this->value[$language] = $value;
    }
}