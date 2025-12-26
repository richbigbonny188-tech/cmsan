<?php
/* --------------------------------------------------------------
  CssGenerator.php 2019-10-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

namespace Gambio\StyleEdit\Core\Components\Style;

use Gambio\StyleEdit\Configurations\ShopBaseUrl;
use Gambio\StyleEdit\Core\Components\BackgroundGroup\Entities\BackgroundGroupOption;
use Gambio\StyleEdit\Core\Components\BorderGroup\Entities\BorderGroupOption;
use Gambio\StyleEdit\Core\Components\FontGroup\Entities\FontGroupOption;
use Gambio\StyleEdit\Core\Components\MarginGroup\Entities\MarginGroupOption;
use Gambio\StyleEdit\Core\Components\PaddingGroup\Entities\PaddingGroupOption;
use Gambio\StyleEdit\Core\Components\Style\Exception\UnfinishedBuildException;
use Gambio\StyleEdit\Core\Components\Style\Traits\CssStyleTrait;
use Gambio\StyleEdit\Core\SingletonPrototype;

/**
 * Class CssGenerator
 */
class CssGenerator
{
    protected const ABSOLUTE_URL_PATTERN = '#^https?://#';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var BackgroundGroupOption
     */
    protected $background;

    /**
     * @var MarginGroupOption
     */
    protected $margin;

    /**
     * @var PaddingGroupOption
     */
    protected $padding;

    /**
     * @var BorderGroupOption
     */
    protected $border;

    /**
     * @var FontGroupOption
     */
    protected $font;


    /**
     * CssGenerator constructor.
     *
     * @param string $id
     */
    protected function __construct(string $id)
    {
        $this->id = $id;

    }


    public static function create(string $id): CssGenerator
    {
        return new static($id);
    }


    /**
     * @return string
     */
    public function __toString()
    {
        $styling = '';

        if (isset($this->background)) {

            $styling .= $this->getBackgroundStyle($this->background);
        }

        if (isset($this->margin)) {

            $styling .= $this->getMarginStyle($this->margin);
        }

        if (isset($this->padding)) {

            $styling .= $this->getPaddingStyle($this->padding);
        }

        if (isset($this->border)) {

            $styling .= $this->getBorderStyle($this->border);
        }

        if (isset($this->font)) {

            $styling .= $this->getFontStyle($this->font);
        }

        if ($styling === '') {

            return '';
        }

        $style = '<style>' . PHP_EOL . "\t#{$this->id} {";

        foreach (explode(PHP_EOL, $styling) as $css) {

            if ($css === '') {

                continue;
            }

            $style .= PHP_EOL . "\t\t$css";
        }

        return $style . PHP_EOL . "\t}" . PHP_EOL . '</style>';
    }


    /**
     * @param BackgroundGroupOption $backgroundOption
     *
     * @return string
     */
    protected function getBackgroundStyle(BackgroundGroupOption $backgroundOption): string
    {
        $background = "";
    
        if (!$backgroundOption->image()->enabled()->value() &&
            !$backgroundOption->gradient()->enabled()->value()
        ) {
            $background = $backgroundOption->color()->value();
        }
    
        if ($backgroundOption->image()->enabled()->value()) {
        
            $searchValue = SingletonPrototype::instance()->get(ShopBaseUrl::class)->value();
            $baseUrlToken = '__SHOP_BASE_URL__';
        
            $val = $backgroundOption->image()->url()->value();
            if (preg_match(self::ABSOLUTE_URL_PATTERN, $val) === 1) {
                $val = str_replace($searchValue, '', $val);
            }
            if (strpos($val, $baseUrlToken) === 0) {
                $val = str_replace($baseUrlToken, '', $val);
            }
        
            $bgPosition   = $backgroundOption->image()->position()->value();
            $bgRepeat     = $backgroundOption->image()->repeat()->value();
            $bgSize       = $backgroundOption->image()->size()->value();
            $bgAttachment = $backgroundOption->image()->attachment()->value();
    
            if (!$backgroundOption->gradient()->enabled()->value()) {
                $background .= "{$backgroundOption->color()->value()} ";
            }
    
            $background .= "url('{$val}') {$bgPosition} / {$bgSize} {$bgRepeat} {$bgAttachment}";
        }

        if ($backgroundOption->gradient()->enabled()->value()) {
    
            if ($backgroundOption->image()->enabled()->value()) {
                $background .= ', ';
            }
            
            $gradientColor1 = $backgroundOption->gradient()->color1()->value();
            $gradientColor2 = $backgroundOption->gradient()->color2()->value();
            $gradientType   = $backgroundOption->gradient()->gradientType()->value();
            $gradientAngle  = $backgroundOption->gradient()->angle()->value();
            
            if ($gradientType === 'linear') {
                $background .= "linear-gradient({$gradientAngle}, {$gradientColor1}, {$gradientColor2})";
            } else {
                $background .= "radial-gradient({$gradientColor1}, {$gradientColor2})";
            }
    
        }
        
        return "background: {$background};" . PHP_EOL;
    }


    /**
     * @param MarginGroupOption $margin
     *
     * @return string
     */
    protected function getMarginStyle(MarginGroupOption $margin): string
    {
        return 'margin: ' . $margin->marginTop()->value() . ' ' . $margin->marginRight()->value() . ' '
            . $margin->marginBottom()->value() . ' ' . $margin->marginLeft()->value() . ';' . PHP_EOL;
    }


    /**
     * @param PaddingGroupOption $padding
     *
     * @return string
     */
    protected function getPaddingStyle(PaddingGroupOption $padding): string
    {
        return 'padding: ' . $padding->paddingTop()->value() . ' ' . $padding->paddingRight()->value() . ' '
            . $padding->paddingBottom()->value() . ' ' . $padding->paddingLeft()->value() . ';' . PHP_EOL;
    }


    /**
     * @param BorderGroupOption $border
     *
     * @return string
     */
    protected function getBorderStyle(BorderGroupOption $border): string
    {
        $style = '';

        $style .= 'border-top-width: ' . $border->top()->value() . ';' . PHP_EOL;
        $style .= 'border-right-width: ' . $border->right()->value() . ';' . PHP_EOL;
        $style .= 'border-bottom-width: ' . $border->bottom()->value() . ';' . PHP_EOL;
        $style .= 'border-left-width: ' . $border->left()->value() . ';' . PHP_EOL;
        $style .= 'border-style: ' . $border->style()->value() . ';' . PHP_EOL;
        $style .= 'border-color: ' . $border->color()->value() . ';';

        return $style;
    }


    /**
     * @param FontGroupOption $font
     *
     * @return string
     */
    protected function getFontStyle(FontGroupOption $font): string
    {
        $style = '';
        $style .= 'font-family: ' . $font->family()->value() . ';' . PHP_EOL;
        $style .= 'font-size: ' . $font->size()->value() . ';' . PHP_EOL;
        $style .= 'font-style: ' . $font->style()->value() . ';' . PHP_EOL;
        $style .= 'text-align: ' . $font->textAlign()->value() . ';' . PHP_EOL;
        $style .= 'color: ' . $font->color()->value() . ';' . PHP_EOL;

        if ($font->textDecorationUnderline()->value()) {

            $style .= PHP_EOL . 'text-decoration: underline;';
        }

        if ($font->textTransformUppercase()->value()) {

            $style .= PHP_EOL . 'text-transform: uppercase;';
        }

        return $style;
    }


    /**
     * @param BackgroundGroupOption $background
     *
     * @return CssGenerator
     */
    public function setBackground(BackgroundGroupOption $background): CssGenerator
    {
        $this->background = $background;

        return $this;
    }


    /**
     * @param MarginGroupOption $margin
     *
     * @return CssGenerator
     */
    public function setMargin(MarginGroupOption $margin): CssGenerator
    {
        $this->margin = $margin;

        return $this;
    }


    /**
     * @param PaddingGroupOption $padding
     *
     * @return CssGenerator
     */
    public function setPadding(PaddingGroupOption $padding): CssGenerator
    {
        $this->padding = $padding;

        return $this;
    }


    /**
     * @param BorderGroupOption $border
     *
     * @return CssGenerator
     */
    public function setBorder(BorderGroupOption $border): CssGenerator
    {
        $this->border = $border;

        return $this;
    }


    /**
     * @param FontGroupOption $font
     *
     * @return CssGenerator
     */
    public function setFont(FontGroupOption $font): CssGenerator
    {
        $this->font = $font;

        return $this;
    }
}