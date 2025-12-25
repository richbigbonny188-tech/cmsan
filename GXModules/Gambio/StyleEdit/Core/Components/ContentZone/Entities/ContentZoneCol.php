<?php
/* --------------------------------------------------------------
  ContentZoneCol.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\ContentZone\Entities;

use Exception;
use Gambio\StyleEdit\Core\Components\BackgroundGroup\Entities\BackgroundGroupOption;
use Gambio\StyleEdit\Core\Components\BorderGroup\Entities\BorderGroupOption;
use Gambio\StyleEdit\Core\Components\ContentZone\Entities\Traits\ContentZoneNormalizeTrait;
use Gambio\StyleEdit\Core\Components\ResponsiveGroup\Entities\ResponsiveGroupOption;
use Gambio\StyleEdit\Core\Components\Style\CssGenerator;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\ContentGeneratorInterface;
use Gambio\StyleEdit\Core\Components\ContentZone\Interfaces\UpdatableContentZoneContentInterface;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\PersistableContentInterface;
use InvalidArgumentException;
use JsonSerializable;
use KeyValueCollection;
use stdClass;

/**
 * Class ContentZoneCol
 */
class ContentZoneCol extends KeyValueCollection
    implements ContentGeneratorInterface, JsonSerializable, PersistableContentInterface, UpdatableContentZoneContentInterface
{
    use ContentZoneNormalizeTrait;
    
    /**
     * @var string
     */
    protected $cssId;
    
    /**
     * @var string
     */
    protected $cssClass;
    
    /**
     * @var ResponsiveGroupOption
     */
    protected $responsive;
    
    /**
     * @var BackgroundGroupOption
     */
    protected $background;
    
    /**
     * @var BorderGroupOption
     */
    protected $border;
    
    
    /**
     * @var ?stdClass
     */
    protected $jsonObject;
    
    /**
     * @var ?CssGenerator
     */
    protected $cssGenerator;
    
    
    /**
     * ContentZoneCol constructor.
     *
     * @param array                 $keyValueArray
     * @param ResponsiveGroupOption $responsive
     * @param BackgroundGroupOption $background
     * @param BorderGroupOption     $border
     * @param TextBox               $cssId
     * @param TextBox               $cssClass
     * @param stdClass|null         $jsonObject
     * @param CssGenerator|null     $cssGenerator
     */
    public function __construct(
        array $keyValueArray,
        ResponsiveGroupOption $responsive,
        BackgroundGroupOption $background,
        BorderGroupOption $border,
        TextBox $cssId,
        TextBox $cssClass,
        ?stdClass $jsonObject,
        ?CssGenerator $cssGenerator = null
    ) {
        if (count($keyValueArray)) {
            
            foreach ($keyValueArray as $widget) {
                
                if (!is_a($widget, AbstractWidget::class)) {
                    
                    throw new InvalidArgumentException(self::class . ' can only hold Widgets');
                }
            }
        }
        
        parent::__construct($keyValueArray);
        
        $this->responsive = $responsive;
        $this->background = $background;
        $this->border     = $border;
        $this->cssId      = $cssId;
        $this->cssClass   = $cssClass;
        
        $this->jsonObject   = $jsonObject;
        $this->cssGenerator = $cssGenerator ?? CssGenerator::create($this->cssId->value());
    }
    
    
    /**
     * @param stdClass $jsonObject
     *
     * @return ContentGeneratorInterface
     *
     * @throws Exception
     */
    public static function createFromJsonObject(stdClass $jsonObject): ContentGeneratorInterface
    {
        if (!isset($jsonObject->widgets, $jsonObject->responsive, $jsonObject->background, $jsonObject->border, $jsonObject->id)) {
            
            throw new InvalidArgumentException;
        }
        
        if (count($jsonObject->widgets)) {
            
            foreach ($jsonObject->widgets as $widgetIndex => &$element) {
                
                if ($element->id === null) {
                    
                    $element->id = $jsonObject->id . '-' . $element->type . '-' . ($widgetIndex + 1);
                }
                
                $widgetTypeName = str_replace('-', '', ucwords($element->type, '-')) . 'Widget';
                
                $element = $widgetTypeName::createFromJsonObject($element);
            }
        }
        
        unset($widget);
        
        $cssId      = TextBox::createFromJsonObject($jsonObject->id);
        $responsive = ResponsiveGroupOption::createFromJsonObject($jsonObject->responsive);
        
        if (!isset($jsonObject->class->labelId)) {
            $jsonObject->class->labelId = 'StyleEdit.contentZone.class.label';
        }
        $cssClass = TextBox::createFromJsonObject($jsonObject->class);
        
        if (!isset($jsonObject->background->labelId)) {
            $jsonObject->background->labelId = 'StyleEdit.contentZone.background.label';
        }
        $background = BackgroundGroupOption::createFromJsonObject($jsonObject->background);
        // Updating background object in order to get the updated image URL
        // if it was parsed, for example: __SHOP_BASE_URL__images/some_image.jpg
        $jsonObject->background = $background->jsonSerialize();
        
        if (!isset($jsonObject->border->labelId)) {
            $jsonObject->border->labelId = 'StyleEdit.contentZone.border.label';
        }
        $border = BorderGroupOption::createFromJsonObject($jsonObject->border);
        
        return new static($jsonObject->widgets, $responsive, $background, $border, $cssId, $cssClass, $jsonObject);
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
        $result = $this->jsonObject;
        
        $result->widgets = [];
        
        /** @var AbstractWidget $widget */
        foreach ($this->getArray() as $widget) {
            
            $result->widgets[] = $widget->jsonSerialize();
        }
        
        return $result;
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string html content of all widgets inside group
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $colId    = $this->cssId->value();
        $colClass = implode('',
                            array_filter([
                                             preg_match('/\s/', $this->cssClass->value()) ? ' '
                                                                                            . $this->cssClass->value() : '',
                                             $this->responsive->hiddenLg()->value() ? ' hidden-lg' : '',
                                             $this->responsive->hiddenMd()->value() ? ' hidden-md' : '',
                                             $this->responsive->hiddenSm()->value() ? ' hidden-sm' : '',
                                             $this->responsive->hiddenXs()->value() ? ' hidden-xs' : ''
                                         ]));
        
        $this->cssGenerator->setBorder($this->border);
        $this->cssGenerator->setBackground($this->background);
        $style = (string)$this->cssGenerator;
        
        $html = $style . PHP_EOL . '<div id="{$user_provided_id}" class="gx-content-zone-col{$user_provided_classes}">';
        $html = str_replace(['{$user_provided_id}', '{$user_provided_classes}'], [$colId, $colClass], $html);
        
        /** @var AbstractWidget $widget */
        foreach ($this->getArray() as $widget) {
            $html .= PHP_EOL . "\t" . '<div class="widget-content">';
            $html .= PHP_EOL . "\t\t" . $widget->htmlContent($currentLanguage);
            $html .= PHP_EOL . "\t" . '</div>';
        }
        
        return $html . PHP_EOL . '</div>';
    }
    
    
    /**
     * @return string
     */
    public function cssClass(): ?string
    {
        return $this->cssClass->value();
    }
    
    
    /**
     * @param string $cssClass
     *
     * @throws Exception
     */
    public function setCssClass(string $cssClass): void
    {
        $jsonObject        = $this->cssClass->jsonSerialize();
        $jsonObject->value = $cssClass;
        $this->cssClass    = TextBox::createFromJsonObject($jsonObject);
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function previewContent(?Language $currentLanguage): string
    {
        return $this->htmlContent($currentLanguage);
    }
    
    
    public function persist(): void
    {
        foreach ($this->getIterator() as $widget) {
            
            $widget->persist();
        }
    }
    
    
    public function update(): void
    {
        foreach ($this->getIterator() as $widget) {
            /** @var AbstractWidget $widget */
            $widget->update();
        }
    }
}
