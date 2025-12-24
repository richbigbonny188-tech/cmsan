<?php
/* --------------------------------------------------------------
  WidgetConfiguration.php 2023-11-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\Widget\Entities;

use Exception;
use Gambio\StyleEdit\Configurations\ShopBasePath;
use Gambio\StyleEdit\Core\BuildStrategies\Interfaces\AlwaysNewStrategyInterface;
use Gambio\StyleEdit\Core\Json\FileIO;
use Gambio\StyleEdit\Core\Options\Entities\FieldSet;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use \stdClass;

/**
 * Class WidgetConfiguration
 */
class WidgetConfiguration implements AlwaysNewStrategyInterface
{
    /**
     * @var string
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $title;
    
    /**
     * @var stdClass
     */
    protected $icon;
    
    /**
     * @var stdClass
     */
    protected $author;
    
    /**
     * @var string
     */
    protected $helpUrl;
    
    /**
     * @var string
     */
    protected $description;
    
    /**
     * @var int
     */
    protected $cache;
    
    /**
     * @var string
     */
    protected $displayConfig;
    
    /**
     * @var array
     */
    protected $fieldsets;
    
    /**
     * @var string
     */
    protected $version;
    
    /**
     * @var string
     */
    protected $widgetPath;
    
    /**
     * @var FileIO
     */
    protected $fileIO;
    
    
    /**
     * WidgetConfiguration constructor.
     *
     * @param stdClass $widgetJson
     * @param string   $widgetPath
     *
     * @throws Exception
     */
    public function __construct(stdClass $widgetJson, string $widgetPath)
    {
        $this->id            = $widgetJson->id;
        $this->title         = $widgetJson->title;
        $this->icon          = $widgetJson->icon;
        $this->author        = $widgetJson->author;
        $this->helpUrl       = $widgetJson->helpUrl;
        $this->description   = $widgetJson->description;
        $this->cache         = $widgetJson->cache;
        $this->version       = $widgetJson->version;
        $this->displayConfig = $widgetJson->displayConfig;
        $this->fieldsets     = $widgetJson->fieldsets;
        $this->widgetPath    = str_replace(basename($widgetPath), '', $widgetPath);
        
        if (isset($this->icon->file)) {
            $shopRoot         = SingletonPrototype::instance()->get(ShopBasePath::class)->value();
            $this->icon->file = str_replace($shopRoot, '', $this->widgetPath) . $this->icon->file;
        }
        
        $this->languageSpecificContent();
    }
    
    
    /**
     * Translates language specific content
     *
     * @throws Exception
     */
    protected function languageSpecificContent(): void
    {
        /** @var LanguageService $languageService */
        $languageService = SingletonPrototype::instance()->get(LanguageService::class);
        
        $bilingualProperties = ['title', 'description'];
        
        foreach ($bilingualProperties as $property) {
            if (property_exists(static::class, $property)) {
                $this->$property = $languageService->translate($this->$property);
            }
        }
        
        foreach ($this->fieldsets as &$fieldset) {
            $fieldset->title = $languageService->translate($fieldset->title);
            
            foreach ($fieldset->options as &$option) {
                $option = $this->getOptionTranslations($option, $languageService);
            }
            
            unset($option);
            
            $fieldset = FieldSet::createFromJsonObject($fieldset);
        }
        unset($fieldset);
    }
    
    
    /**
     * @param string $jsonPath
     *
     * @return WidgetConfiguration
     * @throws Exception
     */
    public static function createFromJsonPath(string $jsonPath): self
    {
        if (!file_exists($jsonPath)) {
            throw new \InvalidArgumentException('widget.json does not exists');
        }
        
        /**
         * @var FileIO
         */
        $fileIO = SingletonPrototype::instance()->get(FileIO::class);
        $json   = $fileIO->read($jsonPath);
        
        return SingletonPrototype::instance()->get(WidgetConfiguration::class, $json, $jsonPath);
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
        $result = new stdClass;
        
        $result->id            = $this->id();
        $result->title         = $this->title();
        $result->icon          = $this->icon();
        $result->author        = $this->author();
        $result->helpUrl       = $this->helpUrl();
        $result->description   = $this->description();
        $result->cache         = $this->cache();
        $result->version       = $this->version();
        $result->displayConfig = $this->displayConfig();
        $result->fieldsets     = $this->fieldsets();
        
        return $result;
    }
    
    
    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
    
    
    /**
     * @return stdClass
     */
    public function icon(): stdClass
    {
        return $this->icon;
    }
    
    
    /**
     * @return stdClass
     */
    public function author(): stdClass
    {
        return $this->author;
    }
    
    
    /**
     * @return string
     */
    public function helpUrl(): string
    {
        return $this->helpUrl;
    }
    
    
    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }
    
    
    /**
     * @return int
     */
    public function cache(): int
    {
        return $this->cache;
    }
    
    
    /**
     * @return string
     */
    public function displayConfig(): string
    {
        return $this->displayConfig;
    }
    
    
    /**
     * @return FieldSet[]
     */
    public function fieldsets(): array
    {
        return $this->fieldsets;
    }
    
    
    /**
     * @return string
     */
    public function version(): string
    {
        return $this->version;
    }
    
    
    /**
     * @return string
     */
    public function widgetPath(): string
    {
        return $this->widgetPath;
    }
    
    
    /**
     * @param stdClass        $option
     * @param LanguageService $languageService
     *
     * @return stdClass
     * @throws Exception
     */
    protected function getOptionTranslations(stdClass $option, LanguageService $languageService): stdClass
    {
        if (isset($option->label)) {
            $option->label = $languageService->translate($option->label);
        }
        
        if (isset($option->attributes) && is_a($option->attributes, stdClass::class)) {
            $option->attributes = $this->getAttributesTranslations($option->attributes, $languageService);
        }
        
        if (isset($option->options) && is_array($option->options)) {
            foreach ($option->options as &$innerOption) {
                if (isset($innerOption->text)) {
                    $innerOption->text = $languageService->translate($innerOption->text);
                }
            }
            
            unset($innerOption);
        }
        
        if (isset($option->items)) {
            foreach ($option->items as &$item) {
                if (is_object($item)) {
                    $item = $this->getOptionTranslations($item, $languageService);
                }
            }
            
            unset($item);
        }
        
        // "fields" property comes from the `RepeaterOption`
        if (isset($option->fields) && is_array($option->fields)) {
            foreach ($option->fields as &$optionField) {
                $optionField = $this->getOptionTranslations($optionField, $languageService);
            }
            
            unset($optionField);
        }
        
        // "default" property as array comes from the `RepeaterOption`
        if (isset($option->default) && is_array($option->default)) {
            foreach ($option->default as &$optionDefault) {
                $optionDefault = $this->getDefaultRowTranslations($optionDefault, $languageService);
            }
            
            unset($optionDefault);
        }
        
        return $option;
    }
    
    
    /**
     * @param stdClass        $attributes
     * @param LanguageService $languageService
     *
     * @return stdClass
     * @throws Exception
     */
    protected function getAttributesTranslations(stdClass $attributes, LanguageService $languageService): stdClass
    {
        foreach ($attributes as $key => $value) {
            // Skips if:
            // - value is not a string
            // - value does not contain a "." -> myWidget.options.label
            // - value contains javascript code -> javascript: alert('hilfe!');
            if (!is_string($value)
                || !str_contains($value, '.')
                || preg_match('/^(javascript:)/', $value) === 1
            ) {
                continue;
            }
            
            $attributes->{$key} = $languageService->translate($value);
        }
        
        return $attributes;
    }
    
    
    /**
     * @param array           $defaultRows
     * @param LanguageService $languageService
     *
     * @return array
     * @throws Exception
     */
    protected function getDefaultRowTranslations(array $defaultRows, LanguageService $languageService): array
    {
        foreach ($defaultRows as &$default) {
            $default = $this->getOptionTranslations($default, $languageService);
        }
        
        return $defaultRows;
    }
}
