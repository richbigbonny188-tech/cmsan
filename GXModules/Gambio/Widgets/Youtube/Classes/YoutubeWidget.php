<?php
/*--------------------------------------------------------------
   YoutubeWidget.php 2023-09-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

use Gambio\CookieConsentPanel\Services\Purposes\Factories\PurposeReaderServiceFactory;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeReaderServiceInterface;
use Gambio\StyleEdit\Core\Components\Checkbox\Entities\CheckboxOption;
use Gambio\StyleEdit\Core\Components\DropdownSelect\Entities\DropdownSelectOption;
use Gambio\StyleEdit\Core\Components\NumberBox\Entities\NumberBoxOption;
use Gambio\StyleEdit\Core\Components\TextBox\Entities\TextBox;
use Gambio\StyleEdit\Core\Language\Entities\Language;
use Gambio\StyleEdit\Core\Language\Services\LanguageService;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\Core\Widgets\Abstractions\AbstractWidget;

/**
 * Class YoutubeWidget
 */
class YoutubeWidget extends AbstractWidget
{
    public const RESPONSIVENESS_CUSTOM  = 'custom';
    public const RESPONSIVENESS_16_BY_9 = '16by9';
    public const RESPONSIVENESS_4_BY_3  = '4by3';
    
    /**
     * @var PurposeReaderServiceInterface
     */
    private PurposeReaderServiceInterface $purposeReader;
    
    
    /**
     * @var TextBox
     */
    protected TextBox $url;
    
    
    /**
     * @var NumberBoxOption
     */
    protected NumberBoxOption $width;
    
    
    /**
     * @var NumberBoxOption
     */
    protected NumberBoxOption $height;
    
    
    /**
     * @var TextBox
     */
    protected TextBox $title;
    
    
    /**
     * @var CheckboxOption
     */
    protected CheckboxOption $showPlayerControls;
    
    
    /**
     * @var DropdownSelectOption
     */
    protected DropdownSelectOption $responsiveness;
    
    
    /**
     * @var LanguageService
     */
    private LanguageService $languageService;
    
    
    /**
     * @param string   $static_id
     * @param array    $fieldsets
     * @param stdClass $jsonObject
     *
     * @throws Exception
     */
    public function __construct(string $static_id, array $fieldsets, stdClass $jsonObject)
    {
        parent::__construct($static_id, $fieldsets, $jsonObject);
        $this->purposeReader = (new PurposeReaderServiceFactory)->service();
        $this->languageService = SingletonPrototype::instance()->get(LanguageService::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function htmlContent(?Language $currentLanguage): string
    {
        $value = $this->url->value($currentLanguage);
        if ($value === '') {
            return $value;
        }
        
        $videoId            = $this->getVideoId($currentLanguage);
        $title              = $this->title->value($currentLanguage);
        $responsiveness     = $this->responsiveness->value();
        $showPlayerControls = $this->showPlayerControls->value() ? '1' : '0;';
        
        return "\n{youtube_widget id='{$this->id->value()}'" . " id='$videoId' title='$title'"
                . " responsiveness='$responsiveness'" . " showPlayerControls='$showPlayerControls'"
                . " width='{$this->width->value()}'" . " height='{$this->height->value()}'}";
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function previewContent(?Language $currentLanguage): string
    {
        $value = $this->url->value($currentLanguage);
        if ($value === '') {
            return $this->languageService->translate('youtubeWidget.options.text.no_url_provided');
        }
        
        return $this->generateIFrame($currentLanguage);
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string
     */
    private function generateIFrame(?Language $currentLanguage): string
    {
        if (($videoId = $this->getVideoId($currentLanguage)) === null) {
            return '';
        }
        
        $width          = $this->width->value() === '' ? 'auto' : $this->width->value();
        $height         = $this->height->value() === '' ? 'auto' : $this->height->value();
        $title          = $this->title->value($currentLanguage);
        $responsiveness = $this->responsiveness->value();
        $showControls   = $this->showPlayerControls->value();
        $command        = new YoutubeWidgetOutputCommand($this->purposeReader,
                                                         $videoId,
                                                         $width,
                                                         $height,
                                                         $title,
                                                         $responsiveness,
                                                         $showControls);
        
        return $command->execute(true);
    }
    
    
    /**
     * @param Language|null $currentLanguage
     *
     * @return string|null
     */
    private function getVideoId(?Language $currentLanguage): ?string
    {
        $pattern = '/youtu.be\/([^?\n&]+)|watch\?v=([^?\n&]+)/m';
        preg_match_all($pattern, $this->url->value($currentLanguage), $matches, PREG_SET_ORDER, 0);
        
        if (empty($matches)) {
            return null;
        }
        
        return array_pop($matches[0]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $result            = $this->jsonObject;
        $result->fieldsets = $this->fieldsets;
        $result->type      = $result->id;
        
        return $result;
    }
}