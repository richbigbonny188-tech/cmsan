<?php
/*--------------------------------------------------------------------------------------------------
    NewsletterWidget.php 2023-09-20
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2023 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\StyleEdit\Core\Components\RadioImage\Entities\RadioImageOption;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 *
 */
class NewsletterWidget extends AbstractWidget
{
    /**
     *
     */
    private const TYPE = 'newsletter';
    
    private const ALLOWED_FORM_LAYOUTS = [self::HORIZONTAL_FORM, self::VERTICAL_FORM];
    
    private const HORIZONTAL_FORM = 'horizontal-form';
    
    private const VERTICAL_FORM = 'vertical-form';
    
    /**
     *
     */
    private const FORM_ACTION = 'newsletter.php';
    
    /**
     * @var TextBox
     */
    protected $class;
    
    /**
     * @var TextBox
     */
    protected $inputLabel;
    
    
    /**
     * @var TextBox
     */
    protected $inputPlaceholder;
    
    
    /**
     * @var TextBox
     */
    protected $inputHelpText;
    
    
    /**
     * @var TextBox
     */
    protected $buttonLabel;
    
    
    /**
     * @var RadioImageOption
     */
    protected $formLayout;
    
    
    /**
     * @var RadioImageOption
     */
    protected $size;
    
    
    /**
     * @var RadioImageOption
     */
    protected $buttonType;
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $formLayout       = $this->formLayout->value();
        $formAction       = self::FORM_ACTION;
        $widgetClass      = implode(' ', array_filter([$formLayout, $this->class->value()]));
        $inputPlaceholder = $this->inputPlaceholder->value($currentLanguage);
        $inputLabel       = $this->inputLabel->value($currentLanguage);
        $inputHelpText    = $this->inputHelpText->value($currentLanguage);
        $buttonLabel      = $this->buttonLabel->value($currentLanguage);
        
        $buttonSize        = $this->size->value($currentLanguage) ? 'btn-' . $this->size->value($currentLanguage) : '';
        $inputSize         = $this->size->value($currentLanguage) ? 'input-'
                                                                    . $this->size->value($currentLanguage) : '';
        $buttonClass       = implode(' ',
                                     array_filter([
                                                      'btn',
                                                      'btn-block',
                                                      $buttonSize,
                                                      $this->buttonType->value($currentLanguage)
                                                  ]));
        $inputClass        = implode(' ', array_filter(['form-control', 'validate', $inputSize]));
        $inputLabelHTML    = $inputLabel ? "<div class='row'><div class='col-xs-12'><label for=\"email-input-$this->id\">$inputLabel</label></div></div>" : '';
        $inputHelpTextHTML = $inputHelpText ? "<p class=\"help-block\">$inputHelpText</p>" : '';
        $formColumnsLayout = $this->getCssClassesForFormLayout($formLayout);
        
        return <<<HTML
            <div class='newsletter-widget $widgetClass' id="$this->id">
                <form action="$formAction" method="post">
                    $inputLabelHTML
                    <div class="row">
                        <div class="{$formColumnsLayout['inputColumn']}">
                            <div class="form-group">
                                <input
                                    id="email-input-$this->id"
                                    class="$inputClass"
                                    data-validator-validate="required email"
                                    name="email"
                                    placeholder="$inputPlaceholder"
                                    type="text">
                                $inputHelpTextHTML
                            </div>
                        </div>
                        <div class="{$formColumnsLayout['buttonColumn']}">
                            <input class="$buttonClass" type="submit" value="$buttonLabel">
                        </div>
                    </div>
                </form>
            </div>
        HTML;
    }
    
    
    /**
     * @return mixed|stdClass
     */
    public function jsonSerialize()
    {
        $result            = $this->jsonObject;
        $result->type      = self::TYPE;
        $result->id        = $this->static_id;
        $result->fieldsets = $this->fieldsets;
        
        return $result;
    }
    
    
    /**
     * Returns an array with the input and button column classes:
     * [
     *     'inputColumn'  => 'col-xs-12 col-md-6',
     *     'buttonColumn' => 'col-xs-12 col-md-6'
     * ]
     *
     * @param string $formLayout
     *
     * @return string[]
     */
    private function getCssClassesForFormLayout(string $formLayout): array
    {
        if ($formLayout === self::HORIZONTAL_FORM || !in_array($formLayout, self::ALLOWED_FORM_LAYOUTS)) {
            return [
                'inputColumn'  => 'col-xs-12 col-sm-6 col-md-7',
                'buttonColumn' => 'col-xs-12 col-sm-6 col-md-5',
            ];
        }
        
        // VERTICAL_FORM
        return [
            'inputColumn'  => 'col-xs-12',
            'buttonColumn' => 'col-xs-12',
        ];
    }
}
