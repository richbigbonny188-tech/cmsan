<?php
/* --------------------------------------------------------------
  SeparatorWidget.php 2019-10-24
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

use Gambio\StyleEdit\Core\Components\BorderGroup\Entities\BorderGroupOption;
use Gambio\StyleEdit\Core\Components\MarginGroup\Entities\MarginGroupOption;
use Gambio\StyleEdit\Core\Components\PaddingGroup\Entities\PaddingGroupOption;
use Gambio\StyleEdit\Core\Components\Style\CssGenerator;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Options\Entities\FieldSet;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;
use Gambio\StyleEdit\Core\Widgets\Abstractions\Interfaces\ContentGeneratorInterface;

/**
 * Class SeparatorWidget
 */
class SeparatorWidget extends AbstractWidget
{
    /**
     * @var MarginGroupOption
     */
    protected $margin;
    
    /**
     * @var BorderGroupOption
     */
    protected $border;
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage) : string
    {
        $style = (string)CssGenerator::create($this->id)->setMargin($this->margin)->setBorder($this->border);
        
        return $style . "<div class='separator-widget' id=\"{$this->id}\"></div>";
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
        $result->type      = 'separator';
        $result->id        = $this->static_id;
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
}