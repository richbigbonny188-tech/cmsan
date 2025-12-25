<?php
/*--------------------------------------------------------------------------------------------------
    CarouselWidget.php 2024-03-04
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2024 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\StyleEdit\Configurations\ShopBasePath;
use Gambio\StyleEdit\Core\Components\Checkbox\Entities\CheckboxOption;
use Gambio\StyleEdit\Core\Components\Repeater\Entities\RepeaterOption;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 *
 */
class CarouselWidget extends AbstractWidget
{
    
    
    /**
     *
     */
    private const FIELD_CAPTION_TITLE = 'caption-title';
    
    /**
     *
     */
    private const FIELD_CAPTION_TAGLINE = 'caption-tagline';
    
    /**
     *
     */
    private const FIELD_ALT_TITLE = 'alt-title';
    
    /**
     *
     */
    private const FIELD_IMAGE = 'image';
    
    /**
     *
     */
    private const FIELD_IMAGE_LANDSCAPE = 'image-landscape';
    
    /**
     *
     */
    private const FIELD_IMAGE_PORTRAIT = 'image-portrait';
    
    /**
     *
     */
    private const FIELD_IMAGE_SMARTPHONE = 'image-smartphone';
    
    /**
     *
     */
    private const SLIDE_LINK = 'slide-link';
    
    /**
     *
     */
    private const SLIDE_LINK_TARGET = 'slide-link-target';
    
    /**
     * @var string
     */
    protected string $class;
    
    /**
     * @var string
     */
    protected string $slideInterval;
    
    /**
     * @var CheckboxOption
     */
    protected CheckboxOption $showIndicators;
    
    /**
     * @var CheckboxOption
     */
    protected CheckboxOption $showControls;
    
    /**
     * @var CheckboxOption
     */
    protected CheckboxOption $fadeEffect;
    
    /**
     * @var CheckboxOption
     */
    protected CheckboxOption $infiniteLoop;
    
    /**
     * @var CheckboxOption
     */
    protected CheckboxOption $pauseOnHover;
    
    
    /**
     * @var RepeaterOption
     */
    protected RepeaterOption $repeater;
    
    
    /**
     * @inheritDoc
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        if (!$this->carouselHasImages($currentLanguage)) {
            return '';
        }
        
        $carouselClasses = implode(' ',
                                   array_filter([
                                                    'carousel slide',
                                                    $this->class,
                                                ]));
        // Add fade effect class
        if ($this->fadeEffect->value()) {
            $carouselClasses .= ' carousel-fade';
        }
        
        $pauseOnHover = $this->pauseOnHover->value() ? 'hover' : 'false';
        $infiniteLoop = $this->infiniteLoop->value() === false ? ' data-wrap="false"' : '';
        
        $slideSpeed = ($this->slideInterval * 1000);
        $slideInterval = $this->slideInterval ? ' data-interval="' . $slideSpeed .'"' : '';
        
        return <<<HTML
        <div id="{$this->id->value()}" class="{$carouselClasses}" data-ride="carousel" data-pause="{$pauseOnHover}" {$slideInterval}{$infiniteLoop}>
          <!-- Indicators -->
          {$this->getIndicatorsHTML($currentLanguage)}
        
          <!-- Wrapper for slides -->
          {$this->getSlidesHTML($currentLanguage)}
        
          <!-- Controls -->
          {$this->getControlsHTML()}
        </div>
HTML;
    }
    
    /**
     * @inheritDoc
     */
    public function previewContent(?Language $currentLanguage): string
    {
        if ($this->carouselHasImages($currentLanguage)) {
            return $this->htmlContent($currentLanguage);
        }
        
        return '<div>' . '<h4 style="color: gray;">' . MainFactory::create(LanguageTextManager::class)->get_text(
                'preview.placeholder',
                'carouselWidget'
            ) . '</h4>' . '</div>';
    }
    
    /**
     * Check if at least one slide has main image
     *
     * @param Language|null $currentLanguage
     *
     * @return bool
     */
    private function carouselHasImages(?Language $currentLanguage): bool
    {
        $carouselSlidesWithImages = array_filter($this->repeater->value(),
            static fn($row) => $row->getValueByField(self::FIELD_IMAGE, $currentLanguage));
        
        return count($carouselSlidesWithImages) > 0;
    }
    
    
    /**
     * Returns the indicators HTML content.
     * If the option to not show the indicators is enabled, an empty string will be returned.
     *
     * @return string
     */
    private function getIndicatorsHTML(?Language $currentLanguage): string
    {
        if (!$this->showIndicators->value()) {
            return '';
        }
        
        $defaultActiveIndex = $this->getDefaultActiveIndex();
        $target             = "#{$this->id->value()}";
        $indicatorsHTML     = '';
        
        foreach ($this->repeater->value() as $index => $row) {
            $cssClass = $defaultActiveIndex === $index ? 'active' : '';
            $imagePath = $row->getValueByField(self::FIELD_IMAGE, $currentLanguage);
            
            $captionTitle   = $row->getValueByField(self::FIELD_CAPTION_TITLE, $currentLanguage);
            $altTitle = $row->getValueByField(self::FIELD_ALT_TITLE, $currentLanguage);
            $altTitle = $altTitle ? : $captionTitle;
            
            if(!$imagePath) {
                continue;
            }
            
            $indicatorsHTML .= <<<HTML
                <li data-target="{$target}" data-slide-to="{$index}" class="{$cssClass}" aria-label="{$altTitle}"></li>
            HTML;
        }
        
        if (empty($indicatorsHTML)) {
            return '';
        }
        
        return <<<HTML
          <ol class="carousel-indicators">
            {$indicatorsHTML}
          </ol>
        HTML;
    }
    
    
    /**
     * Returns the slides HTML content.
     *
     * @param Language|null $language
     *
     * @return string
     */
    private function getSlidesHTML(?Language $language): string
    {
        $shopUrl = SingletonPrototype::instance()->get(ShopBasePath::class)->value();
        $slidesHTML         = '';
        $defaultActiveIndex = $this->getDefaultActiveIndex();
        
        foreach ($this->repeater->value() as $index => $row) {
            $cssClass  = $defaultActiveIndex === $index ? 'active' : '';
            $imagePath = $row->getValueByField(self::FIELD_IMAGE, $language);
            $imageInfo = getimagesize($shopUrl . $imagePath);
            $imageSize = $imageInfo ? $imageInfo[3] : '';
            
            $captionTitle   = $row->getValueByField(self::FIELD_CAPTION_TITLE, $language);
            $captionTagline = $row->getValueByField(self::FIELD_CAPTION_TAGLINE, $language);
            
            $altTitle = $row->getValueByField(self::FIELD_ALT_TITLE, $language);
            $altTitle = $altTitle ? : $captionTitle;
            
            $slideLink = $row->getValueByField(self::SLIDE_LINK, $language);
            $slideLinkTarget = $row->getValueByField(self::SLIDE_LINK_TARGET);
            
            $slideTag = $slideLink ? 'a' : 'div';
            $linkAttributes  = $slideLink ? ' href="'. $slideLink .'"' : '';
            $linkAttributes .= $slideLink ? ' target="'. $slideLinkTarget .'"' : '';
            
            $imageLandscapePath   = $row->getValueByField(self::FIELD_IMAGE_LANDSCAPE, $language);
            $imagePortraitPath    = $row->getValueByField(self::FIELD_IMAGE_PORTRAIT, $language);
            $imageSmartphonePath  = $row->getValueByField(self::FIELD_IMAGE_SMARTPHONE, $language);
            
            if(!$imagePath) {
                continue;
            }
            
            $slidesHTML .= <<<HTML
            <{$slideTag}{$linkAttributes} class="item {$cssClass}">
                <picture>
                  {$this->getSlideResponsiveImages($imageLandscapePath, $imagePortraitPath, $imageSmartphonePath)}
                  <img
                      class="img-responsive"
                      loading="lazy"
                      aria-hidden="true"
                      focusable="false"
                      src="{$imagePath}"
                      alt="{$altTitle}"
                      {$imageSize}
                  />
                </picture>
              {$this->getCaptionHTML($captionTitle, $captionTagline)}
            </{$slideTag}>
            HTML;
        }
        
        return <<<HTML
            <div class="carousel-inner">
                {$slidesHTML}
            </div>
        HTML;
    }
    
    
    /**
     * Returns the HTML content of the Carousel control.
     * If the option to not show the controls is enabled, an empty string will be returned.
     *
     * @return string
     * @todo Implement the option to not show the controls
     */
    private function getControlsHTML(): string
    {
        if (!$this->showControls->value()) {
            return '';
        }
        
        $leftClasses = 'left carousel-control';
        $rightClasses = 'right carousel-control';
        
        // Disable arrows if infinite loop option is disabled
        if($this->infiniteLoop->value() === false) {
            $activeIndex = $this->getDefaultActiveIndex();
            $slidesCounter = count($this->repeater->value());
            
            if($activeIndex === 0) {
                $leftClasses .= ' disabled';
            }
            
            if($activeIndex === ($slidesCounter - 1)) {
                $rightClasses .= ' disabled';
            }
        }
        
        return <<<HTML
          <a class="{$leftClasses}" href="#{$this->id->value()}" role="button" data-slide="prev">
            <span class="icon-prev" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="{$rightClasses}" href="#{$this->id->value()}" role="button" data-slide="next">
            <span class="icon-next" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
HTML;
    }
    
    
    /**
     * Returns the caption content HTML.
     * If there is no caption title and caption tagline, an empty string will be returned.
     *
     * @param string $captionTitle
     * @param string $captionTagline
     *
     * @return string
     */
    private function getCaptionHTML(string $captionTitle, string $captionTagline): string
    {
        if (!$captionTitle && !$captionTagline) {
            return '';
        }
        
        $captionTitleHTML   = $captionTitle ? "<h3>{$captionTitle}</h3>" : '';
        $captionTaglineHTML = $captionTagline ? "<p>{$captionTagline}</p>" : '';
        
        return <<<HTML
        <div class="carousel-caption">
            {$captionTitleHTML}
            {$captionTaglineHTML}
        </div>
        HTML;
    }
    
    
    /**
     * Returns the responsive images HTML (<source>).
     * If there is no images selected, an empty string will be returned.
     *
     * @param string $imageLandscapePath
     * @param string $imagePortraitPath
     * @param string $imageSmartphonePath
     *
     * @return string
     */
    private function getSlideResponsiveImages(string $imageLandscapePath, string $imagePortraitPath, string $imageSmartphonePath): string {
        $images  = $imageSmartphonePath ? '<source media="(max-width: 480px)" srcset="'. $imageSmartphonePath .'" />' : '';
        $images .= $imagePortraitPath ? '<source media="(max-width: 768px)" srcset="'. $imagePortraitPath .'" />' : '';
        $images .= $imageLandscapePath ? '<source media="(max-width: 1024px)" srcset="'. $imageLandscapePath .'" />' : '';
        
        return $images;
    }
    
    
    /**
     * As the `getValue()` throws an `InvalidArgumentException` if the value is not found, we set the index to zero
     *
     * @return int
     */
    private function getDefaultActiveIndex(): int
    {
        try {
            return (int)$this->repeater->attributes()->getValue('defaultActiveIndex')->value();
        } catch (InvalidArgumentException $e) {
            return 0;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $result                 = $this->jsonObject;
        $result->id             = $this->static_id;
        $result->class          = $this->class;
        $result->showIndicators = $this->showIndicators;
        $result->showControls   = $this->showControls;
        $result->fadeEffect     = $this->fadeEffect;
        $result->type           = 'carousel';
        
        return $result;
    }
}