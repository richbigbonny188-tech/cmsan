<?php
/* --------------------------------------------------------------
  HeadlineWidget.php 2022-08-05
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\StyleEdit\Core\Components\DropdownSelect\Entities\DropdownSelectOption;
use Gambio\StyleEdit\Core\Components\FontGroup\Entities\FontGroupOption;
use Gambio\StyleEdit\Core\Components\Style\CssGenerator;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 * Class HeadlineWidget
 */
class HeadlineWidget extends AbstractWidget
{
    /**
     * @var DropdownSelectOption
     */
    protected $headingType;
    
    /**
     * @var TextBox
     */
    protected $text;
    
    /**
     * @var FontGroupOption
     */
    protected $font;
    
    
    /**
     * produces the widget HTML output
     *
     * @param Language|null     $currentLanguage
     *
     * @param CssGenerator|null $cssGenerator
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage, ?CssGenerator $cssGenerator = null) : string
    {
        $html = $this->htmlTemplate();
        $html = str_replace(
            ['{headingType}', '{text}', '{id}'],
            [
                $this->headingType->value($currentLanguage),
                $this->text->value($currentLanguage),
                $this->id->value($currentLanguage)
            ],
            $html
        );
        
        if ($this->font !== null) {
            
            if ($this->font->enableCustomization()->value($currentLanguage) === true) {
                $cssGenerator = $cssGenerator ?? CssGenerator::create($this->id->value($currentLanguage));
                $cssGenerator->setFont($this->font);
                $style = (string)$cssGenerator;
            } else {
                
                $style = '';
            }
            
            $html = $style . PHP_EOL . $html;
        }
        
        return $html;
    }
    
    
    /**
     * @return string
     */
    protected function htmlTemplate() : string
    {
        return '<{headingType} id="{id}">{text}</{headingType}>';
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
        $result            = $this->jsonObject;
        $result->type      = 'headline';
        $result->id        = $this->static_id;
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
    
}