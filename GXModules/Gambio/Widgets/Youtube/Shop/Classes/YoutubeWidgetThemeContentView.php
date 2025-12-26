<?php
/*--------------------------------------------------------------
   YoutubeWidgetThemeContentView.php 2023-06-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);


/**
 * Class YoutubeWidgetThemeContentView
 *
 * @package GXModules\Gambio\Widgets\Youtube\Shop\Classes
 */
class YoutubeWidgetThemeContentView extends ThemeContentView
{
    /**
     * GoogleMapsWidgetThemeContentView constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $tpl_dir = DIR_FS_CATALOG . 'GXModules/Gambio/Widgets/Youtube/Shop/Html';
        $this->set_template_dir($tpl_dir);
        
        $this->set_content_data('tpl_dir', "{$tpl_dir}/");
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
    }
    
    
    /**
     * @return void
     */
    public function setToPreviewTemplate(): void
    {
        $this->set_content_template('youtube_widget.html');
    }
    
    
    /**
     * @return void
     */
    public function setToOilJsTemplate(): void
    {
        $this->set_content_template('youtube_oil_widget.html');
        $message = LanguageTextManager::get_instance()->get_text('not_consented_to_youtube', 'youtubeWidget');
        $message = sprintf($message, 'href="javascript:;" trigger-cookie-consent-panel=""');
        $this->set_content_data('NOCONSENTMESSAGE', $message);
        $this->set_content_data('frameId', spl_object_id($this));
    }
    
    /**
     * @param int $purposeId
     */
    public function setPurposeId(int $purposeId): void
    {
        $this->set_content_data('purposeId', $purposeId);
    }
}