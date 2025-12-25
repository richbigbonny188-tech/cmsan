<?php
/*--------------------------------------------------------------------------------------------------
    AccordionWidget.php 2023-07-20
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

class AccordionWidget extends AbstractWidget
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
        $panelsHTML = '';
        try {
            $defaultActiveIndex = $this->repeater->attributes()->getValue('defaultActiveIndex')->value() ?? 0;
        } catch (InvalidArgumentException $exception) {
            $defaultActiveIndex = 0;
        }
        
        foreach ($this->repeater->value() as $index => $panels) {
            $activeClass = $defaultActiveIndex === $index ? 'in' : '';
            $headingActiveClass = $defaultActiveIndex != $index ? 'collapsed' : '';
            
            $title   = $panels->getValueByField(self::FIELD_TITLE, $currentLanguage);
            $content = $panels->getValueByField(self::FIELD_CONTENT, $currentLanguage);
            
            if (is_array($content) && $currentLanguage) {
                $content = $content[$currentLanguage->code()];
                $content = $content->value() ?? '';
            }
            
            $parsedIndex = ($index + 1);
            $headingId   = "{$this->id->value()}-heading-{$parsedIndex}";
            $collapseId  = "{$this->id->value()}-collapse-{$parsedIndex}";
            
            $panelsHTML .= <<<HTML
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="{$headingId}">
                    <h4 class="panel-title">
                        <a role="button" data-toggle="collapse" data-parent="#{$this->id->value()}" href="#{$collapseId}" aria-expanded="true" aria-controls="{$collapseId}" class="{$headingActiveClass}">
                            {$title}
                        </a>
                    </h4>
                </div>
                <div id="{$collapseId}" class="panel-collapse collapse {$activeClass}" role="tabpanel" aria-labelledby="{$headingId}">
                    <div class="panel-body">
                        {$content}
                    </div>
                </div>
            </div>
HTML;
        }
        
        return <<<HTML
        <div class="panel-group accordion-widget {$this->class}" id="{$this->id->value()}" role="tablist" aria-multiselectable="true">
            {$panelsHTML}
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
        $result->type      = 'accordion';
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
}