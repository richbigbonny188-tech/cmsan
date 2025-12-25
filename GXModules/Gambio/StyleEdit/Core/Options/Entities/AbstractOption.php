<?php
/*--------------------------------------------------------------------------------------------------
    AbstractOption.php 2023-05-30
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Options\Entities;

use Exception;
use Gambio\StyleEdit\Core\Components\Option\Entities\OptionCollection;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Options\Interfaces\ComponentGroupOptionInterface;
use Gambio\StyleEdit\Core\Repositories\Entities\Configuration;
use Gambio\StyleEdit\Core\Repositories\SettingsRepository;
use Gambio\StyleEdit\Core\SingletonPrototype;
use JsonSerializable;
use ReflectionException;

/**
 * Class Variant
 *
 * @package Gambio\StyleEdit\Core\Components
 */
abstract class AbstractOption implements JsonSerializable, OptionInterface
{
    /**
     * @var SettingsRepository|null
     */
    protected $configurationRepository;
    
    /**
     * @var mixed
     */
    protected $default;
    
    /**
     * @var string|null
     */
    protected ?string $for;
    
    /**
     * @var string|null
     */
    protected ?string $group;
    
    /**
     * @var bool
     */
    protected bool $hidden = false;
    
    /**
     * @var string|null
     */
    protected $id;
    
    /**
     * @var string|null
     */
    protected ?string $labelId;
    
    /**
     * @var bool
     */
    protected bool $requiresReload;
    
    /**
     * @var string|null
     */
    protected ?string $selector;
    
    /**
     * @var bool|null
     */
    protected ?bool $translatable = false;
    
    /**
     * @var string|null
     */
    protected ?string $type;
    
    /**
     * @var mixed
     */
    protected $value;
    
    /**
     * @var mixed|null
     */
    protected $visible;
    
    /**
     * @var string|null
     */
    protected ?string $pageNamespace;
    
    /**
     * @var string|null
     */
    protected ?string $repeaterField;
    
    
    /**
     * AbstractOption constructor.
     *
     * @param string|null $id
     * @param string|null $type
     * @param string|null $labelId
     * @param string|null $group
     * @param string|null $selector
     * @param string|null $for
     * @param bool|null   $translatable
     * @param bool|null   $requiresReload
     * @param null        $default
     * @param null        $value
     * @param null        $visible
     * @param string|null $pageNamespace
     * @param string|null $repeaterField
     */
    public function __construct(
        $id = null,
        string $type = null,
        string $labelId = null,
        string $group = null,
        string $selector = null,
        string $for = null,
        bool   $translatable = null,
        bool   $requiresReload = null,
               $default = null,
               $value = null,
               $visible = null,
        string $pageNamespace = null,
        string $repeaterField = null
    ) {
        $this->id             = $id;
        $this->default        = $default;
        $this->value          = $value;
        $this->selector       = $selector;
        $this->for            = $for;
        $this->translatable   = $translatable ?? false;
        $this->type           = $type;
        $this->group          = $group;
        $this->requiresReload = $requiresReload ?? false;
        $this->labelId        = $labelId;
        $this->visible        = $visible;
        $this->pageNamespace  = $pageNamespace;
        $this->repeaterField  = $repeaterField;
    }
    
    
    /**
     * @param                              $jsonObject
     * @param string                       $prefix
     * @param SettingsRepository|null      $configurationRepository
     *
     * @return bool|OptionCollection|mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public static function createFromJsonObject(
        $jsonObject,
        string $prefix = '',
        SettingsRepository $configurationRepository = null
    ) {
        $optionTypeName = str_replace('-', '', ucwords($jsonObject->type, '-')) . 'Option';
        
        //use the prototype as classloader to create specialized instances of subtypes
        $instance = SingletonPrototype::instance()->get($optionTypeName);
        
        $prefix = $prefix ? $prefix . '-' : '';
        
        if ($instance instanceof self) {
            $jsonObject->id                    = $prefix . $jsonObject->id;
            $instance->configurationRepository = $configurationRepository;
            $instance->initializeFromJsonObject($jsonObject);
            
            return $instance;
        }
        
        if ($instance instanceof OptionCollection) {
            $result = new OptionCollection();
            $prefix = strtolower($prefix . $jsonObject->id);
            foreach ($instance as &$option) {
                $result->addItem(self::createFromJsonObject($option->jsonSerialize(),
                                                            $prefix,
                                                            $configurationRepository));
            }
            
            return $result;
        }
        
        throw new Exception('Invalid Option object! Type:[' . $jsonObject->type . ']');
    }
    
    
    /**
     * @param $object
     *
     * @throws Exception
     */
    public function initializeFromJsonObject($object): void
    {
        if (property_exists($object, 'group') && $object->group) {
            $this->group = $object->group;
        }
        
        if (!isset($object->value) && isset($object->id)) {
            
            if ($this->configurationRepository()) {
                $configuration = $this->configurationRepository()->getJsonConfigurationFrom($object->id);
                
                if (!$configuration) {
                    
                    if (!($this instanceof ComponentGroupOptionInterface)) {
                        
                        $configuration = Configuration::createFromJson((object)[
                            'name'   => $object->id,
                            'value'  => $object->default ?? null,
                            'group'  => $this->group() ?? 'template',
                            'type'   => $this->type(),
                            'hidden' => $this->hidden(),
                        ]);
                        
                        $this->configurationRepository()->saveJsonConfigurationFrom($configuration);
                        $object->value = $configuration->value();
                    } else {
                        $object->value = property_exists($object, 'default') ? $object->default : null;
                    }
                } else {
                    $object->value = $configuration->value();
                    if (!$configuration->type()) {
                        $cfg       = $configuration->jsonSerialize();
                        $cfg->type = $this->type();
                        $this->configurationRepository()
                            ->saveJsonConfigurationFrom(Configuration::createFromJson($cfg));
                    }
                }
            } elseif (property_exists($object, 'default')) {
                $object->value = $object->default;
            } else {
                $object->value = null;
            }
        }
        
        $this->requiresReload = isset($object->requiresReload) && $object->requiresReload === true;
        
        if (isset($object->id)) {
            $this->id = $object->id;
        }
        
        if (isset($object->type)) {
            $this->type = $object->type;
        }
        
        if (isset($object->default)) {
            $this->default = $object->default;
        }
        
        if (isset($object->selector)) {
            $this->selector = $object->selector;
        }
        
        if (isset($object->for)) {
            $this->for = $object->for;
        }
        
        if (isset($object->pageNamespace)) {
            $this->pageNamespace = $object->pageNamespace;
        }
        
        if (isset($object->visible, $object->visible->id, $object->visible->value)) {
            $this->visible = $object->visible;
        }
        
        if (isset($object->translatable)) {
            $this->translatable = $object->translatable === true;
        }
        
        if (isset($object->value) && $this->isValid($object->value)) {
            $this->value = $this->parseValue($object->value);
        }
        
        if (isset($object->labelId)) {
            $this->labelId = $object->labelId;
        }
        
        if (isset($object->hidden)) {
            $this->hidden = $object->hidden;
        }
        
        if (isset($object->repeaterField)) {
            $this->repeaterField = $object->repeaterField;
        }
    }
    
    
    /**
     * @return string|null
     */
    public function pageNamespace(): ?string
    {
        return $this->pageNamespace;
    }
    
    
    /**
     * @return SettingsRepository
     * @throws Exception
     */
    protected function configurationRepository(): ?SettingsRepository
    {
        return $this->configurationRepository;
    }
    
    
    /**
     * @param SettingsRepository $configurationRepository
     */
    public function setConfigurationRepository(SettingsRepository $configurationRepository): void
    {
        $this->configurationRepository = $configurationRepository;
    }
    
    
    /**
     * @return string|null
     */
    public function group(): ?string
    {
        return $this->group;
    }
    
    
    /**
     * @return bool|null
     */
    public function hidden(): bool
    {
        return $this->hidden;
    }
    
    
    /**
     * @return mixed
     */
    public function visible()
    {
        return $this->visible;
    }
    
    
    /**
     * @param $value
     *
     * @return boolean
     */
    abstract protected function isValid($value): bool;
    
    
    /**
     * @param $value
     *
     * @return mixed
     */
    abstract protected function parseValue($value);
    
    
    /**
     * @param SettingsRepository|null $configurationRepository
     *
     * @return $this
     */
    public function withConfigurationRepository(?SettingsRepository $configurationRepository): self
    {
        $this->configurationRepository = $configurationRepository;
        
        return $this;
    }
    
    
    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $result = [];
        if ($this->id()) {
            $result['id'] = $this->id();
        }
        
        if ($this->visible()) {
            $result['visible'] = $this->visible();
        }
        
        // We could have "boolean" values so that we need to check if is
        // not null instead of check only if is true/false
        if ($this->defaultValue() !== null) {
            // Changed from $this->defaultValue() to $this->default
            // because the defaultValue method was casting the property to string and
            // if we have a boolean property it will be casted to "1" or "0"
            $result['default'] = $this->default;
        }
        
        $result['type'] = $this->type();
        if ($this->translatable()) {
            $result['translatable'] = $this->translatable();
        }
        
        // We could have "boolean" values so that we need to check if is
        // not null instead of check only if is true/false
        if ($this->value() !== null) {
            $result['value'] = $this->value();
        }
        
        if ($this->pageNamespace()) {
            $result['pageNamespace'] = $this->pageNamespace();
        }
        
        if ($this->selector()) {
            $result['selector'] = $this->selector();
        }
        
        if ($this->group()) {
            $result['group'] = $this->group();
        }
        
        if ($this->requiresReload()) {
            $result['requiresReload'] = true;
        }
        
        if ($this->labelId()) {
            $result['labelId'] = $this->labelId();
        }
        
        if ($this->hidden()) {
            $result['hidden'] = $this->hidden();
        }
        
        if ($this->repeaterField()) {
            $result['repeaterField'] = $this->repeaterField();
        }
        
        return (object)$result;
    }
    
    
    /**
     * @return string|null
     */
    public function id(): ?string
    {
        return $this->id;
    }
    
    
    /**
     * @param string $id
     */
    protected function setId(string $id): void
    {
        $this->id = $id;
    }
    
    
    /**
     * @return mixed
     */
    public function defaultValue()
    {
        return $this->default;
    }
    
    
    /**
     * @return bool|null
     */
    public function translatable(): bool
    {
        return $this->translatable;
    }
    
    
    /**
     * @param Language|null $language
     *
     * @return mixed
     */
    public function value(?Language $language = null)
    {
        $value = $this->value ?? $this->defaultValue();
        
        if (is_null($language)) {
            return $value;
        }
        
        if (is_object($value)) {
            if (!property_exists($value, $language->code())) {
                return null;
            }
            
            return $value->{$language->code()};
        }
        
        return $value;
    }
    
    
    /**
     * @return string|null
     */
    public function selector(): ?string
    {
        return $this->selector;
    }
    
    
    /**
     * @return bool
     */
    public function requiresReload(): bool
    {
        return $this->requiresReload ?? false;
    }
    
    
    /**
     * @return string|null
     */
    public function labelId(): ?string
    {
        return $this->labelId;
    }
    
    
    /**
     * @return string|null
     */
    public function for(): ?string
    {
        return $this->for;
    }
    
    
    /**
     * @return string|null
     */
    public function repeaterField(): ?string
    {
        return $this->repeaterField;
    }
}
