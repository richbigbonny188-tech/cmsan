<?php
/*--------------------------------------------------------------------------------------------------
    TabsWidget.php 2023-09-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\StyleEdit\Core\Components\Repeater\Entities\RepeaterOption;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 *
 */
class TabsWidget extends AbstractWidget
{
    private const FIELD_TITLE = 'title';
    
    private const FIELD_CONTENT = 'content';
    
    /**
     * @var string
     */
    protected string $class;
    
    
    /**
     * @var RepeaterOption
     */
    protected RepeaterOption $repeater;
    
    
    /**
     * @inheritDoc
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $tabTitles   = "";
        $tabContents = "";
        
        try {
            $defaultActiveIndex = $this->repeater->attributes()->getValue('defaultActiveIndex')->value() ?? 0;
        } catch (InvalidArgumentException $exception) {
            $defaultActiveIndex = 0;
        }
        
        foreach ($this->repeater->value() as $index => $tabOptions) {
            $parsedIndex = ($index + 1);
            
            $activeClass = $defaultActiveIndex === $index ? 'active' : '';
            $titleOption = $tabOptions->getOptionByRepeaterField(self::FIELD_TITLE);
            $titleOptionId = "{$this->id->value()}-title-{$parsedIndex}";
            
            $tabTitle    = $titleOption ? $titleOption->value($currentLanguage) : '';
            
            $tabContent  = $tabOptions->getValueByField(self::FIELD_CONTENT, $currentLanguage);
            
            $tabTitles   .= <<<HTML
                <li role="presentation" class="{$activeClass}">
                    <a href="#{$titleOptionId}" aria-controls="{$titleOptionId}" role="tab" data-toggle="tab">{$tabTitle}</a>
                </li>
            HTML;
            $tabContents .= <<<HTML
                <div role="tabpanel" class="tab-pane {$activeClass}" id="{$titleOptionId}">
                    <h3 class="visible-xs">{$tabTitle}</h3>
                    {$tabContent}
                </div>
            HTML;
        }
        
        return <<<HTML
        <div id="{$this->id->value()}" class="tabs-widget {$this->class}">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                {$tabTitles}
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                {$tabContents}
            </div>
        </div>
        HTML;
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $result            = $this->jsonObject;
        $result->id        = $this->static_id;
        $result->class     = $this->class;
        $result->type      = 'tabs';
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
}