<?php
/*--------------------------------------------------------------------------------------------------
    FancyLinkWidget.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\StyleEdit\Core\Components\DropdownSelect\Entities\DropdownSelectOption;
use Gambio\StyleEdit\Core\Components\RadioImage\Entities\RadioImageOption;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Components\Url\Entities\UrlOption;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 * Class ButtonWidget
 */
class ButtonWidget extends AbstractWidget
{
    /**
     * @var string
     */
    protected const TEXT_FALLBACK = 'Button';
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $text;
    /**
     * @var RadioImageOption
     */
    protected $size;
    /**
     * @var RadioImageOption
     */
    protected $buttonType;
    /**
     * @var UrlOption
     */
    protected $link;
    /**
     * @var TextBox
     */
    protected $class;
    /**
     * @var DropdownSelectOption
     */
    protected $target;
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $href = $this->link !== null ? $this->link->value($currentLanguage) : '';
        
        $class = implode(' ',
                         array_filter([
                                          'btn',
                                          $this->class->value($currentLanguage),
                                          $this->size->value($currentLanguage),
                                          $this->buttonType->value($currentLanguage)
        
                                      ]));
        
        return ' <a href="' . $href . '" id="' . $this->id->value($currentLanguage) . '" target="'
               . $this->target->value($currentLanguage) . '" class="' . $class . '">'
               . $this->text->value($currentLanguage) . '</a>';
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
        $result            = $this->jsonObject;
        $result->id        = $this->static_id;
        $result->type      = 'button';
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
}