<?php
/*--------------------------------------------------------------
   YoutubeWidgetOutputCommand.php 2023-08-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeInterface;
use Gambio\CookieConsentPanel\Services\Purposes\Interfaces\PurposeReaderServiceInterface;

/**
 * Class YoutubeWidgetOutputCommand
 *
 * @package GXModules\Gambio\Widgets\Youtube\Classes
 */
class YoutubeWidgetOutputCommand
{
    public function __construct(
        private PurposeReaderServiceInterface $purposeReadService,
        private string                        $videoId,
        private string                        $width,
        private string                        $height,
        private                               $title,
        private string                        $responsiveness,
        private bool                          $showPlayerControls
    ) {
    }
    
    
    /**
     * @param YoutubeWidgetThemeContentView $contentView
     *
     * @return bool
     */
    private function setPurposeId(YoutubeWidgetThemeContentView $contentView): bool
    {
        $purposes = $this->purposeReadService->allPurposes();
        $callback = static fn(PurposeInterface $purpose): bool => $purpose->alias()->value() === 'gambio/youtube';
        $purposes = array_filter($purposes, $callback);
        
        if (count($purposes) !== 1) {
            return false;
        }
        /** @var PurposeInterface $purpose */
        $purpose = array_values($purposes)[0];
        
        if ($purpose->status()->isActive() === false) {
            return false;
        }
        
        $contentView->setPurposeId($purpose->id()->value());
        
        return true;
    }
    
    
    /**
     * @param bool $isPreview
     *
     * @return string
     */
    public function execute(bool $isPreview = false): string
    {
        $contentView = MainFactory::create(YoutubeWidgetThemeContentView::class);
        
        if ($isPreview === false && $this->cookieConsentInstalled() && $this->setPurposeId($contentView)) {
            $contentView->setToOilJsTemplate();
        } else {
            $contentView->setToPreviewTemplate();
        }
        $isResponsive = $this->isResponsive();
        $videoId      = !$this->showPlayerControls ? $this->videoId . '?controls=0' : $this->videoId;
        $contentView->set_content_data('width', $isResponsive ? '100%' : $this->width);
        $contentView->set_content_data('height', $isResponsive ? '100%' : $this->height);
        $contentView->set_content_data('videoId', $videoId);
        $contentView->set_content_data('title', $this->title);
        
        if ($this->responsiveness === YoutubeWidget::RESPONSIVENESS_16_BY_9) {
            $contentView->set_content_data('responsive16by9', true);
        } elseif ($this->responsiveness === YoutubeWidget::RESPONSIVENESS_4_BY_3) {
            $contentView->set_content_data('responsive4by3', true);
        }
        
        return $contentView->get_html();
    }
    
    
    /**
     * @return bool
     */
    private function isResponsive(): bool
    {
        return $this->responsiveness === YoutubeWidget::RESPONSIVENESS_16_BY_9
               || $this->responsiveness === YoutubeWidget::RESPONSIVENESS_4_BY_3;
    }
    
    
    /**
     * @return bool
     */
    private function cookieConsentInstalled(): bool
    {
        return gm_get_conf('MODULE_CENTER_GAMBIOCOOKIECONSENTPANEL_INSTALLED') !== '0';
    }
}